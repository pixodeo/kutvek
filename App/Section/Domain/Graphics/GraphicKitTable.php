<?php 
namespace App\Section\Domain\Graphics;

use Core\Model\Table;

class GraphicKitTable extends Table {	
	private array $_filters = [];
    private array $_items = [];

	public function setFilters($filters){
	    $this->_filters = $filters;
	}

    public function items(int $category, int|false $store):array {
        if(!$store) return [];
        $params = ['store'=>$store, 'dpt' => $category];
        $sql ="SELECT i_s.item AS 'id'
        FROM item_stores i_s
        LEFT JOIN items i ON i.id = i_s.item
        LEFT JOIN graphic_kits AS p ON p.id = i_s.item       
        JOIN vehicle AS v ON v.id = p.vehicle 
        LEFT JOIN designs_orders AS d_o ON (d_o.d_store = i_s.store AND d_o.design = p.design AND d_o.brand = v.brand)
        WHERE i_s.store = :store
        AND i_s.status = 1
        AND i.department = :dpt
        AND (CASE WHEN i_s.store = 9 THEN p.kit_type IS NULL OR p.kit_type = 2 ELSE 1 = 1 END) ";       
        if($this->_filters){
            foreach($this->_filters as $filter => $value):                   
                    $filters = is_string($value) ? explode(',', $value) : [$value];               
                    $plh = $this->namedPlaceHolder2($filters, $filter);
                    $params = array_merge($params, $plh->values);
                    // comment on fait pour la marque / brand ? qui peut être celle du vehicule ou celle du produit 
                    if ($filter == 'family'):
                        $sql .= " AND  v.universe IN ({$plh->place_holder}) ";            
                    elseif($filter == 'brand'):
                        $sql .= " AND v.brand  IN({$plh->place_holder}) ";
                    elseif($filter == 'model'):
                        $sql .= " AND v.model  IN({$plh->place_holder}) ";                    
                    else:
                        $sql .= " AND p.{$filter} IN({$plh->place_holder}) ";
                    endif;
                               
            endforeach;
        } 
        $sql .= " ORDER BY d_o._order IS NULL, d_o._order ASC, p.id DESC";

        $query = $this->setFetchMode(2)->query($sql, $params);
        $this->setFetchMode(false);
        $this->_items = array_column($query, 'id');
        return $this->_items;

    } 

