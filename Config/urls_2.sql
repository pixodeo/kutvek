select `m`.`id` AS `id`,
CASE WHEN m_w._type = 2 THEN (SELECT p_l10n.slug FROM page_l10ns p_l10n WHERE p_l10n.page = JSON_VALUE(m_w.attr, '$.page') AND p_l10n.l10n = l10n.id ) ELSE m_l10n.slug END AS 'slug',
`l10n`.`id` AS `l10n`,
m_w.website,
case 
	when `m_w`.`department_store` is not null then `m_w`.`department_store` 
	else (
		select `mw_2`.`department_store`
		from `menu_websites` `mw_2`
		LEFT JOIN menus m2 ON (m2.id = mw_2.menu AND m2.workspace = m.workspace)
		WHERE  mw_2.website = m_w.website 
		AND `mw_2`.`department_store` IS NOT NULL
		AND `m2`.`node_left` < `m`.`node_left` 
		AND `m2`.`node_right` > `m`.`node_right` 
		ORDER BY `m2`.`node_left` DESC LIMIT 1
	) 
end AS `department_store`,
NULL AS `behavior_id`,
NULL AS `behavior_name`,
m_w._type AS 'type_id',
LOWER(m_w_t.designation) AS 'type_name',
case 
	when `m_w`.`category` is not null then `m_w`.`category` 
	else (
		select `mw_3`.`category`
		from `menu_websites` `mw_3`
		LEFT JOIN menus m3 ON (m3.id = mw_3.menu AND m3.workspace = m.workspace)
		WHERE  mw_3.website = m_w.website 
		AND `mw_3`.`category` IS NOT NULL
		AND `m3`.`node_left` < `m`.`node_left` 
		AND `m3`.`node_right` > `m`.`node_right` 
		ORDER BY `m3`.`node_left` DESC LIMIT 1
	) 
end AS `category`
from `menu_l10ns` `m_l10n` 
left join `l10ns` `l10n` on(`l10n`.`id` = `m_l10n`.`l10n`) 
left join `menus` `m` on(`m`.`id` = `m_l10n`.`menu`)
left join menu_websites AS m_w ON m_w.menu = m.id
LEFT JOIN website_menu_types m_w_t ON (m_w_t.id = m_w._type)
union all 
select `i_l10n`.`item` AS `id`,
case when locate('.html',`i_l10n`.`item_slug`) > 0 then `i_l10n`.`item_slug` 
else concat_ws('-',`i_l10n`.`item_slug`,`i_l10n`.`item`) 
end AS `slug`,
`l10n`.`id` AS `l10n`,
`w`.`id` AS `website`,
NULL AS `department_store`,
`i`.`behavior` AS `behavior_id`,
lcase(`b`.`_type`) AS `behavior_name`,
NULL AS 'type_id',
'product' AS 'type_name',
NULL AS 'category'
from `item_l10ns` `i_l10n` 
left join `l10ns` `l10n` on(`l10n`.`id` = `i_l10n`.`l10n`) 
left join `items` `i` on(`i`.`id` = `i_l10n`.`item` and `i`.`id` is not null)
left join `behaviors` `b` on(`b`.`id` = `i`.`behavior`)
left join `domain_names` `w` on(`w`.`id` in (1,5));



#produits à afficher sur une section. préférence pour la requete 2  

#1
SELECT i.id
FROM items i
WHERE i.department IN (68) AND i.behavior IN (1,7)
AND EXISTS (SELECT i_s.item FROM item_stores i_s WHERE (i_s.item = i.id AND i_s.store = 1) AND i_s.status = 1);

#2
SELECT i.id
FROM items i
JOIN item_stores i_s ON (i_s.item = i.id AND i_s.store = 1)
WHERE i.department IN (68) AND i.behavior IN (1,7) AND i_s.status = 1;


select `m`.`id` AS `id`,`m_l10n`.`slug` AS `slug`,`l10n`.`id` AS `l10n`,`m_w`.`website` AS `website`,case when `m_w`.`department_store` is not null then `m_w`.`department_store` else (select `mw_2`.`department_store` from (`menu_websites` `mw_2` left join `menus` `m2` on(`m2`.`id` = `mw_2`.`menu` and `m2`.`workspace` = `m`.`workspace`)) where `mw_2`.`website` = `m_w`.`website` and `mw_2`.`department_store` is not null and `m2`.`node_left` < `m`.`node_left` and `m2`.`node_right` > `m`.`node_right` order by `m2`.`node_left` desc limit 1) end AS `department_store`,NULL AS `behavior_id`,NULL AS `behavior_name`,`m_w`.`_type` AS `type_id`,lcase(`m_w_t`.`designation`) AS `type_name`,case when `m_w`.`category` is not null then `m_w`.`category` else (select `mw_3`.`category` from (`menu_websites` `mw_3` left join `menus` `m3` on(`m3`.`id` = `mw_3`.`menu` and `m3`.`workspace` = `m`.`workspace`)) where `mw_3`.`website` = `m_w`.`website` and `mw_3`.`category` is not null and `m3`.`node_left` < `m`.`node_left` and `m3`.`node_right` > `m`.`node_right` order by `m3`.`node_left` desc limit 1) end AS `category` from ((((`menu_l10ns` `m_l10n` left join `l10ns` `l10n` on(`l10n`.`id` = `m_l10n`.`l10n`)) left join `menus` `m` on(`m`.`id` = `m_l10n`.`menu`)) left join `menu_websites` `m_w` on(`m_w`.`menu` = `m`.`id`)) left join `website_menu_types` `m_w_t` on(`m_w_t`.`id` = `m_w`.`_type`)) union all select `i_l10n`.`item` AS `id`,case when locate('.html',`i_l10n`.`item_slug`) > 0 then `i_l10n`.`item_slug` else concat_ws('-',`i_l10n`.`item_slug`,`i_l10n`.`item`) end AS `slug`,`l10n`.`id` AS `l10n`,`w`.`id` AS `website`,NULL AS `department_store`,`i`.`behavior` AS `behavior_id`,lcase(`b`.`_type`) AS `behavior_name`,NULL AS `type_id`,'product' AS `type_name`,NULL AS `category` from ((((`item_l10ns` `i_l10n` left join `l10ns` `l10n` on(`l10n`.`id` = `i_l10n`.`l10n`)) left join `items` `i` on(`i`.`id` = `i_l10n`.`item` and `i`.`id` is not null)) left join `behaviors` `b` on(`b`.`id` = `i`.`behavior`)) left join `domain_names` `w` on(`w`.`id` in (1,5)))