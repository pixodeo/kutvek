<?php
declare(strict_types=1);
namespace Domain\Table;

use Core\Database\Database;

class Graphic extends Product {
	
	public function __construct(protected Database $db, protected ?object $product){}	

	public function cardData(int $item_id){
		
		$this->setEntity('Cards\Graphic');
		$params = ['l10n_id' => $this->getL10nId(), 'item_id' => $item_id];
		$sql = "SELECT
		    i.id,  
			df.category,
			df_2.category AS 'df2',
			df.designation,
			df.full_designation,
			df_2.item_slug,
			l10n._prefix,
			d.designation AS 'design_name',
			c.designation AS 'color_name',
			v.fam_name AS 'family_name',
			v.brand_name,
			v.fullname AS 'vehicle_fullname',
			v.fam_slug AS 'family_slug',
			v.brand_slug,
			CONCAT_WS('-', v.model_slug, v.vehicle_slug) AS 'vehicle_slug',
			d.d_url AS 'design_slug',
			c.slug AS 'color_slug'
			FROM items i 
			JOIN graphic_kits g_k ON g_k.id = i.id
			LEFT JOIN l10ns l10n ON l10n.id = :l10n_id
			LEFT JOIN category_default_content df 
			    ON (df.l10n = l10n.id
			        AND df.category = (
			        SELECT c.id FROM categories node 
			        LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
			        LEFT JOIN category_default_content dfc ON (dfc.category = c.id AND dfc.l10n = l10n.id)
			        WHERE node.id = i.department
			        AND dfc.category IS NOT NULL
			        ORDER BY c.node_left DESC
			        LIMIT 1)
			    )
			LEFT JOIN category_default_content df_2 
			    ON (df_2.l10n = l10n.id
			        AND df_2.category = (
			        SELECT c.id FROM categories node 
			        LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
			        LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = l10n.id)
			        WHERE node.id = i.department
			        AND df.category IS NOT NULL
			        AND df.item_slug IS NOT NULL
			        ORDER BY c.node_left DESC
			        LIMIT 1)

			    )
			LEFT JOIN vue_vehicle_2 v ON (v.id = g_k.vehicle AND v.l10n = l10n.id)
			LEFT JOIN color_l10ns c ON (c.color = g_k.color AND c.l10n = l10n.id)
			LEFT JOIN vue_designs d ON d.id = g_k.design
			WHERE i.id = :item_id;";
			return $this->query($sql, $params, true);
	} 

	public function setProduct(object $product):void{$this->product = $product;}

	/**
	 * Infos supplémentaires pour un kit déco
	 */
	public function info(int $id){
		$sql = "
			SELECT gk.vehicle AS 'vehicle_id',
			(select JSON_OBJECT(
				'price', 
				CASE WHEN vp.currency = 3 THEN (vp.price * 1.20) ELSE vp.price END)          
            from vehicle_price_currencies  vp
            where (vp.vehicle = gk.vehicle)
            and vp.currency = cur.currency_id
            and vp.valid_since <= current_timestamp() 
            and (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until) 
            and vp.template_type = gk.kit_type
            ORDER BY vp.id DESC 
            LIMIT 1) AS 'price',
            gk.design AS 'design_id',
			design.designation AS 'design_name',
			gk.color AS 'color_id',
			color.designation AS 'color_name',
			gk.kit_type AS 'item_type',
			i_l10n.short_desc AS 'l10n_short_desc',
			i_l10n.description AS 'l10n_description',
			i_l10n.meta_title AS 'l10n_meta_title',
			i_l10n.meta_description AS 'l10n_meta_description',            
			i_l10n.features AS 'l10n_features',
			i_l10n.composition_care AS 'l10n_composition_care',
			l10ns.id AS 'l10n_id',
            JSON_OBJECT(
				'finish', 
				CASE         
				WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.finish') IS  NULL THEN JSON_VALUE(parent.attr, '$.finish') 
				ELSE JSON_VALUE(gk.attr, '$.finish') END,
				'switch',
				CASE 
				WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.switch') IS  NULL THEN JSON_VALUE(parent.attr, '$.switch') 
				ELSE JSON_VALUE(gk.attr, '$.switch') 
				END,
				'opts',
				CASE 
				WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.opts') IS  NULL THEN JSON_VALUE(parent.attr, '$.opts') 
				ELSE JSON_VALUE(gk.attr, '$.opts') 
				END,
				'door_stickers', JSON_VALUE(gk.attr, '$.door_stickers'),
				'seat_cover', JSON_VALUE(gk.attr, '$.seat_cover'),
				'rim_sticker', JSON_VALUE(gk.attr, '$.rim_sticker'),
				'chrome',
				CASE 
				# si on a un parent, un null pour chrome
				WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.chrome') IS  NULL THEN JSON_VALUE(parent.attr, '$.chrome') 
				ELSE JSON_VALUE(gk.attr, '$.chrome') 
				END,
				'plastics',JSON_VALUE(gk.attr, '$.plastics'),
				'sled_color', JSON_VALUE(v.attr, '$.sled_color'),
				'mini_plates',
				CASE         
				WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.mini_plates') IS  NULL THEN JSON_VALUE(parent.attr, '$.mini_plates') 
				ELSE JSON_VALUE(gk.attr, '$.mini_plates') END,			
				'hubs_stickers', 
				CASE         
				WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.hubs_stickers') IS  NULL THEN JSON_VALUE(parent.attr, '$.hubs_stickers') 
				ELSE JSON_VALUE(gk.attr, '$.hubs_stickers') END,			
				'tunnel', JSON_VALUE(v.attr, '$.tunnel'),
				'turbo', JSON_VALUE(v.attr, '$.turbo'),
				'cylinder', JSON_VALUE(v.attr, '$.cylinder'),
				'starter', JSON_VALUE(v.attr, '$.starter'),
				'reverse', JSON_VALUE(v.attr, '$.reverse'),
				'wide', JSON_VALUE(v.attr, '$.wide')            
			) AS 'attributes',
			JSON_OBJECT(
            'id', v.id, 
            'designation', v.name,
            'full_designation', v.fullname,
            'family', JSON_OBJECT('id', v.universe, 'designation', v.fam_name),
            'brand', JSON_OBJECT('id', v.brand, 'designation', v.brand_name),
            'model', JSON_OBJECT('id', v.model, 'designation', v.model_name),
            'sponsors', JSON_OBJECT('template', v.template_sponsors, 'quota', v.nb_sponsor),
            'years', JSON_ARRAY()          
        	) AS 'vehicle', 
			v.name AS 'vehicle_designation',
			v.universe AS 'family_id'
            FROM graphic_kits gk
            JOIN items i ON i.id = gk.id
            LEFT JOIN currency cur ON (cur.currency_id = :currency_id)
            LEFT JOIN l10ns ON l10ns.id = :l10n_id
            LEFT JOIN graphic_kits AS parent ON parent.id = i.parent
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10ns.id)
            LEFT JOIN vue_vehicle_2 AS v ON (v.id = gk.vehicle AND v.l10n = l10ns.id)
            LEFT JOIN vue_designs design ON design.id = gk.design 
			LEFT JOIN vue_colors color ON (color.id = gk.color AND color.l10n = l10ns.id)  
            WHERE gk.id = :id;
		";

		$infos = $this->query($sql, ['l10n_id' => $this->getL10nId(),   'id' => $id, 'currency_id' => $this->getCurrencyId()], true);
		foreach($infos as $property => $value):
			$this->product->{$property} = $value;
		endforeach;
		$this->product->years = $this->years();
		return $this->product;
	}

	public function product(int $id){
		$this->setEntity('Graphic');
		$sql = "SELECT i.id,
		i.department,            
		i.weight,
		i.license,
		i.parent,
		p.vehicle,
		v.fullname AS 'vehicle_fullname',
		v.id AS 'vehicle_id',
		v.universe AS 'family_id',
		p.design AS 'design_id',
		design.designation AS 'design_name',
		p.color AS 'color_id',
		color.designation AS 'color_name',
		p.kit_type AS 'item_type',
		i.is_new,
		i.behavior AS 'behavior_id',
		b._type AS 'behavior_type',
		CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'l10n_designation',
		i_l10n.short_desc AS 'l10n_short_desc',
		i_l10n.description AS 'l10n_description',
		i_l10n.meta_title AS 'l10n_meta_title',
		i_l10n.meta_description AS 'l10n_meta_description',            
		i_l10n.features AS 'l10n_features',
		i_l10n.composition_care AS 'l10n_composition_care',
		df.full_designation AS 'df_full_designation',
		df.short_desc AS 'df_short_desc',
		df.description AS 'df_description',		
		JSON_OBJECT(
			'finish', 
			CASE         
			WHEN i.parent IS NOT NULL AND JSON_VALUE(p.attr, '$.finish') IS  NULL THEN JSON_VALUE(parent.attr, '$.finish') 
			ELSE JSON_VALUE(p.attr, '$.finish') END,
			'switch',
			CASE 
			WHEN i.parent IS NOT NULL AND JSON_VALUE(p.attr, '$.switch') IS  NULL THEN JSON_VALUE(parent.attr, '$.switch') 
			ELSE JSON_VALUE(p.attr, '$.switch') 
			END,
			'opts',
			CASE 
			WHEN i.parent IS NOT NULL AND JSON_VALUE(p.attr, '$.opts') IS  NULL THEN JSON_VALUE(parent.attr, '$.opts') 
			ELSE JSON_VALUE(p.attr, '$.opts') 
			END,
			'door_stickers', JSON_VALUE(p.attr, '$.door_stickers'),
			'seat_cover', JSON_VALUE(p.attr, '$.seat_cover'),
			'rim_sticker', JSON_VALUE(p.attr, '$.rim_sticker'),
			'chrome',
			CASE 
			# si on a un parent, un null pour chrome
			WHEN i.parent IS NOT NULL AND JSON_VALUE(p.attr, '$.chrome') IS  NULL THEN JSON_VALUE(parent.attr, '$.chrome') 
			ELSE JSON_VALUE(p.attr, '$.chrome') 
			END,
			'plastics',JSON_VALUE(p.attr, '$.plastics'),
			'sled_color', JSON_VALUE(v.attr, '$.sled_color'),
			'mini_plates',
			CASE         
			WHEN i.parent IS NOT NULL AND JSON_VALUE(p.attr, '$.mini_plates') IS  NULL THEN JSON_VALUE(parent.attr, '$.mini_plates') 
			ELSE JSON_VALUE(p.attr, '$.mini_plates') END,			
			'hubs_stickers', 
			CASE         
			WHEN i.parent IS NOT NULL AND JSON_VALUE(p.attr, '$.hubs_stickers') IS  NULL THEN JSON_VALUE(parent.attr, '$.hubs_stickers') 
			ELSE JSON_VALUE(p.attr, '$.hubs_stickers') END,			
			'tunnel', JSON_VALUE(v.attr, '$.tunnel'),
			'turbo', JSON_VALUE(v.attr, '$.turbo'),
			'cylinder', JSON_VALUE(v.attr, '$.cylinder'),
			'starter', JSON_VALUE(v.attr, '$.starter'),
			'reverse', JSON_VALUE(v.attr, '$.reverse'),
			'wide', JSON_VALUE(v.attr, '$.wide')            
		) AS 'attributes',
		JSON_OBJECT(
			'id', v.id,
			'name', v.name
		) AS 'vehicle'
		FROM graphic_kits p 
		JOIN items i ON i.id = p.id 		
		LEFT JOIN graphic_kits AS parent ON parent.id = i.parent
		LEFT JOIN l10ns ON l10ns.id = :l10n_id
		LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = 1)
		LEFT JOIN vue_vehicle_2 AS v ON (v.id = p.vehicle AND v.l10n = 1)
		LEFT JOIN vue_designs design ON design.id = p.design 
		LEFT JOIN vue_colors color ON (color.id = p.color AND color.l10n = 1)            
		LEFT JOIN category_default_content df ON (
			df.l10n = 1 
			AND df.category = (
				SELECT c.id
				FROM categories node 
				LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
				LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = 1)
				WHERE node.id = i.department
				AND df.category IS NOT NULL
				ORDER BY c.node_left DESC
				LIMIT 1
				)
			)            
		LEFT JOIN behaviors b ON b.id = i.behavior
		WHERE p.id = :id 
		";
		$q = $this->query($sql, ['id' => $id, 'l10n_id' => $this->getL10nId()], true);
		if($q):            
			$q->files = $this->files($id); 
			$q->years = $this->years((int)$q->vehicle_id);          
		else:
			$q->files = [];
			$q->years = [];
		endif;
		return $q;
	}

	public function years():array {
		$params = [
			'vehicle_id'	=> $this->product->vehicle_id, 
			'design_id' 	=> $this->product->design_id,
			'color_id'		=> $this->product->color_id,
			'l10n_id' 		=> $this->getL10nId(), 
			'cur' 			=> $this->getCurrencyId()
		];
		$this->setEntity('YearType');
		$sql = "SELECT
		m_t.kit_type AS 'id',
		v_m.id AS 'year_id',
		:design_id AS 'design_id',
		:color_id AS 'color_id',
		CASE WHEN v_m.finish = '-' THEN CONCAT(v_m.begin, v_m.finish) ELSE CONCAT_WS('-', v_m.begin, v_m.finish) END AS 'year',
		(SELECT 
		JSON_OBJECT(
		'price_id', vp.id,
		'cost', vp.price,
		'fluo_cost', vp.fluo_printed		
		)
		FROM vehicle_price_currencies vp
		WHERE (vp.vehicle = v_m.vehicle) 
		AND vp.template_type = m_t.kit_type 
		AND vp.currency = cur.currency_id
		and vp.valid_since <= current_timestamp() 
		AND (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)            
		ORDER BY vp.id DESC 
		LIMIT 1
		) AS 'price',
		(SELECT 
		JSON_OBJECT(
		'price_id', vp.id,
		'cost', vp.price,
		'fluo_cost', vp.fluo_printed		
		)
		FROM vehicle_price_currencies vp
		WHERE (vp.template = v_m.id) 
		AND vp.template_type = m_t.kit_type 
		AND vp.currency = cur.currency_id
		and vp.valid_since <= current_timestamp() 
		AND (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)            
		ORDER BY vp.id DESC 
		LIMIT 1
		) AS 'price_template',		
		p_type.designation,
		p_type.description,
		cur.currency_lib AS 'currency_code'
		FROM vehicle_millesims v_m
		JOIN vehicle  v ON v.id = v_m.vehicle
		JOIN l10ns ON l10ns.id = :l10n_id
		JOIN currency cur ON cur.currency_id = :cur
		JOIN vehicle_millesim_type m_t ON m_t.millesim = v_m.id
		JOIN vue_kit_types p_type ON (p_type.id = m_t.kit_type AND p_type.family = v.universe AND p_type.l10n = l10ns.id)
		WHERE v_m.vehicle = :vehicle_id 
		AND v_m.type = 'fairing'
		ORDER BY v_m.begin DESC, p_type.position ASC;";
		return $this->query($sql,$params);
	}

	public function yearKitTypes(int $year_id){
		$this->setEntity('YearType');
		$params = [
			'year_id'	=> $year_id,		
			'l10n_id' 		=> $this->getL10nId(), 
			'cur' 			=> $this->getCurrencyId()
		];
		$sql = "SELECT
		m_t.kit_type AS 'id',
		v_m.id AS 'year_id',		
		CASE WHEN v_m.finish = '-' THEN CONCAT(v_m.begin, v_m.finish) ELSE CONCAT_WS('-', v_m.begin, v_m.finish) END AS 'year',
		(SELECT 
		JSON_OBJECT(
		'price_id', vp.id,
		'cost', vp.price,
		'fluo_cost', vp.fluo_printed		
		)
		FROM vehicle_price_currencies vp
		WHERE (vp.vehicle = v_m.vehicle) 
		AND vp.template_type = m_t.kit_type 
		AND vp.currency = cur.currency_id
		and vp.valid_since <= current_timestamp() 
		AND (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)            
		ORDER BY vp.id DESC 
		LIMIT 1
		) AS 'price',
		(SELECT 
		JSON_OBJECT(
		'price_id', vp.id,
		'cost', vp.price,
		'fluo_cost', vp.fluo_printed		
		)
		FROM vehicle_price_currencies vp
		WHERE (vp.template = v_m.id) 
		AND vp.template_type = m_t.kit_type 
		AND vp.currency = cur.currency_id
		and vp.valid_since <= current_timestamp() 
		AND (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)            
		ORDER BY vp.id DESC 
		LIMIT 1
		) AS 'price_template',		
		p_type.designation,
		p_type.description,
		cur.currency_lib AS 'currency_code'
		FROM vehicle_millesims v_m
		JOIN vehicle  v ON v.id = v_m.vehicle
		JOIN l10ns ON l10ns.id = :l10n_id
		JOIN currency cur ON cur.currency_id = :cur
		JOIN vehicle_millesim_type m_t ON m_t.millesim = v_m.id
		JOIN vue_kit_types p_type ON (p_type.id = m_t.kit_type AND p_type.family = v.universe AND p_type.l10n = l10ns.id)
		WHERE v_m.id = :year_id
		ORDER BY p_type.position ASC;";
		return $this->query($sql,$params);
	}

	public function graphicYears($vehicle_id, string $sku = ''){
		$parts = explode('.', $sku);
		list($design_id, $color_id) = $parts;
		$params = [
			'vehicle_id'	=> $vehicle_id, 
			'design_id' 	=> $design_id,
			'color_id'		=> $color_id,
			'l10n_id' 		=> $this->getL10nId(), 
			'cur' 			=> $this->getCurrencyId()
		];
		$this->setEntity('YearType');
		$sql = "SELECT
		m_t.kit_type AS 'id',
		v_m.id AS 'year_id',
		:design_id AS 'design_id',
		:color_id AS 'color_id',
		CASE WHEN v_m.finish = '-' THEN CONCAT(v_m.begin, v_m.finish) ELSE CONCAT_WS('-', v_m.begin, v_m.finish) END AS 'year',
		(SELECT 
		JSON_OBJECT(
		'price_id', vp.id,
		'cost', vp.price,
		'fluo_cost', vp.fluo_printed		
		)
		FROM vehicle_price_currencies vp
		WHERE (vp.vehicle = v_m.vehicle) 
		AND vp.template_type = m_t.kit_type 
		AND vp.currency = cur.currency_id
		and vp.valid_since <= current_timestamp() 
		AND (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)            
		ORDER BY vp.id DESC 
		LIMIT 1
		) AS 'price',
		(SELECT 
		JSON_OBJECT(
		'price_id', vp.id,
		'cost', vp.price,
		'fluo_cost', vp.fluo_printed		
		)
		FROM vehicle_price_currencies vp
		WHERE (vp.template = v_m.id) 
		AND vp.template_type = m_t.kit_type 
		AND vp.currency = cur.currency_id
		and vp.valid_since <= current_timestamp() 
		AND (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)            
		ORDER BY vp.id DESC 
		LIMIT 1
		) AS 'price_template',		
		p_type.designation,
		p_type.description,
		cur.currency_lib AS 'currency_code'
		FROM vehicle_millesims v_m
		JOIN vehicle  v ON v.id = v_m.vehicle
		JOIN l10ns ON l10ns.id = :l10n_id
		JOIN currency cur ON cur.currency_id = :cur
		JOIN vehicle_millesim_type m_t ON m_t.millesim = v_m.id
		JOIN vue_kit_types p_type ON (p_type.id = m_t.kit_type AND p_type.family = v.universe AND p_type.l10n = l10ns.id)
		WHERE v_m.vehicle = :vehicle_id 
		AND v_m.type = 'fairing'
		ORDER BY v_m.begin DESC, p_type.position ASC;";
		return $this->query($sql,$params);
	}

	/**
	 * A refaire 
	 *
	 * @param      int     $sibling  The sibling
	 * @param      int     $item_id  The item identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function vehicles(int $sibling, int $item_id){
		$this->setEntity(null);
		$sql = " SELECT
		sibling.id,
		sibling.name,
		(SELECT gk.id FROM graphic_kits gk WHERE gk.vehicle = sibling.id AND gk.design = g.design AND gk.color = g.color AND gk.kit_type = g.kit_type) AS 'item'
		FROM vue_vehicle_2 v
		LEFT JOIN graphic_kits g ON g.id = :pid
		LEFT JOIN vue_vehicle_2 sibling ON (sibling.universe = v.universe AND sibling.brand = v.brand AND sibling.l10n = v.l10n AND sibling.id != v.id)
		WHERE v.id = :vid
		AND v.l10n = 1
		AND EXISTS (SELECT v_m.id FROM vehicle_millesims v_m WHERE  v_m.vehicle = sibling.id AND v_m.type = 'fairing' LIMIT 1)

		ORDER BY sibling.model_name, sibling.version;
		";
		return $this->query($sql, ['vid' => $sibling, 'pid' => $item_id]);
	}    

	/**
	 * Déclinaisons produit ici kit déco 
	 *
	 * @param      <type>  $product  The product
	 */
	public function siblings($id):array{
		$this->setEntity('Graphic');
		$sql = "SELECT c.id,
		c.department,
		c._table,
		c.weight,
		c.license,
		c.parent,
		c.vehicle,
		c.design AS 'design_id',
		design.designation AS 'design_name',
		c.color AS 'color_id',
		color.designation AS 'color_name',
		c.kit_type,
		c.is_new,
		c.behavior_id,
		b._type AS 'behavior_type',
		i_l10n.full_designation,
		df.full_designation AS 'df_full_designation',
		v.fullname AS 'vehicle_fullname',
		CONCAT_WS(' ', v.brand_name, v.name) AS 'vehicle_name',
		f.url AS 'cover',
		JSON_OBJECT(
			'finish', 
			CASE         
			WHEN c.parent IS NOT NULL AND JSON_VALUE(c.attr, '$.finish') IS  NULL THEN JSON_VALUE(parent.attr, '$.finish') 
			ELSE JSON_VALUE(c.attr, '$.finish') END,
			'switch',
			CASE 
			WHEN c.parent IS NOT NULL AND JSON_VALUE(c.attr, '$.switch') IS  NULL THEN JSON_VALUE(parent.attr, '$.switch') 
			ELSE JSON_VALUE(c.attr, '$.switch') 
			END,
			'opts',
			CASE 
			WHEN c.parent IS NOT NULL AND JSON_VALUE(c.attr, '$.opts') IS  NULL THEN JSON_VALUE(parent.attr, '$.opts') 
			ELSE JSON_VALUE(c.attr, '$.opts') 
			END,
			'door_stickers', JSON_VALUE(c.attr, '$.door_stickers'),
			'seat_cover', JSON_VALUE(c.attr, '$.seat_cover'),
			'rim_sticker', JSON_VALUE(c.attr, '$.rim_sticker'),
			'chrome',
			CASE 
			# si on a un parent, un null pour chrome
			WHEN c.parent IS NOT NULL AND JSON_VALUE(c.attr, '$.chrome') IS  NULL THEN JSON_VALUE(parent.attr, '$.chrome') 
			ELSE JSON_VALUE(c.attr, '$.chrome') 
			END,
			'plastics',JSON_VALUE(c.attr, '$.plastics'),
			'mini_plates', JSON_VALUE(c.attr, '$.mini_plates'),
			'hubs_stickers', JSON_VALUE(c.attr, '$.hubs_stickers'),
			'sled_color', JSON_VALUE(v.attr, '$.sled_color'),
			'tunnel', JSON_VALUE(v.attr, '$.tunnel'),
			'turbo', JSON_VALUE(v.attr, '$.turbo'),
			'cylinder', JSON_VALUE(v.attr, '$.cylinder'),
			'starter', JSON_VALUE(v.attr, '$.starter'),
			'reverse', JSON_VALUE(v.attr, '$.reverse'),
			'wide', JSON_VALUE(v.attr, '$.wide')            
			) AS 'attributes',
		JSON_OBJECT(
			'id', v.id,
			'name', v.name
			) AS 'vehicle'
		FROM catalog c 
		LEFT JOIN catalog AS parent ON parent.id = c.parent
		LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = c.id AND i_l10n.l10n = 1)
		LEFT JOIN vue_vehicle_2 AS v ON (v.id = c.vehicle AND v.l10n = 1)
		LEFT JOIN vue_designs design ON design.id = c.design 
		LEFT JOIN vue_colors color ON (color.id = c.color AND color.l10n = 1)       
		LEFT JOIN category_default_content df ON (
			df.l10n = 1 
			AND df.category = (
				SELECT c.id
				FROM categories node 
				LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
				LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = 1)
				WHERE node.id = c.department
				AND df.category IS NOT NULL
				ORDER BY c.node_left DESC
				LIMIT 1
				)
			)
		LEFT JOIN vue_files f ON (f.product = c.id AND f.cover = 'on')
		LEFT JOIN behaviors b ON b.id = c.behavior_id
		WHERE c.parent = :id 
		";
		$siblings = $this->query($sql, ['id' => $id]);
		if(count($siblings)>0):
			foreach ($siblings as $sibling) {
				$sibling->in_stores = $this->item_stores($sibling->id);
				$sibling->files = $this->files($sibling->id);
			}
		endif;
		return $siblings;
	} 



	// Rajouter l'uri
	public function millesims(int $vehicle)
	{
		$this->setEntity(null);
		$sql = "SELECT vm.id,
		CASE
		WHEN vm.begin > YEAR(NOW()) THEN vm.begin
		WHEN vm.finish = '-' AND YEAR(DATE_ADD(NOW(), INTERVAL +5 MONTH)) = vm.begin THEN vm.begin
		WHEN vm.finish = '-' THEN CONCAT_WS('-', vm.begin, YEAR(DATE_ADD(NOW(), INTERVAL +5 MONTH)))
		WHEN vm.finish IS NULL THEN vm.begin
		ELSE CONCAT_WS('-', vm.begin, vm.finish)
		END AS 'name',
		CONCAT('/api/millesims/', vm.id ,'/types') AS 'uri',
		vm.type
		FROM vehicle_millesims AS vm
		WHERE vm.vehicle = :vehicle
		
		ORDER BY vm.begin DESC";
		$millesims = $this->query($sql, ['vehicle' => $vehicle]);
		array_walk($millesims, function($y){$y->types = $this->yearTypes($y->id);});        
		// récupérer les types de kit disponibles pour tous les gabarits
		/*foreach($millesims as $millesim) {
			if(property_exists($millesim, 'types')) continue;
			$millesim->types = $this->yearKitTypes($millesim->id);
		}*/
		return $millesims;        
	}

	public function yearTypes(int $millesim){
		$this->setEntity('YearTypeEntity');
		$types = $this->query("SELECT 
			g_kt.id,
			g_kt.title,
			(SELECT CASE WHEN vp.currency = 3 THEN (vp.price * 1.20) ELSE vp.price END AS 'price'
				FROM vehicle_price_currencies vp 
				WHERE vp.vehicle = m.vehicle 
				AND vp.template_type = m_t.kit_type 
				AND vp.currency = cur.currency_id
				AND vp.valid_since <= current_timestamp()
				AND (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)
				AND vp.template IS NULL
				ORDER BY vp.id DESC 
				LIMIT 1) AS 'vehicle_price',
			(SELECT vmp.price FROM vehicle_price_currencies vmp 
				WHERE vmp.template = m.id 
				AND vmp.template_type = m_t.kit_type 
				AND vmp.currency = cur.currency_id
				AND vmp.valid_since <= current_timestamp()
				AND (vmp.valid_until IS NULL OR current_timestamp() <= vmp.valid_until)
				ORDER BY vmp.id DESC 
				LIMIT 1) AS 'template_price',
			l10ns._locale AS 'l10n',
			cur.currency_lib AS 'currency_code',
			country.vat
			FROM millesim_types AS m_t
			JOIN l10ns ON l10ns.id = :l10n
			JOIN graphic_kit_types AS g_kt ON g_kt.id = m_t.kit_type
			JOIN vehicle_millesims AS m ON m.id =  m_t.millesim
			JOIN vehicle AS v ON v.id = m.vehicle
			LEFT JOIN country ON country.country_iso = :country
			LEFT JOIN currency AS cur ON cur.currency_lib = :cur
			WHERE m_t.millesim = :millesim;
			", 
			['millesim' => $millesim, 'cur' => $this->getCurrencyCode(), 'l10n' => $this->getL10nId(), 'country' => $this->getCountryCode()]);
		$this->setEntity(null);
		return $types;
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
		CASE WHEN  opt.input_name = 'switch_color' THEN 'radio' ELSE opt.input_type END AS 'input_type',
		CASE WHEN opt.modal = 'sponsors-only' THEN 'plate-sponsors' ELSE opt.modal END AS 'modal',
		CASE WHEN pc.currency != 1 THEN pc.price*1.20 ELSE pc.price END AS 'value',
		CASE WHEN pc.currency != 1 THEN pc.price*1.20 ELSE pc.price END AS 'price', 
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
		$params = array_merge($data->values, ['l10n' => $this->getL10nId(), 'cur'=> $this->getCurrencyCode(), 'country' => $this->getCountryCode()]);
		return $this->query($sql, $params);
	}	
}