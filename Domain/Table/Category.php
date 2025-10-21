<?php
declare(strict_types=1);
namespace Domain\Table;

class Category extends Catalog {
	protected $section;	
	private bool $_withFilters = false;
	private array $_filters = [];
    

    public function setSection(object $section):void{$this->section = $section;}
	
	public function read(int $section){
		$this->setConstructorArgs([$this->_route]);
		$sql = "SELECT 
			m.id AS 'menu_id',
			m_l10n.title,
			m_l10n.name,
			m_l10n.short_desc,
			m_l10n.description,
			m_l10n.features,
			m_l10n.faq,
			m_l10n.further_info,
			m_l10n.meta_title,
			m_l10n.meta_description,
			m_l10n.files,
			l10n.id AS 'l10n_id',
			m_w.filters,
			m_w.attr,
			m_w.category,
			m_w.website,
			CASE 
				WHEN m_w.department_store IS NOT NULL THEN m_w.department_store 
				ELSE (
					SELECT mw_2.department_store
					FROM menu_websites mw_2
					LEFT JOIN menus m2 ON (m2.id = mw_2.menu AND m2.workspace = m.workspace)
					WHERE  mw_2.website = m_w.website 
					AND mw_2.department_store IS NOT NULL
					AND m2.node_left < m.node_left 
					AND m2.node_right > m.node_right 
					ORDER BY m2.node_left DESC LIMIT 1
				) 
			END AS 'department_store',						
			(SELECT JSON_ARRAYAGG(menu_product_types.product_type) FROM menu_product_types WHERE menu_product_types.menu = m.id) AS types,
			(SELECT JSON_ARRAYAGG(m_c.category)  FROM menu_categories m_c WHERE m_c.menu = m.id AND m_c.website = m_w.website) AS 'categories'			
			FROM menus m
			LEFT JOIN l10ns l10n ON l10n.id = :l10n_id
			LEFT JOIN menu_l10ns m_l10n ON (m_l10n.menu = m.id AND m_l10n.l10n = l10n.id)
			LEFT JOIN menu_websites m_w ON (m_w.menu = m.id AND m_w.website = :ws)
			WHERE m.id = :id;";
		$this->section = $this->query($sql,['id'=>$section, 'l10n_id'=>$this->getL10nId(), 'ws'=>WEBSITE_ID],true);
		if($this->section):			
			$this->section->types = json_decode($this->section->types??'[]',true);
			$this->section->categories = json_decode($this->section->categories??'[]',true);

			$this->section->items = $this->itemsInSection();
			$this->section->l10ns = $this->setL10ns();
		endif;
		return $this->section;
	}	

