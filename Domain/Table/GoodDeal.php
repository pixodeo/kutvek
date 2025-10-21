<?php
declare(strict_types=1);
namespace Domain\Table;

class GoodDeal extends Catalog  {
	

	public function editorial(string $slug){
        
		$sql = "SELECT 
			CASE WHEN c_l10n.full_designation IS NOT NULL THEN c_l10n.full_designation ELSE c_l10n.designation END AS 'designation',
			c_l10n.short_desc,
			c_l10n.description,
			c_l10n.meta_title,
            c_l10n.breadcrumb AS 'breadcrumb_name',
			c_l10n.meta_description,
			c_l10n.cover,
			c_l10n.portrait,
            v_s.slug AS 'slug', 
			c_l10n.category AS 'category_id',
            c.department_store,
			c_l10n.l10n AS 'l10n_id'
			FROM vue_slugs v_s 
            JOIN l10ns ON l10ns.id = v_s.l10n
            LEFT JOIN categories c ON  c.id = v_s.id  
			LEFT JOIN category_l10ns c_l10n ON (c_l10n.category = c.id  AND c_l10n.l10n = l10ns.id)
			WHERE v_s.slug = :slug 
			AND v_s.l10n = :l10n
			AND v_s.slug_type = 'section';
		";
		$editorial = $this->query($sql, ['slug' => $slug, 'l10n' => $this->getL10nId()], true);

		if($editorial):
			$editorial->l10ns = $this->slugs($editorial->category_id); 
		endif;	
		return $editorial;
	}

