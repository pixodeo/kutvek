<?php
declare(strict_types=1);
namespace Domain\Table;

use Core\Domain\Table;

class Vehicle extends  Table {
	/**
	 * On renvoi les version / types de kit (gabarit) / produit disponibles sur une année
	 *
	 * @param      int    $year_id
	 *
	 * @return     array 
	 */
	public function yearTypes(int $year_id): array {
		$this->setEntity('YearType');
		$sql = "SELECT 
		v_m.id AS 'year_id',
		CASE WHEN v_m.finish = '-' THEN CONCAT(v_m.begin, v_m.finish) ELSE CONCAT_WS('-', v_m.begin, v_m.finish) END AS 'year',
		(SELECT 
		JSON_OBJECT(
		'price_id', vp.id,
		'cost', vp.price,
		'fluo_cost', vp.fluo_printed,
		'currency_id', vp.currency
		)
		FROM `vehicle_price_currencies` vp
		WHERE (vp.`vehicle` = v_m.vehicle) 
		AND vp.`template_type` = m_t.kit_type 
		AND vp.`currency` = '1'
		and vp.valid_since <= current_timestamp() 
		AND (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)            
		ORDER BY vp.id DESC 
		LIMIT 1
		) AS 'price',
		(SELECT 
		JSON_OBJECT(
		'price_id', vp.id,
		'cost', vp.price,
		'fluo_cost', vp.fluo_printed,
		'currency_id', vp.currency
		)
		FROM `vehicle_price_currencies` vp
		WHERE (vp.template = v_m.id) 
		AND vp.`template_type` = m_t.kit_type 
		AND vp.`currency` = '1'
		and vp.valid_since <= current_timestamp() 
		AND (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)            
		ORDER BY vp.id DESC 
		LIMIT 1
		) AS 'price_template',
		m_t.kit_type AS 'type_id',
		p_type.designation,
		p_type.description
		FROM `vehicle_millesims` v_m
		JOIN vehicle  v ON v.id = v_m.vehicle
		JOIN l10ns ON l10ns.id = 1
		JOIN vehicle_millesim_type m_t ON m_t.millesim = v_m.id
		JOIN vue_kit_types p_type ON (p_type.id = m_t.kit_type AND p_type.family = v.universe AND p_type.l10n = l10ns.id)
		WHERE v_m.id = :year_id 
		ORDER BY v_m.`begin` DESC;
		";
		return $this->query($sql, ['year_id'=>$year_id]);
	}
}