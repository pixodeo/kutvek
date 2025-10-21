<?php
declare(strict_types=1);
namespace Domain\Table;
use Core\Domain\Table;

class Catalog extends Table  {
    protected int $_store = 1;    

    public function listOfProducts(int $id):array{
        $sql = "SELECT i.id
        FROM items i 
        JOIN item_stores i_s  ON (i_s.item = i.id AND i_s.status = 1)
        WHERE i.department IN (
            (SELECT c.id
            FROM categories p
            LEFT JOIN categories c ON (c.workspace = p.workspace AND c.node_left >= p.node_left AND c.node_right <= p.node_right)
            WHERE p.id = :id
            ORDER BY c.node_left)    
        )
        AND i_s.store =  :store
        ORDER BY i.id DESC;";
        $query = $this->query($sql, ['id'=>$id, 'store' => $this->_store]);
        return $query;
    } 

    public function itemsInSection(): array {
        $this->setEntity(null);
        $params = ['store' => $this->section->department_store];
        $plh_1 = $this->namedPlaceHolder($this->section->types, 't');       
        $plh_2 = $this->namedPlaceHolder($this->section->categories, 'c');
        $params = array_merge($params, $plh_1->values,$plh_2->values);              
        $sql = "SELECT i.id
            FROM items i
            JOIN item_stores i_s ON (i_s.item = i.id AND i_s.store = :store AND i_s.status = 1)
            WHERE  i.behavior IN ({$plh_1->place_holder})
            AND  i.department IN ({$plh_2->place_holder})
            ORDER BY i.id DESC;
        ";
        $items = $this->query($sql, $params);
        return array_column($items,'id');        
    }

    public function megamenu(): array
    {
        $sql = "SELECT m_i.id, 
        w_m.depth,
        w_m.node_left, 
        w_m.node_right,       
        w_m.parent,  
        m_i.display,
        m_i.active,
        m_i.position,
        m_i.menu,
        m_i.obfuscated,
        CASE WHEN w_m.node_right - w_m.node_left > 1 
            THEN (
                SELECT count(*) 
                FROM vue_menu_items AS m_i2 
                LEFT JOIN website_menus AS w_m2 ON (w_m2.id = m_i2.menu) 
                WHERE m_i2.l10n = m_i.l10n
                AND m_i2.website = m_i.website
                AND m_i2.display = 1 
                AND w_m2.node_left > w_m.node_left 
                AND w_m2.node_right < w_m.node_right
                ) 
            ELSE 0 
        END AS 'leafs',         
        CASE 
            WHEN m_i.page IS NOT NULL THEN page.slug 
            WHEN m_i.in_progress IS NOT NULL THEN in_progress.slug
            ELSE m_i.slug
        END AS 'slug',
        -- CASE 
        --     WHEN m_i.page IS NOT NULL THEN page.title
        --     WHEN m_i.in_progress IS NOT NULL THEN in_progress.title 
        -- ELSE m_i.name END AS 'name'
        m_i.name
        FROM website_menus w_m        
        LEFT JOIN l10ns AS l10n ON l10n.id = :l10n
        LEFT JOIN vue_menu_items  AS m_i ON (
            m_i.menu = w_m.id  
            AND m_i.l10n = l10n.id 
        )        
        LEFT JOIN  page_l10ns AS page ON (page.page = m_i.page AND page.l10n = l10n.id)   
        LEFT JOIN  in_progress_l10ns AS in_progress ON (in_progress.in_progress = m_i.in_progress AND in_progress.l10n = l10n.id) 
        WHERE w_m.website = :website
        AND m_i.menu_top IS NULL
        AND m_i.website = w_m.website
        AND m_i.display = 1    
        ORDER BY w_m.node_left ASC;";        
        return $this->query($sql, ['website' => WEBSITE_ID, 'l10n' => $this->getL10nId()]);
    } 

