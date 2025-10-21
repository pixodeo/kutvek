SELECT 
m_i.`id`,
m.id AS 'menu_id',
(SELECT m_store.store_department
FROM menus m_store
WHERE m_store.node_left <= m.node_left 
AND m_store.node_right >= m.node_right 
AND m_store.workspace = m.workspace
AND m_store.website = m.website 
AND  m_store.store_department IS NOT NULL
ORDER BY m_store.node_left DESC
LIMIT 1) AS 'store_id',
m_i.`name`, 
m_i.`slug`, 
m_i.`display`, 
m_i.`active`, 
m_i.category,
CASE WHEN m.department IS NOT NULL THEN m.department ELSE m_i.`department` END AS 'department',
CASE WHEN m_i.page IS NOT NULL THEN (SELECT p_l10n.slug FROM page_l10ns p_l10n WHERE p_l10n.page = m_i.page AND p_l10n.l10n = l10n.id) END AS 'page_slug',
m_i.`l10n`,
(SELECT JSON_ARRAYAGG(m_types.product_type) FROM menu_product_types m_types  WHERE m_types.menu = m.id) AS 'behaviors',
JSON_OBJECT(
'id', m_t.id, 
'name', m_t.designation,
'reference', CASE WHEN m_t.designation = 'page' THEN JSON_VALUE(m.attr, '$.page') END
) AS '_type',
NULL AS `status_code`,
NULL AS `location`,
0 AS `redirection`,
m.website,
m.workspace,
m.node_left,
m.node_right,
m.depth,
m.attr,
m.filters
FROM `menu_items` m_i
JOIN menus m ON m.id = m_i.menu
LEFT JOIN  website_menu_types  m_t ON m_t.id = m._type
LEFT JOIN l10ns l10n ON l10n.id = m_i.l10n;



select `m_i`.`id` AS `id`,
`m`.`id` AS `menu_id`,
(select `m_store`.`store_department` from `menus` `m_store` where `m_store`.`node_left` <= `m`.`node_left` and `m_store`.`node_right` >= `m`.`node_right` and `m_store`.`workspace` = `m`.`workspace` and `m_store`.`website` = `m`.`website` and `m_store`.`store_department` is not null order by `m_store`.`node_left` desc limit 1) AS `store_id`,
`m_i`.`name` AS `name`,
`m_i`.`slug` AS `slug`,
`m_i`.`display` AS `display`,
`m_i`.`active` AS `active`,
`m_i`.`category` AS `category`,
case when `m`.`department` is not null then `m`.`department` else `m_i`.`department` end AS `department`,
case 
when `m_i`.`page` is not null then (select `p_l10n`.`slug` from `page_l10ns` `p_l10n` where `p_l10n`.`page` = `m_i`.`page` and `p_l10n`.`l10n` = `l10n`.`id`)
when json_value(`m_w`.`attr`,'$.page') is not null then (select `p_l10n`.`slug` from `page_l10ns` `p_l10n` where `p_l10n`.`page` = json_value(`m_w`.`attr`,'$.page') and `p_l10n`.`l10n` = `l10n`.`id`)
end AS `page_slug`,
`m_i`.`l10n` AS `l10n`,
(select json_arrayagg(`m_types`.`product_type`) from `menu_product_types` `m_types` where `m_types`.`menu` = `m`.`id`) AS `behaviors`,
json_object('id',`m_t`.`id`,'name',`m_t`.`designation`,'reference',case when `m_t`.`designation` = 'page' then json_value(`m_w`.`attr`,'$.page') end) AS `_type`,
NULL AS `status_code`,
NULL AS `location`,
0 AS `redirection`,
`m`.`website` AS `website`,
`m`.`workspace` AS `workspace`,
`m`.`node_left` AS `node_left`,
`m`.`node_right` AS `node_right`,
`m`.`depth` AS `depth`,
json_object(
	'family', case when json_value(m_w.attr, '$.family') is null then m_i.family else json_value(m_w.attr, '$.family') end,
	'brand', case when json_value(m_w.attr, '$.brand') is null then m_i.brand else json_value(m_w.attr, '$.brand') end,
	'vehicle', case when json_value(m_w.attr, '$.vehicle') is null then m_i.vehicle else json_value(m_w.attr, '$.vehicle') end,
	'model', case when json_value(m_w.attr, '$.model') is null then m_i.model else json_value(m_w.attr, '$.model') end,
	'page', case when json_value(m_w.attr, '$.page') is null then m_i.page else json_value(m_w.attr, '$.page') end
) AS 'attr',

`m_w`.`filters` AS `filters` 
from `menu_items` `m_i` 
join `menus` `m` on(`m`.`id` = `m_i`.`menu`)
left join menu_websites m_w  ON m_w.menu = m.id
left join `website_menu_types` `m_t` on(`m_t`.`id` = `m_w`.`_type`) 
left join `l10ns` `l10n` on(`l10n`.`id` = `m_i`.`l10n`)