	public function itemsInCategory(int $category_id): array {
		$this->setConstructorArgs([$this->_route]);
		$this->setEntity(null);
		$params = ['store' => 13];
		
					
		$sql = "SELECT i.id
			FROM items i
			JOIN item_stores i_s ON (i_s.item = i.id AND i_s.store = :store AND i_s.status = 1)
			WHERE  i.department IN (600)
			ORDER BY i.id DESC;
		";
		$items = $this->query($sql, $params);
		return array_column($items,'id');



		/*$sql = "SELECT count(*) AS 'nb'		
		FROM vue_catalog c
		JOIN item_stores i_s ON (i_s.item = c.item AND i_s.store = :store AND i_s.status = 1)
		WHERE 1 = 1";
		if(count($this->section->product_types) > 0):
			$plh_1 = $this->namedPlaceHolder($this->section->product_types, 't');
			$sql .= " AND c.behavior IN ({$plh_1->place_holder})";
			$params = array_merge($params, $plh_1->values);
		endif; 
		if(isset($this->section->attr['family'])):
			$sql .= " AND c.family IN (:family) = 1";			
			$params = array_merge($params, ['family' => $this->section->attr['family']]);
		endif; 
		if(isset($this->section->attr['brand'])):
			$sql .= " AND c.brand IN (:brand)";
			$params = array_merge($params, ['brand' => $this->section->attr['brand']]);
		endif;   
		if(isset($this->section->attr['model'])):
			$sql .= " AND c.model IN (:model)";
			$params = array_merge($params, ['model' => $this->section->attr['model']]);
		endif; 
		if(isset($this->section->attr['vehicle'])):
			$sql .= " AND c.vehicle IN (:vehicle)";
			$params = array_merge($params, ['vehicle' => $this->section->attr['vehicle']]);
		endif; 
		$q = $this->query($sql, $params, true);*/
		
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
            i_l10n.item_slug,
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

	public function itemsInSectionWithFilters(): array {
		$this->setEntity(null);
		$params = ['store' => $this->section->department_store];
		$plh_1 = $this->namedPlaceHolder($this->section->types, 't');		
		$plh_2 = $this->namedPlaceHolder($this->section->categories, 'c');
		$params = array_merge($params, $plh_1->values,$plh_2->values);				
		$sql = "SELECT i.item
			FROM vue_catalog i
			JOIN item_stores i_s ON (i_s.item = i.item AND i_s.store = :store AND i_s.status = 1)
			WHERE i.behavior IN ({$plh_1->place_holder})
			AND i.department IN ({$plh_2->place_holder})			
		";		
		if($this->section->family !== null && count($this->section->family) > 0):			
			$holder = $this->namedPlaceHolder($this->section->family, 'fam');
			$sql .= " AND i.family IN ({$holder->place_holder}) ";
			$params = array_merge($params, $holder->values);
		endif; 
		if($this->section->brand !== null && count($this->section->brand) > 0):			
			$holder = $this->namedPlaceHolder($this->section->brand, 'brand');
			$sql .= " AND i.brand IN ({$holder->place_holder}) ";
			$params = array_merge($params, $holder->values);
		endif; 
		if($this->section->model !== null && count($this->section->model) > 0):			
			$holder = $this->namedPlaceHolder($this->section->model, 'model');
			$sql .= " AND i.model IN ({$holder->place_holder}) ";
			$params = array_merge($params, $holder->values);
		endif; 
		if($this->section->vehicle !== null && count($this->section->vehicle) > 0):			
			$holder = $this->namedPlaceHolder($this->section->vehicle, 'vehicle');
			$sql .= " AND i.vehicle IN ({$holder->place_holder}) ";
			$params = array_merge($params, $holder->values);
		endif; 
		if($this->section->color !== null && count($this->section->color) > 0):			
			$holder = $this->namedPlaceHolder($this->section->color, 'color');
			$sql .= " AND i.color IN ({$holder->place_holder}) ";
			$params = array_merge($params, $holder->values);
		endif; 
		if($this->section->design !== null && count($this->section->design) > 0):			
			$holder = $this->namedPlaceHolder($this->section->design, 'design');
			$sql .= " AND i.design IN ({$holder->place_holder}) ";
			$params = array_merge($params, $holder->values);
		endif; 	
		$sql .= " ORDER BY i.item DESC";
		$items = $this->query($sql, $params);
		return array_values(array_column($items,'item'));		
	}

	public function setL10ns(){
        $this->setEntity(null);
        $sql = "SELECT v_s.menu AS 'menu_id', 
        CONCAT_WS('/', :fqdn, l10ns._prefix, v_s.slug) AS 'slug', 
        v_s.l10n, 
        l10ns._locale,
        CASE WHEN v_s.l10n = :l10n THEN 'current' ELSE '' END AS 'class' 
        FROM menu_l10ns v_s 
        JOIN l10ns ON l10ns.id = v_s.l10n
        JOIN menu_websites m_w ON (m_w.menu = v_s.menu AND m_w.website = :ws)
        WHERE v_s.menu = :menu      
        AND m_w.visible = 1;";
        return $this->query($sql, ['fqdn'=>FQDN,'menu'=> $this->section->menu_id, 'l10n' => $this->section->l10n_id, 'ws'=> $this->section->website]);
    } 

	public function children(){
		$sql = "SELECT name, designation, slug, fqdn,
		CASE WHEN l10n IN (3) THEN CONCAT_WS( '/', 'https:/', fqdn, 'en', slug) ELSE CONCAT_WS('/', 'https:/', fqdn, slug) END AS 'link'
		FROM vue_menu_websites_l10n 
		WHERE node_left > :lft 
		AND node_right < :rgt
		AND website = :site
		AND depth = :depth
		AND l10n = :l10n_id
		AND workspace = :ws
		ORDER BY node_left";
		return $this->query($sql, ['lft'=>$this->section->node_left, 'rgt'=>$this->section->node_right, 'site'=>$this->section->website, 'ws'=>$this->section->workspace, 'l10n_id'=> $this->section->l10n_id, 'depth'=> $this->section->depth + 1 ]);

	}

	public function breadcrumb(){
		$sql = "SELECT menu, name, designation, slug, fqdn, 
		CASE WHEN l10n IN (3) THEN CONCAT_WS( '/', 'https:/', fqdn, 'en', CASE WHEN slug = '/' THEN NULL ELSE slug END) ELSE CONCAT_WS('/', 'https:/', fqdn, CASE WHEN slug = '/' THEN NULL ELSE slug END) END AS 'link'
		FROM   vue_menu_websites_l10n 
		WHERE node_left < :lft
        AND node_right > :rgt
        AND website = :site		
		AND l10n = :l10n_id
		AND workspace = :ws
		ORDER BY node_left;
        ";
        return $this->query($sql, ['lft'=>$this->section->node_left, 'rgt'=>$this->section->node_right, 'site'=>$this->section->website, 'ws'=>$this->section->workspace, 'l10n_id'=> $this->section->l10n_id]);
	}


	/**
	 * Récupère tous les types de produits qu'on peut afficher sur la section 
	 * Le plus souvent 1 seul comme kits déco
	 */
	public function typeOfProducts():array {
		$q = $this->query("SELECT product_type FROM menu_product_types WHERE menu = :menu;", ['menu' => $this->section->menu_id]);
		return array_column($q, 'product_type');
	}	

	public function models(): array {
		$this->setEntity(null);
		if($this->section->department_store === null) return [];
		$params = ['store' => $this->section->department_store];
		$sql = "SELECT i.model AS 'id' , m.name
		FROM item_stores i_s
		JOIN vue_catalog i ON i.item = i_s.item
		JOIN model_2 m ON m.id = i.model
		WHERE i_s.store = :store 
		AND i_s.status = 1 ";
		if($this->section->categories !== null && count($this->section->categories) > 0):
			$plh_2 = $this->namedPlaceHolder($this->section->categories, 'c');
			$sql .= "AND i.department IN ({$plh_2->place_holder}) ";
			$params = array_merge($params, $plh_2->values);
		endif; 
		if($this->section->product_types !== null && count($this->section->product_types) > 0):
			$plh_1 = $this->namedPlaceHolder($this->section->product_types, 't');
			$sql .= "AND i.behavior IN ({$plh_1->place_holder}) ";
			$params = array_merge($params, $plh_1->values);
		endif; 
		if($this->section->family !== null):			
			$sql .= "AND i.family = :family ";
			$params = array_merge($params, ['family' => $this->section->family]);
		endif; 
		if($this->section->brand !== null):
			$sql .= "AND i.brand = :brand ";
			$params = array_merge($params, ['brand' => $this->section->brand]);
		endif; 	
			$sql .= "GROUP BY i.model";
		$q = $this->query($sql, $params);
		return $q;
	}

	/**
	 * La liste de tous les  véhicule liés à la section courante pour le filtre Vehicle
	 * filtrer par behavior, family, brand
	 */
	public function vehicles(): array {
		$this->setEntity(null);
		if($this->section->department_store === null) return [];
		$params = ['store' => $this->section->department_store,'l10n_id' =>$this->section->l10n_id];
		$sql = "SELECT i.vehicle AS 'id' , v.name
		FROM item_stores i_s
		JOIN vue_catalog i ON i.item = i_s.item
		JOIN vue_vehicle_2 v ON (v.id = i.vehicle AND v.l10n = :l10n_id)
		WHERE i_s.store = :store 
		AND i_s.status = 1 ";
		if($this->section->product_types !== null &&  count($this->section->product_types) > 0):
			$plh_1 = $this->namedPlaceHolder($this->section->product_types, 't');
			$sql .= "AND i.behavior IN ({$plh_1->place_holder}) ";
			$params = array_merge($params, $plh_1->values);
		endif; 
		if($this->section->family !== null):			
			$sql .= "AND i.family = :family ";
			$params = array_merge($params, ['family' => $this->section->family]);
		endif; 
		if($this->section->brand !== null):
			$sql .= "AND i.brand = :brand ";
			$params = array_merge($params, ['brand' => $this->section->brand]);
		endif; 	
		if($this->section->model !== null):
			$sql .= "AND i.model = :model ";
			$params = array_merge($params, ['model' => $this->section->model]);
		endif; 
		$sql .= "GROUP BY i.vehicle  ORDER BY v.name";
		$q = $this->query($sql, $params);
		return $q;
	}


	public function designs(): array {
		$this->setEntity(null);
		if($this->section->department_store === null) return [];
		$params = ['store' => $this->section->department_store];
		$sql = "SELECT i.design AS 'id' , d.designation AS 'name'
		FROM item_stores i_s
		JOIN vue_catalog i ON i.item = i_s.item
		JOIN vue_designs d ON d.id = i.design
		WHERE i_s.store = :store 
		AND i_s.status = 1 ";
		if($this->section->categories !== null && count($this->section->categories) > 0):
			$plh_2 = $this->namedPlaceHolder($this->section->categories, 'c');
			$sql .= "AND i.department IN ({$plh_2->place_holder}) ";
			$params = array_merge($params, $plh_2->values);
		endif; 
		if($this->section->product_types !== null &&  count($this->section->product_types) > 0):
			$plh_1 = $this->namedPlaceHolder($this->section->product_types, 't');
			$sql .= "AND i.behavior IN ({$plh_1->place_holder}) ";
			$params = array_merge($params, $plh_1->values);
		endif; 
		if($this->section->family !== null):			
			$sql .= "AND i.family = :family ";
			$params = array_merge($params, ['family' => $this->section->family]);
		endif; 
		if($this->section->brand !== null):
			$sql .= "AND i.brand = :brand ";
			$params = array_merge($params, ['brand' => $this->section->brand]);
		endif; 	
		if($this->section->vehicle !== null):
			$sql .= "AND i.vehicle = :vehicle ";
			$params = array_merge($params, ['vehicle' => $this->section->vehicle]);
		endif; 
		if($this->section->model !== null):
			$sql .= "AND i.model = :model ";
			$params = array_merge($params, ['model' => $this->section->model]);
		endif; 	
			$sql .= " GROUP BY i.design ORDER BY d.designation ";
		$q = $this->query($sql, $params);
		return $q;	
	}

	public function colors(): array {
		if($this->section->department_store === null) return [];
		$params = ['store' => $this->section->department_store, 'l10n_id' =>$this->section->l10n_id];
		$sql = "SELECT i.color AS 'id' , CASE WHEN i.color = 115 THEN 'Replica' ELSE REPLACE(c.designation, '-', '/') END AS 'name'
		FROM item_stores i_s
		JOIN vue_catalog i ON i.item = i_s.item
		JOIN color_l10ns c ON (c.color = i.color AND c.l10n = :l10n_id)
		WHERE i_s.store = :store		 
		AND i_s.status = 1 ";
		if($this->section->categories !== null && count($this->section->categories) > 0):
			$plh_2 = $this->namedPlaceHolder($this->section->categories, 'cat');
			$sql .= "AND i.department IN ({$plh_2->place_holder}) ";
			$params = array_merge($params, $plh_2->values);
		endif; 
		if($this->section->product_types !== null &&  count($this->section->product_types) > 0):
			$plh_1 = $this->namedPlaceHolder($this->section->product_types, 't');
			$sql .= "AND i.behavior IN ({$plh_1->place_holder}) ";
			$params = array_merge($params, $plh_1->values);
		endif; 
		/*if($this->section->family !== null):			
			$sql .= "AND i.family = :family ";
			$params = array_merge($params, ['family' => $this->section->family]);
		endif; 
		if($this->section->brand !== null):
			$sql .= "AND i.brand = :brand ";
			$params = array_merge($params, ['brand' => $this->section->brand]);
		endif; 
		if($this->section->vehicle !== null):
			$sql .= "AND i.vehicle = :vehicle ";
			$params = array_merge($params, ['vehicle' => $this->section->vehicle]);
		endif; 
		if($this->section->model !== null):
			$sql .= "AND i.model = :model ";
			$params = array_merge($params, ['model' => $this->section->model]);
		endif; */	
			$sql .= " GROUP BY i.color ORDER BY name ";
		$q = $this->query($sql, $params);
		return $q;
	}

	public function hasFilters():void {$this->_withFilters = true;}

	public function setFilters(string $filter, array $values): Section{
		$this->_filters[$filter] = $values;
		return $this;
	}	
}