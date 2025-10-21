<?php
declare(strict_types=1);
namespace Domain\Table;

class SaddleCover extends Catalog {
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
            c_l10n.breadcrumb,
            c_l10n.slug,
			c_l10n.category AS 'category_id',
			c_l10n.l10n AS 'l10n_id'
			FROM vue_slugs v_s
            LEFT JOIN categories c ON  c.id = v_s.id
			LEFT JOIN category_l10ns c_l10n ON (c_l10n.category = c.id  AND c_l10n.l10n = v_s.l10n)
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
        JOIN saddle_covers gd ON gd.id = i.id
        JOIN item_stores i_s ON (i_s.item = i.id AND i_s.status = 1 AND i_s.store = :store)
        WHERE i.department IN (
            (SELECT c.id
            FROM categories p
            LEFT JOIN categories c ON (c.workspace = p.workspace AND c.node_left >= p.node_left AND c.node_right <= p.node_right)
            WHERE p.id = :id
            ORDER BY c.node_left)    
        )          
        ORDER BY -i.position  DESC, i.id DESC;";
        $query = $this->query($sql, ['id'=>$id, 'store'=> $this->_store]);
        return $query;
    } 

    public function listOfProductsWithFilters(int $id): array {

        $queries = $this->getRequest()->getQueryParams();        
        $sql_parts = [
            'fields' => ["SELECT i.id "],
            'tables' => ["FROM saddle_covers gd 
                JOIN items i ON i.id = gd.id
                JOIN item_stores i_s ON (i_s.item = i.id AND i_s.status = 1 AND i_s.store = :store)
                JOIN item_vehicles i_v ON i_v.item = gd.id 
                JOIN vue_vehicle_2 v ON (v.id = i_v.vehicle AND v.l10n = 1)"],
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
                case 'universes':
                    $universes = explode(',', $values);                   
                    $plh = $this->namedPlaceHolder($universes, $k);
                    $params = array_merge($params, $plh->values);
                    //$sql .= " AND v.universe IN({$plh->place_holder})";  
                    $sql_parts['conds'][] = " AND v.universe IN({$plh->place_holder})"; 
                    break;
                case 'brands':
                    $brands = explode(',', $values);                   
                    $plh = $this->namedPlaceHolder($brands, $k);
                    $params = array_merge($params, $plh->values);
                    //$sql .= " AND v.brand IN({$plh->place_holder})"; 
                    $sql_parts['conds'][] = "AND v.brand IN({$plh->place_holder})";  
                    break;
                case 'vehicles':
                    $vehicles = explode(',', $values);                   
                    $plh = $this->namedPlaceHolder($vehicles, $k);
                    $params = array_merge($params, $plh->values);
                    //$sql .= " AND v.id IN({$plh->place_holder})";  
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
        $sql_parts['conds'][] = " GROUP BY i.id ORDER BY -i.position  DESC, i.id DESC;";
        $sql_parts['fields'] = implode(', ', $sql_parts['fields']);
        $sql_parts['tables'] = implode(' ', $sql_parts['tables']);
        $sql_parts['conds'] = implode(' ', $sql_parts['conds']);
        $sql = implode('', $sql_parts);
        $query = $this->query($sql, $params);
        return $query;
    } 


    public function listOfProductsToExport():array {        
        $this->setEntity('SaddleCoverExport');        
        $params = array_merge(['c_iso' => $this->getCountryCode(), 'l10n'=>$this->getL10nId()]);        
        $sql = " SELECT i.id AS 'item',           
            l10n.id AS 'l10n',            
            g._type AS 'saddle_type_id',
            s_type.designation AS 'saddle_type_name',
            null AS 'family_name',                
            g.design AS 'design_id', 
            g.color AS 'color_id',
            CONCAT_WS(' ', d.name, d.season) AS 'design_name',
            color.designation AS 'color_name',           
            
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            i_l10n.item_slug,
            df.description,
            df.full_designation AS 'df_full_designation'               
            FROM saddle_covers g
            JOIN  items i ON i.id = g.id
            LEFT JOIN l10ns l10n ON l10n.id = :l10n                    
            LEFT JOIN designs d ON d.id = g.design
            LEFT JOIN color_l10ns color ON (color.color = g.color AND color.l10n = l10n.id)            
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)
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
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso   
            LEFT JOIN product_type_l10ns s_type ON (s_type.product_type = g._type AND s_type.l10n = l10n.id)            
            GROUP BY g.id
            ORDER BY -i.position  DESC, i.id DESC;
        ";
        $cards =$this->query($sql, $params);
       
        foreach($cards as $card):
            if($card->full_designation === null):
                $this->setEntity(null);
                $sql = "SELECT c_l10n.designation FROM item_colors i_c JOIN color_l10ns c_l10n ON (c_l10n.color = i_c.color AND c_l10n.l10n = :l10n_id) WHERE i_c.item = :id
                ";
                $q = $this->query($sql, ['id' => $card->item, 'l10n_id' => $this->getL10nId()]);                
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
        $this->setEntity('SaddleCover');
        $ids = array_column($slices, 'id');
        $pl = $this->namedPlaceHolder2($ids);
        $params = array_merge(['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n'=>$this->getL10nId()], $pl->values);        
        $sql = " SELECT i.id, 
            i.department, 
            i.weight, 
            i.license, 
            i.parent,  
            l10n.id AS 'l10n_id',
            df.category AS 'df_id',
            g._type AS 'saddle_type_id',
            CASE WHEN s_type.designation IS NOT NULL THEN s_type.designation ELSE p_type.name END AS 'saddle_type_name',
            null AS 'family_name',
            UPPER(l.name) AS 'brand_name',
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',
            g.design AS 'design_id', 
            g.color AS 'color_id',
            CONCAT_WS(' ', d.name, d.season) AS 'design_name',
            color.designation AS 'color_name',           
            b._type AS 'behavior_type',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            i_l10n.item_slug,
            df.full_designation AS 'df_full_designation',
            df.item_slug AS 'df_item_slug',
            f.url AS 'cover',            
            (SELECT
                JSON_OBJECT(
                'min', MIN(s_p.price),
                'max', MAX(s_p.price),
                'unique', CASE WHEN MIN(s_p.price) = MAX(s_p.price) THEN 1 ELSE 0 END,                
                'template_type',g._type,               
                'currency_id', cur.currency_id,
                'currency_code', cur.currency_lib)
                FROM item_vehicles i_v                
                LEFT JOIN saddle_prices s_p ON (s_p.vehicle = i_v.vehicle AND s_p.template_type = g._type)                
                WHERE i_v.item = i.id
                AND s_p.currency = cur.currency_id
                AND s_p.valid_since <= current_timestamp() 
                AND (s_p.valid_until IS NULL OR current_timestamp() <= s_p.valid_until)
            ) AS 'prices'           
            FROM saddle_covers g
            JOIN  items i ON i.id = g.id
            LEFT JOIN l10ns l10n ON l10n.id = :l10n                    
            LEFT JOIN designs d ON d.id = g.design
            LEFT JOIN color_l10ns color ON (color.color = g.color AND color.l10n = l10n.id)            
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)
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
            LEFT JOIN currency cur ON cur.currency_id = :cur
            LEFT JOIN licences l ON l.id = i.license
            LEFT JOIN product_types p_type ON p_type.id = g._type
            LEFT JOIN product_type_l10ns s_type ON (s_type.product_type = p_type.id AND s_type.l10n = l10n.id)
            WHERE g.id IN ({$pl->place_holder}) 
            GROUP BY g.id
            ORDER BY -i.position  DESC, i.id DESC;
        ";
        $cards =$this->query($sql, $params);
       
        
         return $cards;
    }

    public function read(int $id){
        $this->setEntity('SaddleCover');
        $params = ['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n'=>$this->getL10nId(), 'id' => $id];
        $sql = "
            SELECT g.id,
            i.weight,
            i.workspace,
            i.license, 
            i.department,
            i.behavior AS 'behavior_id',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
             df.full_designation AS 'df_full_designation',
            i_l10n.short_desc,
            i_l10n.description,
            CONCAT_WS(' | ', i_l10n.meta_title, 'Kutvek kitgraphik') AS 'meta_title',
            i_l10n.meta_description,
            i_l10n.item_slug,
            l10n.id AS 'l10n_id',
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',
            CASE WHEN g.design IS NOT NULL THEN g.design ELSE 0 END AS 'design_id', 
            g.color AS 'color_id',           
            g._type AS 'item_type',
            CONCAT_WS(' ', d.name, d.season) AS 'design_name',
            color.designation AS 'color_name',     
            b._type AS 'behavior_type',
            CASE WHEN JSON_VALUE(g.attr, '$.foam') = 1 THEN 1 ELSE 0 END AS 'foam',
            CASE WHEN JSON_VALUE(g.attr, '$.install') = 1 THEN 1 ELSE 0 END AS 'install'                  
            FROM saddle_covers g
            JOIN  items i ON i.id = g.id           
            LEFT JOIN l10ns l10n ON l10n.id = :l10n 
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id) 
            LEFT JOIN designs d ON d.id = g.design
            LEFT JOIN color_l10ns color ON (color.color = g.color AND color.l10n = l10n.id)
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
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso
            LEFT JOIN currency cur ON cur.currency_id = :cur
            WHERE g.id = :id;
        ";
        $product = $this->query($sql, $params, true);
        if($product):
            $this->setEntity(null);
            $product->suitable =  $this->suitable($product->id, $product->currency_id);
            $product->files = $this->files($id);
            $product->optFoam = $this->optFoam();
        endif;
        return $product;
    }

    public function optFoam(){
        $params = ['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n_id'=>$this->getL10nId()];
        $this->setEntity('Option');
        $sql = "SELECT o.id, 
            o_l10n.name,
            l10n._locale,
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',
            (SELECT
                o_p.price               
                FROM option_prices o_p 
                WHERE o_p.option = o.id
                AND o_p.currency = cur.currency_id
                AND o_p.valid_since <= current_timestamp() 
                AND (o_p.valid_until IS NULL OR current_timestamp() <= o_p.valid_until)
                ORDER BY o_p.id DESC 
                LIMIT 1
            ) AS 'price'
            FROM options o 
            LEFT JOIN l10ns l10n ON l10n.id = :l10n_id
            LEFT JOIN option_l10ns o_l10n ON (o_l10n.option = o.id AND o_l10n.l10n = l10n.id)
            LEFT JOIN currency cur ON cur.currency_id = :cur
            LEFT JOIN country ON country.country_iso = :c_iso
            WHERE o.opt_type = 'foam'
            ORDER BY o._order;
        ";
        return $this->query($sql, $params);

    }    

    public function suitable(int $item,  int $cur): array {
        $this->setEntity('Years');
        $sql = "
        SELECT  s_c.id AS 'saddle_id',
        i_v.vehicle AS 'id',
        i_v.fullname,
        i_v.name,
        i_v.brand_id,
        i_v.brand_name,
        v_m.id AS 'year_id',
        currency.currency_lib AS 'currency_code',
        CASE WHEN v_m.finish = '-' THEN CONCAT(v_m.begin, '-') ELSE CONCAT_WS('-', v_m.begin, v_m.finish) END AS 'year',
        (SELECT
            s_p.price               
            FROM saddle_prices s_p 
            WHERE s_p.vehicle = i_v.vehicle 
            AND s_p.template_type = s_c._type            
            AND s_p.currency = currency.currency_id
            AND s_p.valid_since <= current_timestamp() 
            AND (s_p.valid_until IS NULL OR current_timestamp() <= s_p.valid_until)
            ORDER BY s_p.id DESC 
            LIMIT 1
        ) AS 'price'
        FROM saddle_covers s_c
        JOIN  vue_item_vehicles i_v ON i_v.item = s_c.id
        JOIN  vehicle_millesims v_m ON (v_m.vehicle = i_v.vehicle AND v_m.type = 'seat-cover')
        JOIN currency ON currency.currency_id = :cur
        WHERE s_c.id = :item
        ORDER BY i_v.vehicle, v_m.begin DESC;";
        return $this->query($sql,['item'=>$item, 'cur'=>$cur]);
    }
}