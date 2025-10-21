SELECT
i.id AS 'item', 
i.behavior,
i.department,
i.parent,
v.universe AS 'family',
v.brand AS 'brand',
v.model AS 'model',
v.id AS 'vehicle',
k.design,
k.color
FROM graphic_kits k
JOIN items i ON i.id = k.id 
JOIN vehicle v ON v.id = k.vehicle
WHERE v.universe = 1
UNION ALL
SELECT 
i.id AS 'item', 
i.behavior,
i.department,
i.parent,
CASE WHEN k.vehicle IS NOT NULL THEN v.universe  ELSE k.family END AS 'family',
CASE WHEN k.vehicle IS NOT NULL THEN v.brand ELSE k.brand END AS 'brand',
CASE WHEN k.vehicle IS NOT NULL THEN v.model  ELSE k.model END AS 'model',
v.id AS 'vehicle',
k.design,
k.color
FROM products_old k 
JOIN items i ON i.id = k.id 
LEFT JOIN vehicle v ON v.id = k.vehicle
UNION ALL 
SELECT
i.id AS 'item',
i.behavior,
i.department,
i.parent,
v.universe AS 'family',
v.brand AS 'brand',
v.model AS 'model',
v.id AS 'vehicle',
k.design,
k.color
FROM saddle_covers k
JOIN items i ON i.id = k.id 
LEFT JOIN item_vehicles i_v ON i_v.item = i.id
JOIN vehicle v ON v.id = i_v.vehicle


select `i`.`id` AS `item`,`i`.`behavior` AS `behavior`,`i`.`department` AS `department`, i.parent, `v`.`universe` AS `family`,`v`.`brand` AS `brand`,`v`.`model` AS `model`,`v`.`id` AS `vehicle`,`k`.`design` AS `design`,`k`.`color` AS `color` from ((`graphic_kits` `k` join `items` `i` on(`i`.`id` = `k`.`id`)) join `vehicle` `v` on(`v`.`id` = `k`.`vehicle`)) 
union all select `i`.`id` AS `item`,`i`.`behavior` AS `behavior`,`i`.`department` AS `department`,i.parent, case when `k`.`vehicle` is not null then `v`.`universe` else `k`.`family` end AS `family`,case when `k`.`vehicle` is not null then `v`.`brand` else `k`.`brand` end AS `brand`,case when `k`.`vehicle` is not null then `v`.`model` else `k`.`model` end AS `model`,`v`.`id` AS `vehicle`,`k`.`design` AS `design`,`k`.`color` AS `color` from ((`products_old` `k` join `items` `i` on(`i`.`id` = `k`.`id`)) left join `vehicle` `v` on(`v`.`id` = `k`.`vehicle`)) 
union all select `i`.`id` AS `item`,`i`.`behavior` AS `behavior`,`i`.`department` AS `department`, i.parent, `v`.`universe` AS `family`,`v`.`brand` AS `brand`,`v`.`model` AS `model`,`v`.`id` AS `vehicle`,`k`.`design` AS `design`,`k`.`color` AS `color` from (((`saddle_covers` `k` join `items` `i` on(`i`.`id` = `k`.`id`)) left join `item_vehicles` `i_v` on(`i_v`.`item` = `i`.`id`)) join `vehicle` `v` on(`v`.`id` = `i_v`.`vehicle`))