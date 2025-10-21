(select `o`.`id` AS `order`,
	oi.qty,
	oi.id AS 'item_id',
	oi.description,
CONCAT_WS(' ', u.firstname, UPPER(u.lastname)) AS 'username',
u.id AS 'userid',
`o`.`created` AS `created`,
year(`o`.`created`) AS `year`,
`o`.`date_paid` AS `date_paid`,
`o`.`workspace` AS `workspace_id`,
`wp`.`name` AS `workspace_name`,
`pcode`.`id` AS `code_id`,
ROUND((JSON_VALUE(oi.item_price, '$.product') + CASE WHEN (JSON_VALUE(oi.item_price, '$.finish') IS NOT NULL THEN JSON_VALUE(oi.item_price, '$.finish') * oi.qty ELSE 0 END) + JSON_VALUE(oi.item_price, '$.opts')),2) AS 'prix',
ROUND((JSON_VALUE(oi.item_price, '$.product') + CASE WHEN (JSON_VALUE(oi.item_price, '$.finish') IS NOT NULL THEN  JSON_VALUE(oi.item_price, '$.finish') * oi.qty ELSE 0 END) + JSON_VALUE(oi.item_price, '$.opts')) * (1 - (30/100)), 2) AS 'prix_remise',
case when `pcode`.`id` is not null then `pcode`.`code` else `o`.`com_bon_reduction` end AS `code_name` 
FROM order_item oi
LEFT JOIN `_order` `o`  ON o.id = oi.id_order
left join `promo_codes` `pcode` on(`pcode`.`id` = `o`.`promo_code`)
left join `workspaces` `wp` on(`wp`.`id` = `o`.`workspace`)
left JOIN user u ON u.id = o.id_user
WHERE o.workspace = 2
AND oi.task IS NOT NULL 
AND oi.status != 18
AND `o`.`promo_code` is not null 
or `o`.`com_bon_reduction` is not null
)
UNION ALL
(select `o`.`id` AS `order`,
	oi.qty,
	oi.id AS 'item_id',
	oi.description,
CONCAT_WS(' ', u.firstname, UPPER(u.lastname)) AS 'username',
u.id AS 'userid',
`o`.`created` AS `created`,
year(`o`.`created`) AS `year`,
`o`.`date_paid` AS `date_paid`,
`o`.`workspace` AS `workspace_id`,
`wp`.`name` AS `workspace_name`,
`pcode`.`id` AS `code_id`,
#{"finish":0,"product":89,"opts":0,"accessories":0}
ROUND((JSON_VALUE(oi.item_price, '$.product') + CASE WHEN (JSON_VALUE(oi.item_price, '$.finish') IS NOT NULL THEN JSON_VALUE(oi.item_price, '$.finish') * oi.qty ELSE 0 END) + JSON_VALUE(oi.item_price, '$.opts')),2) AS 'prix',
ROUND((JSON_VALUE(oi.item_price, '$.product') + CASE WHEN (JSON_VALUE(oi.item_price, '$.finish') IS NOT NULL THEN  JSON_VALUE(oi.item_price, '$.finish') * oi.qty ELSE 0 END) + JSON_VALUE(oi.item_price, '$.opts')) * (1 - (30/100)), 2) AS 'prix_remise',
case when `pcode`.`id` is not null then `pcode`.`code` else `o`.`com_bon_reduction` end AS `code_name` 
FROM order_item oi
LEFT JOIN `_order` `o`  ON o.id = oi.id_order
left join `promo_codes` `pcode` on(`pcode`.`id` = `o`.`promo_code`)
left join `workspaces` `wp` on(`wp`.`id` = `o`.`workspace`)
left JOIN user u ON u.id = o.id_user
WHERE o.workspace = 1
AND `o`.`promo_code` is not null 
or `o`.`com_bon_reduction` is not null
)