    public function countries(): array {
        $sql = "SELECT 
        c.id, 
        UPPER(c.country_iso) AS 'country_code', 
        CONCAT('/img/flags/1x1/', LOWER(c.country_iso),'.svg') AS 'flag', 
        CASE WHEN :l10n = 1 THEN c.name_fr ELSE c.name_en END AS 'country_name',
        CONCAT_WS(' ', cur.currency_symbol, cur.currency_lib) AS 'currency_name',
        cur.currency_lib AS 'currency_code',
        c.default_currency,
        cur.currency_lib,
        cur.currency_symbol
        FROM country AS c
        JOIN currency AS cur ON cur.currency_id = c.default_currency
        ORDER BY  c.preferences_position DESC, country_name";

        return $this->query($sql, ['l10n' => $this->getL10nId()]);
    }


    public function setStore(int $store):void{$this->_store = $store;}

    public function editorial(string $slug){
        $sql = "SELECT 
            CASE WHEN c_l10n.full_designation IS NOT NULL THEN c_l10n.full_designation ELSE c_l10n.designation END AS 'designation',
            c.department_store as 'd_store',
            c_l10n.short_desc,
            c_l10n.description,
            c_l10n.meta_title,
            c_l10n.meta_description,
            c_l10n.cover,
            c_l10n.portrait,
            c_l10n.category AS 'category_id',
            c_l10n.l10n AS 'l10n_id'
            FROM vue_slugs v_s
            LEFT JOIN categories c ON  c.id = v_s.id
            LEFT JOIN category_l10ns c_l10n ON (c_l10n.category = c.id  AND c_l10n.l10n = v_s.l10n)
            WHERE v_s.slug = :slug 
            AND v_s.l10n = :l10n
            AND v_s.workspace = 2
            AND v_s.slug_type = 'section';
        ";
        $editorial = $this->query($sql, ['slug' => $slug, 'l10n' => $this->getL10nId()], true);

        if($editorial):
            $editorial->l10ns = $this->slugs($editorial->category_id); 
        endif;  
        return $editorial;
    }

    public function slugs(int $category_id){
        $sql = "SELECT v_s.id AS 'category', 
        CONCAT_WS('/', l10ns._prefix, v_s.slug) AS 'slug', 
        v_s.l10n, 
        l10ns._locale,
        CASE WHEN v_s.l10n = :l10n THEN 'current' ELSE '' END AS 'class' 
        FROM vue_slugs v_s 
        JOIN l10ns ON l10ns.id = v_s.l10n
        WHERE v_s.id = :category AND v_s.slug_type = 'section';";
        return $this->query($sql, ['category'=> $category_id, 'l10n' => $this->getL10nId()]);
    }