    public function store(string $slug){
        $sql = "SELECT           
            c.department_store, 
            c.id AS 'category_id'      
            FROM vue_slugs v_s
            LEFT JOIN categories c ON  c.id = v_s.id           
            WHERE v_s.slug = :slug 
            AND v_s.l10n = :l10n
            AND v_s.slug_type = 'section';
        ";
        $q = $this->query($sql, ['slug' => $slug, 'l10n' => $this->getL10nId()], true);
        return $q;
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

    public function listOfProducts(int $id):array{
        $sql = "SELECT i.id
        FROM items i 
        JOIN good_deal gd ON gd.id = i.id
        WHERE i.department IN (
            (SELECT c.id
            FROM categories p
            LEFT JOIN categories c ON (c.workspace = p.workspace AND c.node_left >= p.node_left AND c.node_right <= p.node_right)
            WHERE p.id = :id
            ORDER BY c.node_left)    
        )
        AND gd.qty > 0
        ORDER BY i.id DESC;";
        $query = $this->query($sql, ['id'=>$id]);
        return $query;
    } 

    public function listOfProductsWithFilters(int $id): array {

        $queries = $this->getRequest()->getQueryParams();        
        $sql_parts = [
            'fields' => ["SELECT i.id "],
            'tables' => ["FROM good_deal gd 
                JOIN items i ON i.id = gd.id
                JOIN item_stores i_s ON (i_s.item = i.id AND i_s.status = 1 AND i_s.store = :store)
                LEFT JOIN item_vehicles i_v ON i_v.item = gd.id 
                LEFT JOIN vue_vehicle_2 v ON (v.id = CASE WHEN gd.behavior = 5 THEN i_v.vehicle ELSE gd.vehicle END    AND v.l10n = 1)               
                "],
            'conds'  => ["WHERE i.department IN (
                (SELECT c.id
                FROM categories p
                LEFT JOIN categories c ON (c.workspace = p.workspace AND c.node_left >= p.node_left AND c.node_right <= p.node_right)
                WHERE p.id = :id
                ORDER BY c.node_left))"
            ]
        ];
        $params = ['id'=>$id, 'store'=> $this->_store];
        foreach($queries as $k => $values)
        {
            switch($k){
                case 'behaviors':
                     $behaviors = explode(',', $values);                   
                    $plh = $this->namedPlaceHolder($behaviors, $k);
                    $params = array_merge($params, $plh->values);                   
                    $sql_parts['conds'][] = " AND gd.behavior IN({$plh->place_holder})"; 
                    break;
                case 'universes':
                    $universes = explode(',', $values);                   
                    $plh = $this->namedPlaceHolder($universes, $k);
                    $params = array_merge($params, $plh->values);                    
                    $sql_parts['conds'][] = " AND v.universe IN({$plh->place_holder})"; 
                    break;
                case 'brands':
                    $brands = explode(',', $values);                   
                    $plh = $this->namedPlaceHolder($brands, $k);
                    $params = array_merge($params, $plh->values);                   
                    $sql_parts['conds'][] = "AND v.brand IN({$plh->place_holder})";  
                    break;
                case 'vehicles':
                    $vehicles = explode(',', $values);                   
                    $plh = $this->namedPlaceHolder($vehicles, $k);
                    $params = array_merge($params, $plh->values);                    
                    $sql_parts['conds'][] = "AND v.id IN({$plh->place_holder})";
                    break;
                case 'colors':
                    $colors = explode(',', $values);                   
                    $plh = $this->namedPlaceHolder($colors, $k);
                    $params = array_merge($params, $plh->values);   
                    $sql_parts['tables'][] = "JOIN item_colors i_c ON i_c.item = i.id AND i_c.color IN({$plh->place_holder})";
                    break;
            }                              
        }        
        $sql_parts['conds'][] = " GROUP BY i.id ORDER BY  i.id DESC;";
        $sql_parts['fields'] = implode(', ', $sql_parts['fields']);
        $sql_parts['tables'] = implode(' ', $sql_parts['tables']);
        $sql_parts['conds'] = implode(' ', $sql_parts['conds']);
        $sql = implode('', $sql_parts);
        $query = $this->query($sql, $params);
        return $query;
    }

     public function listOfProductsToExport(): array {       
        $this->setEntity('SaleCardExport');       
        $params = array_merge(['c_iso' => $this->getCountryCode(), 'l10n'=>$this->getL10nId()]);        
        $sql = " SELECT i.id AS 'item',                  
            i_l10n.item_slug,
            l10n.id AS 'l10n',           
            CONCAT_WS(' ', d.name, d.season) AS 'design_name',
            color.designation AS 'color_name',
            CASE WHEN g.vehicle IS NOT NULL 
            THEN CONCAT_WS(' ', v.fullname , CONCAT_WS('-', g.year_start, g.year_end) )            
            END AS 'vehicle_fullname',          
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            CASE WHEN i_l10n.description IS NOT NULL THEN i_l10n.description ELSE g.description END AS 'description',
            df.description AS 'df_description',
            g.behavior,
            df.full_designation AS 'df_full_designation',
            s_type.designation AS 'saddle_type_name'                   
            FROM good_deal g
            JOIN  items i ON i.id = g.id
            LEFT JOIN l10ns l10n ON l10n.id = :l10n
            LEFT JOIN designs d ON d.id = g.graphic_range
            LEFT JOIN color_l10ns color ON (color.color = g.color_ref AND color.l10n = l10n.id)
            LEFT JOIN vue_vehicle_2 v ON (v.id = g.vehicle AND v.l10n = l10n.id)
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)
            LEFT JOIN category_default_content df ON (
                df.l10n = l10n.id
                AND df.category = (
                    SELECT c.id
                    FROM categories node 
                    LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
                    LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = l10n.id)
                    WHERE node.id = g.department
                    AND df.category IS NOT NULL
                    ORDER BY c.node_left DESC
                    LIMIT 1
                )
            )            
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso 
            LEFT JOIN product_type_l10ns s_type ON (s_type.product_type = g._type AND s_type.l10n = l10n.id)   
            ORDER BY i.id DESC
        ";
        $cards = $this->query($sql, $params);
        foreach($cards as $card):
            if($card->full_designation === null):
                $this->setEntity(null);
                $sql = "SELECT c_l10n.designation FROM item_colors i_c JOIN color_l10ns c_l10n ON (c_l10n.color = i_c.color AND c_l10n.l10n = :l10n_id) WHERE i_c.item = :id
                ";
                $q = $this->query($sql, ['id' => $card->item, 'l10n_id' => $this->getL10nId()]);   
                //$card->colours = $q;             
                if(count($q) > 0):
                    $color = implode('/', array_column($q,'designation'));
                    $card->color_name = $color;

