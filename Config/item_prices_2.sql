select `i`.`id` AS `id`,
g.id AS 'graphic_kit',
CASE  
	WHEN g.id IS NOT NULL THEN (
		select 
		case when `vp`.`currency` = 3 then min(`vp`.`price` * 1.20) else min(`vp`.`price`) end 
		from `vehicle_price_currencies` `vp` 
		where `vp`.`vehicle` = `g`.`vehicle` 
		and `vp`.`currency` = `cur`.`currency_id` 
		and `vp`.`valid_since` <= current_timestamp() 
		and (`vp`.`valid_until` is null or current_timestamp() <= `vp`.`valid_until`) 
		and `vp`.`template_type` in (1,2,3) order by `vp`.`id` desc limit 1
	)	
	ELSE (
		SELECT p.price FROM prices p

		where  p.item = i.id 
		and `p`.`valid_since` <= current_timestamp() 
		and (`p`.`valid_until` is null or current_timestamp() <= `p`.`valid_until`) 
		and p.currency = cur.currency_id
		order by `p`.`id` desc limit 1
	)
END AS 'price',
`cur`.`currency_id` AS `currency_id`,
`cur`.`currency_lib` AS `currency_code` 
from items i
left JOIN  `graphic_kits` `g` On g.id = i.id 
join `currency` `cur` on(`cur`.`currency_id` in (1,2,3,4)) 