    public function getVehicles(){
        if(count($this->_items) === 0) return [];
        $this->setEntity(null);
        $p_h = $this->namedPlaceHolder($this->_items);
        $params = array_merge(['l10n' => $this->getL10nId()], $p_h->values);

        $sql = "SELECT v.id, v.name 
        FROM graphic_kits p 
        LEFT JOIN  vue_vehicle_2 v ON v.id = p.vehicle AND v.l10n = :l10n
        WHERE p.id IN({$p_h->place_holder})
        GROUP BY v.id
        ORDER BY v.name;";
        
        return $this->query($sql, $params);
    }  

  
    public function cards(array $items): array
    {
        if(count($items) === 0) return [];
        $this->setEntity('Card');
        $p_h = $this->namedPlaceHolder($items);
        $params = array_merge(['l10n' => $this->getL10nId(), 'cur' => $this->getCurrency(), 'pro' => 1, 'country' => $this->getCountry()], $p_h->values);
        //$this->setEntity('GraphicKitCard');
        $sql = "SELECT 
        i.id, 
        i.parent,
        i.weight, 
        p.design AS 'design_id',
        p.color AS 'color_id',
        p.kit_type,
        k_type.title AS 'type_designation',      
        (select 
            CASE WHEN vp.currency = 3 THEN (vp.price * 1.20) ELSE vp.price END AS 'price'
            from vehicle_price_currencies  vp
            where (vp.vehicle = p.vehicle)
            and vp.currency = cur.currency_id
            and vp.valid_since <= current_timestamp() 
            and (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until) 
            and vp.template_type = p.kit_type
            ORDER BY vp.id DESC 
            LIMIT 1) AS 'price', 
        (select 
            CASE WHEN p.kit_type IS NULL OR p.kit_type = 2 THEN  price_currency.price
            WHEN p.kit_type = 3 THEN  price_currency.full
            WHEN p.kit_type = 5 THEN  price_currency.mega
            WHEN p.kit_type = 1 THEN  price_currency.light
            END
            from price_currency
            where (price_currency.price_id = v.price)
            and price_currency.currency = cur.currency_id
            and price_currency.valid_since <= current_timestamp() 
            and (price_currency.valid_until IS NULL OR current_timestamp() <= price_currency.valid_until) 
            ORDER BY price_currency.id DESC 
            LIMIT 1
        ) AS 'price_2',        
        (SELECT  JSON_OBJECT(
            'min', CASE WHEN pc.currency= 3 THEN MIN(pc.price)*1.20 ELSE MIN(pc.price) END, 
            'max', CASE WHEN pc.currency= 3 THEN MAX(pc.price)*1.20 ELSE MAX(pc.price) END,
            'brand', v.brand,
            'fam', v.universe)
            FROM vehicle v2
            LEFT JOIN price_currency pc ON pc.price_id = v2.price
            WHERE v2.`universe` = v.universe AND v2.`brand` = v.brand
            AND pc.currency = cur.currency_id
            and pc.valid_since <= current_timestamp() 
            and (pc.valid_until IS NULL OR current_timestamp() <= pc.valid_until) 
            ORDER BY pc.valid_since DESC
        ) AS 'prices',
        (select 
            JSON_OBJECT(
                'type', offer.offer_type, 
                'discount', offer.discount, 
                'designation', offer.designation,
                'img', CONCAT('/img/pictos/', CONCAT_WS('-', offer.designation, offer.discount, intl.iso_639_1), '.png')
            )
            from designs_sales AS offer
            where offer.design = p.design
            and offer.currency = cur.currency_id
            and offer.valid_since <= current_timestamp() 
            and (offer.valid_until IS NULL OR current_timestamp() <= offer.valid_until) 
            ORDER BY offer.design DESC 
            LIMIT 1
        ) AS 'promo', 
        :pro AS 'is_pro', 
        v.fam_name, 
        CONCAT_WS(' ', v.brand_name, v.name) AS 'vehicle_name',      
        v.brand_name,  
        v.model_name,
        v.name,        
        v.version AS 'vehicle_version', 
        v.finish AS 'vehicle_finish',
        v.id AS 'vehicle_id',        
        CONCAT_WS('/', 'https://www.kutvek-kitgraphik.com/images/produits/original', p.produit_visuel) AS 'old_visual',        
        CONCAT_WS(' ', d.name, d.season) AS 'design_name',
        colour.designation AS 'color_name',
        cur.currency_lib AS 'currency_code',
        country.vat,
        l10n._locale,
        slug.slug,      
        CASE            
            WHEN (p_url.produit_id IS NOT NULL AND p_url.prefix = 'en-fr') THEN CONCAT(CONCAT_WS('/', 'en', p_url.url ), '.html') 
            WHEN (p_url.produit_id IS NOT NULL AND p_url.prefix = 'fr-fr') THEN CONCAT(p_url.url , '.html')
            WHEN p_url.produit_id IS NOT NULL THEN CONCAT(CONCAT_WS('/', p_url.prefix, p_url.url ), '.html') 
            ELSE NULL 
        END AS 'old_url',
        (SELECT  df_content.full_designation
        FROM items i2        
        LEFT JOIN category_default_content df_content ON 
        (df_content.category = (
        SELECT c.id 
        FROM categories node
        JOIN categories c ON (c.node_left <= node.node_left AND c.node_right >= node.node_right AND c.workspace = node.workspace)
        WHERE EXISTS (
            SELECT category
            FROM  category_default_content
            WHERE category_default_content.category = c.id
        )
        AND node.id = (CASE WHEN i2.department IS NOT NULL THEN i2.department ELSE i2.category END)
        ORDER BY c.node_left 
        LIMIT 1)
        AND df_content.l10n = l10n.id
        )
        WHERE i2.id = i.id
        ) AS 'designation'
        FROM graphic_kits p
        JOIN items i  ON i.id = p.id                    
        #LEFT JOIN products AS parent ON parent.id = p.parent
        JOIN l10ns AS l10n ON l10n.id = :l10n  
        LEFT JOIN internationalisation intl ON intl.id = l10n.i18n    
        LEFT JOIN product_urls AS p_url ON (p_url.produit_id = p.id_produit AND p_url.l10n = l10n.id)
        JOIN vue_vehicle_2 AS v ON (v.id = p.vehicle  AND v.l10n = l10n.id)        
        LEFT JOIN currency cur ON (cur.currency_lib = :cur)
        LEFT JOIN country ON country.country_iso = :country        
        LEFT JOIN item_l10ns trad ON (trad.item = p.id AND trad.l10n = l10n.id)              
        LEFT JOIN produits_prix prix ON (prix.id_produit = p.id_produit AND prix.currency = cur.currency_id)
        LEFT JOIN designs AS d ON d.id = p.design        
        LEFT JOIN color_l10ns AS colour ON (colour.color = p.color AND colour.l10n = l10n.id)
        LEFT JOIN slugs AS slug ON (slug.category = i.category AND slug.l10n = l10n.id)     
        LEFT JOIN graphic_kit_types AS k_type  ON k_type.id = p.kit_type
        WHERE p.id IN({$p_h->place_holder}) 
        ORDER BY p.id DESC;";
        $cards = $this->query($sql, $params);
        if (count($cards) > 0) {
            foreach ($cards as $card) {
                $card->visual = $this->cardVisual($card);
            }
        }
        return $cards;
    }