                endif;
                
                $universes = $this->query("SELECT
                    v.universe AS 'family_id',
                    v.fam_name AS 'family_name'
                    FROM item_vehicles i_v
                    JOIN vue_vehicle_2 v ON v.id = i_v.vehicle AND v.l10n = :l10n
                    WHERE i_v.item = :id
                    GROUP BY v.universe
                    ORDER BY v.position
                ",
                ['l10n' => $this->getL10nId(), 'id' => $card->item]);
                $card->family_name = implode('/', array_column($universes,'family_name'));

                $brands = $this->query("SELECT
                v.brand AS 'brand_id',
                v.brand_name
                FROM `item_vehicles` i_v                
                JOIN vue_vehicle_2 v ON v.id = i_v.vehicle AND v.l10n = :l10n
                WHERE i_v.item = :id
                GROUP BY v.brand
                ORDER BY v.brand_name
                ",
                ['l10n' => $this->getL10nId(),  'id' => $card->item]);

                $card->brand_name = (count($brands) > 1) ? null : implode('/', array_column($brands,'brand_name'));
                $card->brands = $brands;
            endif; 
        endforeach;
        return $cards;
    }


    public function cards(array $slices = []):array{
        if(count($slices) < 1) return [];
        $this->setEntity('SaleCard');
        $ids = array_column($slices, 'id');
        $pl = $this->namedPlaceHolder2($ids);
        $params = array_merge(['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n'=>$this->getL10nId()], $pl->values);        
        $sql = " SELECT i.id, 
            g.department,
            df.category AS 'df_id', 
            i.weight, 
            i.license, 
            i.parent,  
            i_l10n.item_slug,
            l10n.id AS 'l10n_id',
            cur.currency_lib AS 'currency_code',
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',
            g.graphic_range AS 'design_id', 
            g.color_ref AS 'color_id',
            CONCAT_WS(' ', d.name, d.season) AS 'design_name',
            color.designation AS 'color_name',
            g.vehicle AS 'vehicle_id',
           	CONCAT_WS(' ', v.fullname , CONCAT_WS('-', g.year_start, g.year_end) ) AS 'vehicle_fullname',
            #c.kit_type, 
            #c.behavior_id,
            b._type AS 'behavior_type',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            df.full_designation AS 'df_full_designation',
            f.url AS 'cover',
            (SELECT 
            	JSON_OBJECT(
            		'new', CASE WHEN price.price_type = 3 THEN (
            			CASE 
            			WHEN price.currency = 1 THEN ROUND((price.price / 1.20) - ((price.price /1.20) * price.rebate / 100) , 2) 
                        ELSE ROUND( price.price - (price.price * price.rebate / 100) , 2) 
            			END)
            			ELSE price.price 
            			END,  
            		'old', CASE WHEN price.currency = 1 THEN ROUND(price.price / 1.20 , 2)
                            WHEN price.currency = 4 THEN ROUND(price.price  , 2)
                            ELSE ROUND(price.price, 2) END,        		
            		'rebate', price.rebate,
            		'type', price.price_type
            	)
	            FROM prices price
	            where price.item = g.id
	            AND price.currency = cur.currency_id
	            AND price.valid_since <= current_timestamp() 
	            AND (price.valid_until IS NULL OR current_timestamp() <= price.valid_until)	            
	            ORDER BY price.id DESC 
	            LIMIT 1
	        ) AS 'price'            
            FROM good_deal g
            JOIN  items i ON i.id = g.id
            LEFT JOIN l10ns l10n ON l10n.id = :l10n
            LEFT JOIN designs d ON d.id = g.graphic_range
            LEFT JOIN color_l10ns color ON (color.color = g.color_ref AND color.l10n = l10n.id)
            LEFT JOIN vue_vehicle_2 v ON (v.id = g.vehicle AND v.l10n = l10n.id)
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)
            LEFT JOIN category_default_content df ON (
                df.l10n = l10n.id
                AND df.category = (
                    SELECT c.id
                    FROM categories node 
                    LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
                    LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = l10n.id)
                    WHERE node.id = g.department
                    AND df.category IS NOT NULL
                    ORDER BY c.node_left DESC
                    LIMIT 1
                )
            )
            LEFT JOIN vue_files f ON (f.product = i.id AND f.cover = 'on')
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso
            LEFT JOIN currency cur ON cur.currency_id = :cur
            WHERE g.id IN ({$pl->place_holder}) 
            ORDER BY i.id DESC
        ";
        return $this->query($sql, $params);
    }

    public function read(int $id){
        $this->setEntity('GoodDeal');
        $params = ['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n'=>$this->getL10nId(), 'id' => $id];
        $sql = "
            SELECT g.id,
            i.weight,
            i.workspace,
            i.department AS 'department_id',
            cat_l10n.breadcrumb AS 'breadcrumb_name',
            i.behavior AS 'behavior_id',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            df.full_designation AS 'df_full_designation',
            i_l10n.short_desc,
            i_l10n.description,
            i_l10n.meta_title,
            i_l10n.meta_description,
            l10n.id AS 'l10n_id',
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',
            g.graphic_range AS 'design_id', 
            g.color_ref AS 'color_id',
            CONCAT_WS(' ', d.name, d.season) AS 'design_name',
            color.designation AS 'color_name',
            g.vehicle AS 'vehicle_id',
            CONCAT_WS(' ', v.fullname , CONCAT_WS('-', g.year_start, g.year_end) ) AS 'vehicle_fullname',
            #c.kit_type, 
            #c.behavior_id,
            b._type AS 'behavior_type',
            (SELECT 
                JSON_OBJECT(
                    'new', CASE WHEN price.price_type = 3 THEN (
                        CASE 
                        WHEN price.currency = 3 
                             THEN ROUND((price.price - (price.price * price.rebate / 100)) * 1.20 , 2) 
                             ELSE ROUND( price.price - (price.price * price.rebate / 100)  , 2) 
                        END
                    )
                        ELSE price.price 
                        END,  
                    'old', CASE WHEN price.currency = 3 THEN ROUND(price.price * 1.20 , 2) ELSE price.price END,                
                    'rebate', price.rebate,
                    'type', price.price_type
                )
                FROM prices price
                where price.item = g.id
                AND price.currency = cur.currency_id
                AND price.valid_since <= current_timestamp() 
                AND (price.valid_until IS NULL OR current_timestamp() <= price.valid_until)             
                ORDER BY price.id DESC 
                LIMIT 1
            ) AS 'price'   
            FROM good_deal g
            JOIN  items i ON i.id = g.id
            LEFT JOIN l10ns l10n ON l10n.id = :l10n 
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id) 
            LEFT JOIN designs d ON d.id = g.graphic_range
            LEFT JOIN color_l10ns color ON (color.color = g.color_ref AND color.l10n = l10n.id)
            LEFT JOIN vue_vehicle_2 v ON (v.id = g.vehicle AND v.l10n = l10n.id)
            LEFT JOIN category_default_content df ON (
                df.l10n = l10n.id 
                AND df.category = (
                    SELECT c.id
                    FROM categories node 
                    LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
                    LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = l10n.id)
                    WHERE node.id = g.department
                    AND df.category IS NOT NULL
                    ORDER BY c.node_left DESC
                    LIMIT 1
                )
            )
            LEFT JOIN category_l10ns cat_l10n ON (cat_l10n.category = i.department AND cat_l10n.l10n = l10n.id)
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso
            LEFT JOIN currency cur ON cur.currency_id = :cur
            WHERE g.id = :id;
        ";
        $product = $this->query($sql, $params, true);
        if($product):
            $this->setEntity(null);
            $product->suitable =  $this->query("SELECT iv.vehicle AS 'id', iv.fullname, iv.principal FROM vue_item_vehicles iv WHERE iv.item = :item;", ['item' => $id]);
            $product->files = $this->files($id);
        endif;
        return $product;
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
}