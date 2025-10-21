<?php
declare(strict_types=1);
namespace Domain\Table;
use Core\Domain\Table;

class Sitemap extends Table  {

	public function categories(){
		$sql = "SELECT
		w_m.id,
		m_i.page,
		m_i.department,
		m_i.name,
		CASE 
			WHEN r.location IS NOT NULL THEN r.location 
			WHEN m_i.page IS NOT NULL THEN p.slug 
			ELSE m_i.slug 
		END AS 'slug',
		r.status_code,
		r.location
		FROM `website_menus` w_m 
		JOIN menu_items m_i ON (m_i.menu = w_m.id AND m_i.l10n = :l10n_id)
		LEFT JOIN category_l10ns cat ON (cat.category = m_i.department AND cat.l10n = m_i.l10n)
		LEFT JOIN page_l10ns p ON (p.page = m_i.page AND p.l10n = m_i.l10n)
		LEFT JOIN redirections r ON (
			r.slug = CASE WHEN m_i.page IS NOT NULL THEN p.slug ELSE m_i.slug END 
			AND r.l10n = m_i.l10n
		)
		WHERE w_m.website = :ws
		AND m_i.active = 1
		ORDER BY w_m.node_left;
		";
		return $this->query($sql, ['ws' => 5, 'l10n_id' => 1]);
	}
}