    public function cards(array $slices = []):array{
        if(count($slices) < 1) return [];
        $this->setEntity('Card');
        $pl = $this->namedPlaceHolder2($slices);       
        $params = array_merge(['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n_id'=>$this->getL10nId()], $pl->values);        
        $sql = " SELECT i.id, 
            i.department, 
            i.weight, 
            i.license, 
            i.parent,  
            l10n.id AS 'l10n_id',
            l10n._locale,
            l10n._prefix,
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code', 
            i.behavior AS 'behavior_id',          
            b._type AS 'behavior_type',
            i_l10n.item AS 'has_content',
            CASE 
                WHEN LOCATE('.html', i_l10n.item_slug) > 0 THEN i_l10n.item_slug
                WHEN  i_l10n.item_slug IS NOT NULL THEN CONCAT_WS('-',i_l10n.item_slug, i.id) 
            END AS 'item_slug',
            JSON_OBJECT('price',ROUND(prices.price)) AS 'price',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            df.full_designation AS 'df_full_designation',
            f.url AS 'cover'                    
            FROM items i
            LEFT JOIN l10ns l10n ON l10n.id = :l10n_id 
            LEFT JOIN currency cur ON cur.currency_id = :cur                  
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)
            LEFT JOIN item_prices_2 prices ON (prices.id = i.id AND prices.currency_id = cur.currency_id)   
            LEFT JOIN category_default_content df ON (
                df.l10n = l10n.id 
                AND df.category = (
                    SELECT c.id
                    FROM categories node 
                    LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
                    LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = l10n.id)
                    WHERE node.id = i.department
                    AND df.category IS NOT NULL
                    ORDER BY c.node_left DESC
                    LIMIT 1
                )
            )
            LEFT JOIN vue_files f ON (f.product = i.id AND f.cover = 'on')
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso            
            WHERE i.id IN ({$pl->place_holder})            
            ORDER BY i.id DESC
        ";
        return $this->query($sql, $params);
    }

    public function read(int $id){
        $this->setEntity('Basic');
        $params = ['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n'=>$this->getL10nId(), 'id' => $id];
        $sql = "";
        $sql = "
            SELECT i.id,
            i.weight,
            i.workspace,
            i.license, 
            i.department,
            i.behavior AS 'behavior_id',
            i.has_sizes,
            i.stock_management,
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',      
            i_l10n.short_desc,
            i_l10n.description,
            i_l10n.meta_title,
            i_l10n.meta_description,
            l10n.id AS 'l10n_id',
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',          
            b._type AS 'behavior_type',
            (SELECT
                JSON_OBJECT(
                'price',  prices.price,                    
                'currency_id', cur.currency_id,
                'currency_code', cur.currency_lib)
                FROM prices                          
                WHERE prices.item = i.id
                AND prices.currency = cur.currency_id
                AND prices.valid_since <= current_timestamp() 
                AND (prices.valid_until IS NULL OR current_timestamp() <= prices.valid_until)
                ORDER BY prices.id DESC 
                LIMIT 1
            ) AS 'price'                             
            FROM items i      
            LEFT JOIN l10ns l10n ON l10n.id = :l10n 
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)           
            LEFT JOIN category_default_content df ON (
                df.l10n = 1 
                AND df.category = (
                    SELECT c.id
                    FROM categories node 
                    LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
                    LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = l10n.id)
                    WHERE node.id = i.department
                    AND df.category IS NOT NULL
                    ORDER BY c.node_left DESC
                    LIMIT 1
                )
            )
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso
            LEFT JOIN currency cur ON cur.currency_id = :cur
            WHERE i.id = :id;
        ";
        $product = $this->query($sql, $params, true);
        if($product):
            $this->setEntity(null);           
            $product->files = $this->files($id);            
        endif;
        return $product;
    }

    public function setL10ns(){
        $this->setEntity(null);
        $sql = "SELECT v_s.menu AS 'menu_id', 
        CONCAT_WS('/', l10ns._prefix, v_s.slug) AS 'slug', 
        v_s.l10n, 
        l10ns._locale,
        CASE WHEN v_s.l10n = :l10n THEN 'current' ELSE '' END AS 'class' 
        FROM menu_l10ns v_s 
        JOIN l10ns ON l10ns.id = v_s.l10n
        JOIN menu_websites m_w ON (m_w.menu = v_s.menu AND m_w.website = :ws)
        WHERE v_s.menu = :menu      
        AND m_w.visible = 1;";
        return $this->query($sql, ['menu'=> $this->section->menu_id, 'l10n' => $this->section->l10n_id, 'ws'=> $this->section->website]);
    } 

    
    public function files($id) { 
        $this->setEntity('File'); 
        $files = [];        
        $sql = "SELECT 
            f.id,
            f.file_id, 
            f.url, 
            f.type,
            f.product,             
            f.associate,
            f.cover, 
            f.position        
            FROM vue_files AS f             
            WHERE f.product = :id AND f.associate = 'on'
            ORDER BY f.cover DESC, f.position IS NOT NULL DESC, f.position ASC;";
        $files = $this->query($sql, ['id' => $id]);             
        return $files;               
    }

    public function reinsuranceOnCart(int $website = 5)
    {
        $this->setEntity(null);
        $sql = "SELECT 
        ri.designation,
        ri.body,
        ri.icon,
        ri.link,
        ri.l10n,
        ri.icon_type
        FROM reinsurance AS r
        JOIN reinsurance_items AS ri ON (ri.reinsurance = r.id AND ri.l10n = :l10n)        
        WHERE r.website = :website 
        AND r.place = 'cart'
        AND ri.position >= 1
        ORDER BY ri.position;";

        return $this->query($sql, ['website' => $website, 'l10n' => $this->getL10nId()]);
    }


    public function finishes(int $family = 1)
    {       
        $this->setEntity('Finish');        
        $sql = "SELECT  
        opt.id,        
        opt_l10n.name,                   
        (SELECT 
            CASE WHEN o_price.currency IN (3,4) THEN (o_price.price * 1.20) ELSE o_price.price END
            FROM option_prices o_price
            WHERE o_price.option = opt.id 
            AND o_price.currency = cur.currency_id                
            AND o_price.valid_since <= current_timestamp()
            AND (o_price.valid_until IS NULL OR current_timestamp() <= o_price.valid_until)
            AND JSON_CONTAINS(o_price.universes, :family, '$') = 1
            AND o_price.kit_type = 2                
            ORDER BY o_price.id DESC 
            LIMIT 1) 
        AS 'price',
        cur.currency_lib AS 'currency_code',
        l10ns._locale AS 'locale', 
        :family AS 'family',         
        country.vat  
        FROM options AS opt
        JOIN l10ns ON l10ns.id = :l10n_id
        LEFT JOIN option_l10ns AS opt_l10n ON (opt_l10n.option = opt.id AND opt_l10n.l10n = l10ns.id)      
        LEFT JOIN currency AS cur ON cur.currency_id = :cur        
        LEFT JOIN country ON country.country_iso = :country        
        WHERE opt.opt_type = 'finish' 
        ORDER BY opt._order;";        
        $params = array_filter(array('l10n_id' => $this->getL10nId(), 'cur'=>$this->getCurrencyId(), 'country' => $this->getCountryCode(), 'family' => $family));
        return $this->query($sql, $params);
    }

    public function premiums(int $family = 1){
        $this->setEntity('Finish');
        $sql = "SELECT  
        opt.id,        
        opt_l10n.name,                   
        (SELECT 
            CASE WHEN o_price.currency IN (3,4) THEN (o_price.price * 1.20) ELSE o_price.price END
            FROM option_prices o_price
            WHERE o_price.option = opt.id 
            AND o_price.currency = cur.currency_id                
            AND o_price.valid_since <= current_timestamp()
            AND (o_price.valid_until IS NULL OR current_timestamp() <= o_price.valid_until)
            AND JSON_CONTAINS(o_price.universes, :family, '$') = 1
            AND o_price.kit_type = 2                
            ORDER BY o_price.id DESC 
            LIMIT 1) 
        AS 'price',
        cur.currency_lib AS 'currency_code',
        l10ns._locale AS 'locale', 
        :family AS 'family',         
        country.vat  
        FROM options AS opt
        JOIN l10ns ON l10ns.id = :l10n_id
        LEFT JOIN option_l10ns AS opt_l10n ON (opt_l10n.option = opt.id AND opt_l10n.l10n = l10ns.id)      
        LEFT JOIN currency AS cur ON cur.currency_id = :cur        
        LEFT JOIN country ON country.country_iso = :country        
        WHERE opt.opt_type = 'premium' 
        AND opt.full_custom = 0
        ORDER BY opt._order;";        
        $params = array_filter(array('l10n_id' => $this->getL10nId(), 'cur'=>$this->getCurrencyId(), 'country' => $this->getCountryCode(), 'family' => $family));
        return $this->query($sql, $params);
    }

    public function mini_plates(int $family = 1){
        $this->setEntity('Finish');        
        $sql = "SELECT  
        opt.id,        
        opt_l10n.name,                   
        (SELECT 
            CASE WHEN o_price.currency IN (3,4) THEN (o_price.price * 1.20) ELSE o_price.price END
            FROM option_prices o_price
            WHERE o_price.option = opt.id 
            AND o_price.currency = cur.currency_id                
            AND o_price.valid_since <= current_timestamp()
            AND (o_price.valid_until IS NULL OR current_timestamp() <= o_price.valid_until)
            AND JSON_CONTAINS(o_price.universes, :family, '$') = 1
            AND o_price.kit_type = 2                
            ORDER BY o_price.id DESC 
            LIMIT 1) 
        AS 'price',
        cur.currency_lib AS 'currency_code',
        l10ns._locale AS 'locale', 
        :family AS 'family',         
        country.vat  
        FROM options AS opt
        JOIN l10ns ON l10ns.id = :l10n_id
        LEFT JOIN option_l10ns AS opt_l10n ON (opt_l10n.option = opt.id AND opt_l10n.l10n = l10ns.id)      
        LEFT JOIN currency AS cur ON cur.currency_id = :cur        
        LEFT JOIN country ON country.country_iso = :country        
        WHERE opt.opt_type = 'mini_plates' 
        AND EXISTS (SELECT o.price FROM option_prices o WHERE o.option = opt.id 
            AND o.currency = cur.currency_id                
            AND o.valid_since <= current_timestamp()
            AND (o.valid_until IS NULL OR current_timestamp() <= o.valid_until)
            AND JSON_CONTAINS(o.universes, :family, '$') = 1
            AND o.kit_type = 2                
            ORDER BY o.id DESC 
            LIMIT 1) 
        ORDER BY opt._order;";        
        $params = array_filter(array('l10n_id' => $this->getL10nId(), 'cur'=>$this->getCurrencyId(), 'country' => $this->getCountryCode(), 'family' => $family));
        return $this->query($sql, $params);
    }    

    public function hubs_stickers(int $family = 1){
        $this->setEntity('Finish');        
        $sql = "SELECT  
        opt.id,        
        opt_l10n.name,                   
        (SELECT 
            CASE WHEN o_price.currency IN (3,4) THEN (o_price.price * 1.20) ELSE o_price.price END
            FROM option_prices o_price
            WHERE o_price.option = opt.id 
            AND o_price.currency = cur.currency_id                
            AND o_price.valid_since <= current_timestamp()
            AND (o_price.valid_until IS NULL OR current_timestamp() <= o_price.valid_until)
            AND JSON_CONTAINS(o_price.universes, :family, '$') = 1
            AND o_price.kit_type = 2                
            ORDER BY o_price.id DESC 
            LIMIT 1) 
        AS 'price',
        cur.currency_lib AS 'currency_code',
        l10ns._locale AS 'locale', 
        :family AS 'family',         
        country.vat  
        FROM options AS opt
        JOIN l10ns ON l10ns.id = :l10n_id
        LEFT JOIN option_l10ns AS opt_l10n ON (opt_l10n.option = opt.id AND opt_l10n.l10n = l10ns.id)      
        LEFT JOIN currency AS cur ON cur.currency_id = :cur        
        LEFT JOIN country ON country.country_iso = :country        
        WHERE opt.opt_type = 'hubs_stickers' 
        AND EXISTS (SELECT o.price FROM option_prices o WHERE o.option = opt.id 
            AND o.currency = cur.currency_id                
            AND o.valid_since <= current_timestamp()
            AND (o.valid_until IS NULL OR current_timestamp() <= o.valid_until)
            AND JSON_CONTAINS(o.universes, :family, '$') = 1
            AND o.kit_type = 2                
            ORDER BY o.id DESC 
            LIMIT 1) 
        ORDER BY opt._order;";        
        $params = array_filter(array('l10n_id' => $this->getL10nId(), 'cur'=>$this->getCurrencyId(), 'country' => $this->getCountryCode(), 'family' => $family));
        return $this->query($sql, $params);
    } 
}