    public function cardVisual(object $card) {
        // 1 on tente de récupérer le fichier dans la bdd product_files; sin a a rien on tente d'envoyer l'ancien fichier après avoir vérifié que celi-ci est bien présent sur le serveur demandé (HTTP 200)
        $visual = false;        
        $this->setEntity(null);
        
        $sql = "SELECT f.url
        FROM product_files AS p_f 
        JOIN files AS f ON (f.id = p_f.file)
        WHERE p_f.product = :id
        AND p_f.cover = 1;";
        $visual=  $this->query($sql, [':id' => $card->id], true);

        if($visual) return $visual->url;
        // Reste à vérifier si visuel existe via CURL
        else return $card->old_visual ?? '/img/blank.png';
    }

    public function getFilterData(string $filter){
        $data = match ($filter) {
            'design' => $this->designs(),    
            'color' => $this->colors(),                             
        };
        return $data;
    }
    
    /**
     * Retourne tous les designs d'une catégorie de kits déco
     *
     * @return     array  ( description_of_the_return_value )
     */
    public function designs(): array {
        $sql = "SELECT 
            CONCAT_WS('', d.name, d.season) as 'name',
            'design' AS '_type',
            d.id 
            FROM graphic_kits g 
            LEFT JOIN items i ON i.id = g.id
            LEFT JOIN designs d ON d.id = g.design             
            WHERE  i.department = :category
            GROUP BY d.id
            ORDER BY CONCAT_WS('', d.name, d.season);
        ";
        return $this->query($sql, ['category'=> $this->_filters['category']]);
    }

     /**
     * Retourne tous les coloris d'une catégorie de kits déco
     *
     * @return     array  ( description_of_the_return_value )
     */
    public function colors(): array {
        $sql = "SELECT 
            c.designation as 'name',            
            c.color  AS 'id'
            FROM graphic_kits g 
            LEFT JOIN items i ON i.id = g.id
            LEFT JOIN color_l10ns c ON c.color = g.color            
            WHERE  i.department = :category
            AND c.l10n = :l10n
            GROUP BY c.color
            ORDER BY c.designation;
        ";
        return $this->query($sql, ['category'=> $this->_filters['category'], 'l10n' => $this->getL10nId()]);
    }

