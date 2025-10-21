SELECT 
m_w_l10n.menu_website,
m.node_left,
m.node_right,
m.depth,
m.workspace,
m.category,
m_w.position,
m_w.obfuscated,
m_w.filters,
CASE 
WHEN m_w.department_store IS NOT NULL THEN m_w.department_store
ELSE (
SELECT 
s.department_store
FROM menus m2
JOIN menu_websites s ON s.menu = m2.id
WHERE m2.node_left <= m.node_left AND m2.node_right >= m.node_right AND m2.workspace = m.workspace
AND s.department_store IS NOT NULL
AND s.website = m_w.website
ORDER BY m2.node_left DESC
LIMIT 1
) 
END AS 'department_store',
m_w_l10n.slug,
m_w_l10n.designation,
m_w_l10n.name,
m_w_l10n.short_desc,
m_w_l10n.description,
m_w_l10n.features,
m_w_l10n.faq,
m_w_l10n.further_info,
m_w_l10n.meta_title,
m_w_l10n.meta_description,
m_w_l10n.cover,
d_n.name AS 'fqdn',
c_i.family,
c_i.brand,
c_i.model,
c_i.vehicle,
m.parent,
t.id AS 'type_id',
t.designation AS 'type_name',
m_w.menu,
m_w.website,
l10n.id AS 'l10n'
FROM menu_website_l10ns m_w_l10n
LEFT JOIN l10ns l10n ON l10n.id = m_w_l10n.l10n
LEFT JOIN menu_websites m_w ON m_w.id = m_w_l10n.menu_website
JOIN menus m ON m.id = m_w.menu
LEFT JOIN domain_names d_n ON d_n.id = m_w.website
LEFT JOIN website_menu_types t ON t.id = m_w._type
LEFT JOIN category_info c_i ON c_i.category = m.category 


/**
 * { item_description }
 */
/**
 * CASE WHEN m_w_l10n.designation IS NOT NULL THEN m_w_l10n.designation ELSE c_l10n.designation END AS 'designation',
CASE WHEN m_w_l10n.name IS NOT NULL THEN m_w_l10n.name ELSE c_l10n.breadcrumb END AS 'name',
CASE WHEN m_w_l10n.short_desc IS NOT NULL THEN m_w_l10n.short_desc ELSE c_l10n.short_desc END AS 'short_desc',
CASE WHEN m_w_l10n.description IS NOT NULL THEN m_w_l10n.description ELSE c_l10n.description END AS 'description',
CASE WHEN m_w_l10n.features IS NOT NULL THEN m_w_l10n.features ELSE c_l10n.features END AS 'features',
CASE WHEN m_w_l10n.faq IS NOT NULL THEN m_w_l10n.faq ELSE c_l10n.faq END AS 'faq',
CASE WHEN m_w_l10n.further_info IS NOT NULL THEN  m_w_l10n.further_info ELSE  c_l10n.further_info END AS 'further_info',
CASE WHEN m_w_l10n.meta_title IS NOT NULL THEN m_w_l10n.meta_title ELSE c_l10n.meta_title END AS 'meta_title',
CASE WHEN m_w_l10n.meta_description IS NOT NULL THEN m_w_l10n.meta_description ELSE c_l10n.meta_description END AS 'meta_description',
CASE WHEN  m_w_l10n.cover IS NOT NULL THEN m_w_l10n.cover ELSE c_l10n.cover END AS 'cover',

LEFT JOIN category_l10ns c_l10n ON (c_l10n.category = m.category AND c_l10n.l10n = l10n.id)

 */