    public function filters(int $store_id, ?int $category, ?int $brand_id) {        
        $sql = "SELECT       
        fam_l10n.family AS 'fam_id',
        LOWER(fam_l10n.name) AS 'fam_designation',
        brand.id AS 'brand_id',
        LOWER(brand.name) AS 'brand_designation',
        p.vehicle AS 'vehicle_id',
        UPPER(v_v.v_name) AS 'vehicle',       
        p.design AS 'design_id',
        LOWER(CONCAT_WS(' ',d.name, d.season)) AS 'design',
        p.color AS 'color_id',
        CASE WHEN p.color = 115 THEN 'Replica' ELSE REPLACE(LOWER(JSON_VALUE(c.attr, CONCAT('$.i18n.', intl.iso_639_1))), '-', '/') END AS `color`,
        l10n.name
        FROM item_stores i_s
        LEFT JOIN items i  ON i.id = i_s.item 
        LEFT JOIN graphic_kits AS p ON p.id = i.id
        LEFT JOIN vue_vehicle AS v_v ON v_v.v_id = p.vehicle
        JOIN l10ns AS l10n ON l10n.url = :l10n
        JOIN internationalisation AS intl ON intl.id = l10n.i18n
        LEFT JOIN vue_brand AS brand ON (
            brand.id =
            CASE 
               WHEN v_v.brand_2 IS NOT NULL THEN v_v.brand_2               
               ELSE 0
            END
        )
        LEFT JOIN families_i18n AS fam_l10n ON (
            fam_l10n.family = 
                CASE 
                   WHEN v_v.universe IS NOT NULL THEN v_v.universe                   
                   ELSE 0
                END
            AND fam_l10n.l10n = l10n.id
        )
        LEFT JOIN color_ref AS c ON c.color_ref_id = p.color
        LEFT JOIN designs AS d ON d.id = p.design
        WHERE i_s.store = :store_id
        AND (CASE WHEN i_s.store = 9 THEN p.kit_type IS NULL OR p.kit_type = 2 ELSE 1 = 1 END) 
        AND i_s.status = 1";
        if($category !== null) $sql .= " AND i.category =  :category "; 
        if($brand_id !== null) $sql .= " AND brand.id =  :brand_id ";  

        $params = array_filter([':store_id' => $store_id, ':category' => $category, ':brand_id' => $brand_id, ':l10n' => $this->getL10nUrl()]); 

        return $this->query($sql, $params);
    }

    public function files(int $id, ?int $parent = null ): array
    {
        $this->setEntity('File');
        if($parent !== null):
            $sql = "SELECT f.id, 
            f.url, 
            f.product, p_f.id AS 'pf_id', 
            p_f.product AS 'associate', 
            p_f.cover, 
            p_f.position
            FROM files AS f
            LEFT JOIN  product_files AS p_f ON (p_f.file = f.id AND p_f.product = :id)
            WHERE f.product = :pid OR f.product = :parent
            ORDER BY p_f.id IS NULL, p_f.position IS NOT NULL DESC, p_f.position ASC;";
            return $this->query($sql, [':parent' => $parent, ':id' => $id, ':pid' => $id], false, true);
        else:
            $sql = "SELECT f.id, 
            f.url, 
            f.product, 
            p_f.id AS 'pf_id', 
            p_f.product AS 'associate', 
            p_f.cover, 
            p_f.position        
            FROM product_files AS p_f 
            LEFT JOIN files AS f ON (f.id = p_f.file)
            WHERE p_f.product = :id
            ORDER BY p_f.id IS NULL, p_f.position IS NOT NULL DESC, p_f.position ASC;";
            return $this->query($sql, [':id' => $id]);    
        endif;    
    }

    public function linkExternalFiles(int $id) {
        $sql = " SELECT JSON_ARRAY (
            CASE WHEN p.produit_visuel IS NOT NULL THEN
            JSON_OBJECT('url',  CONCAT('https://www.kutvek-kitgraphik.com/images/produits/original/', p.produit_visuel),
                'position', 1,
                'cover', 1,
                'product', p.id) ELSE NULL END,
            CASE WHEN p.produit_maquette IS NOT NULL THEN
            JSON_OBJECT('url',  CONCAT('https://www.kutvek-kitgraphik.com/images/produits/original/', p.produit_maquette),
                'position', 2,
                'cover', 0,
                'product', p.id) ELSE NULL END
            ) AS 'files'
        FROM graphic_kits AS p
        WHERE p.id = :id;";
        return $this->query($sql, [':id' => $id], true);
    }

    public function millesims(int $vehicle)
    {
        $sql = "SELECT vm.id , LPAD(vm.id, 5, '0') AS 'value',
        CASE
            WHEN vm.finish = '-' AND YEAR(DATE_ADD(NOW(), INTERVAL +5 MONTH)) = vm.begin THEN vm.begin
            WHEN vm.finish = '-' THEN CONCAT_WS('-', vm.begin, YEAR(DATE_ADD(NOW(), INTERVAL +5 MONTH))) 
            WHEN vm.finish IS NULL THEN vm.begin
            ELSE CONCAT_WS('-', vm.begin, vm.finish)
        END AS 'text'
        FROM vehicle_millesims AS vm
        WHERE vm.vehicle = :vehicle_id
        AND vm.type = 'fairing'
        ORDER BY vm.begin DESC";
        $millesims = $this->query($sql, [':vehicle_id' => $vehicle]);
        
        // récupérer les types de kit disponibles pour tous les gabarits
        return $millesims;
    }

    public function finish(int $family = 1)
    {
        $this->setEntity('OptionFinish');        
        $sql = "SELECT
        _i18n.name AS 'text',
        _i18n.name,
        opt.id, 
        CASE WHEN pc.currency IN (3,4) THEN pc.price*1.20 ELSE pc.price END AS 'value',
        CASE WHEN pc.currency IN (3,4) THEN pc.price*1.20 ELSE pc.price END AS 'price',  
        cur.currency_lib AS 'currency',
        l10ns.name AS 'l10n',
        country.vat
        FROM options AS opt
        LEFT JOIN options_i18n AS _i18n ON _i18n.option = opt.id
        LEFT JOIN l10ns ON l10ns.id = _i18n.l10n
        LEFT JOIN currency AS cur ON cur.currency_lib = :currency
        LEFT JOIN option_prices AS pc ON (pc.option = opt.id AND pc.currency = cur.currency_id)
        LEFT JOIN country ON country.country_iso = :country
        WHERE l10ns.id = :l10n
        AND opt.parent = 2
        AND opt.opt_type = 'finish'
        AND pc.id = (SELECT
                option_prices.id
            FROM
                option_prices
            WHERE
                option_prices.option = pc.option
                AND option_prices.currency = pc.currency
                AND option_prices.valid_since <= current_timestamp()
                AND (
                    option_prices.valid_until IS NULL
                    OR option_prices.valid_until >= current_timestamp()
                )
                AND JSON_CONTAINS(option_prices.universes, :family, '$') = 1
                AND option_prices.kit_type = 2
                ORDER BY option_prices.id 
                DESC LIMIT 1
        );";
        
        $params = array_filter(array('l10n' => $this->getL10nId(), 'currency'=> $this->_currency, 'country' => $this->getCountry(), 'family' => $family));
        return $this->query($sql, $params);
    }

    public function premium(int $family = 1) {
        $sql = "SELECT
        _i18n.name AS 'text',
        _i18n.name,
        opt.id AS 'option_id',        
        CASE WHEN pc.currency IN (3,4) THEN pc.price*1.20 ELSE pc.price END AS 'value',
        CASE WHEN pc.currency IN (3,4) THEN pc.price*1.20 ELSE pc.price END AS 'price', 
        cur.currency_lib AS 'currency',
        l10ns.name AS 'l10n',
        country.vat
        FROM options AS opt     
        LEFT JOIN options_i18n AS _i18n ON _i18n.option = opt.id 
        LEFT JOIN currency AS cur ON cur.currency_lib = :currency   
        LEFT JOIN option_prices AS pc ON (pc.option = opt.id AND pc.currency = cur.currency_id) 
        LEFT JOIN country ON country.country_iso = :country
        LEFT JOIN l10ns ON l10ns.id = _i18n.l10n
        WHERE opt.opt_type = 'premium'   
        AND _i18n.l10n = :l10n
        AND pc.id = (
        select max(option_prices.id) 
            from option_prices 
            where option_prices.option = pc.option
            and option_prices.currency = pc.currency
            and option_prices.valid_since <= current_timestamp() 
            and (option_prices.valid_until is null or option_prices.valid_until >= current_timestamp())
            AND option_prices.kit_type = 2
            AND JSON_CONTAINS(option_prices.universes, :family, '$') = 1
        )
        AND opt.full_custom = 0
        ORDER BY opt._order;";
        return $this->query($sql, array(':l10n' => $this->getL10nId(), ':currency'=> $this->_currency, ':country' => $this->getCountry(), 'family' => $family));
    }

    

    public function optionsInfos(array $options = [])
    {
        $this->setEntity('Option');
        if(count($options) === 0) return $options;
        $data = $this->namedPlaceHolder($options);
        $sql = "SELECT
        _i18n.name AS 'text',
        opt.id, 
        opt.sibling,
        opt.label_id,
        opt.input_name,
        opt.input_type,
        opt.modal,
        CASE WHEN pc.currency = 3 THEN pc.price*1.20 ELSE pc.price END AS 'value',
        CASE WHEN pc.currency = 3 THEN pc.price*1.20 ELSE pc.price END AS 'price', 
        cur.currency_lib AS 'currency',
        country.vat,
        l10ns.name AS 'l10n',
        JSON_VALUE(opt.attr, '$.picto') AS 'picto'
        FROM options AS opt
        LEFT JOIN options_i18n AS _i18n ON _i18n.option = opt.id
        LEFT JOIN l10ns ON l10ns.id = _i18n.l10n
        LEFT JOIN currency AS cur ON cur.currency_lib = :cur
        LEFT JOIN country ON country.country_iso = :country
        LEFT JOIN option_prices AS pc ON (pc.option = opt.id AND pc.currency = cur.currency_id)
        WHERE l10ns.id = :l10n
        AND opt.id IN ({$data->place_holder}) 
        AND pc.id = (
        select max(option_prices.id) 
        from option_prices 
        where option_prices.option = pc.option
        and option_prices.currency = pc.currency
        and option_prices.valid_since <= current_timestamp() 
        and (option_prices.valid_until is null or option_prices.valid_until >= current_timestamp())
        );";
        return $this->query($sql, array_merge($data->values, array('l10n' => $this->getL10nId(), 'cur'=> $this->_currency, 'country' => $this->getCountry())));
    }

    public function plastics(int $family)
    {
        $this->setEntity('OptionFinish');

        $sql = "SELECT
            o.id,
            o_i18n.name AS 'text',
            'plastics' AS 'name',
            CASE
                WHEN o_p.currency IN (3, 4) THEN o_p.price * 1.20
                ELSE o_p.price
            END AS 'value',
            CASE
                WHEN o_p.currency IN (3, 4) THEN o_p.price * 1.20
                ELSE o_p.price
            END AS 'price',
            cur.currency_lib AS 'currency',
            l10ns.name AS 'l10n',
            country.vat
        FROM
            options AS o
            LEFT JOIN options_i18n AS o_i18n ON o_i18n.option = o.id
            LEFT JOIN l10ns ON l10ns.id = o_i18n.l10n
            LEFT JOIN option_prices AS o_p ON o_p.option = o.id
            LEFT JOIN currency AS cur ON cur.currency_lib = :cur
            LEFT JOIN country ON country.country_iso = :country
        WHERE
            o.opt_type = 'plastics'
            AND l10ns.id = :l10n
            AND o_p.id = (
                SELECT
                    option_prices.id
                FROM
                    option_prices
                WHERE
                    option_prices.option = o_p.option
                    AND option_prices.currency = cur.currency_id
                    AND option_prices.valid_since <= current_timestamp()
                    AND (
                        option_prices.valid_until IS NULL
                        OR option_prices.valid_until >= current_timestamp()
                    )
                    AND JSON_CONTAINS(option_prices.universes, :family, '$') = 1
                    AND option_prices.kit_type = 2
                ORDER BY
                    option_prices.id DESC
                LIMIT
                    1
            );";

        return $this->query($sql, ['l10n' => $this->getL10nId(), 'cur' => $this->getCurrency(), 'family' => $family, 'country' => $this->getCountry()]);
    }    
}