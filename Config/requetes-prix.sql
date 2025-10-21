SELECT 
'2024-11-22 00:00:00' AS 'valid_since',
vpc.vehicle, 
v.fam_name,
v.brand_name,
v.name,
vpc.template_type,
gkt.title AS 'type_designation', 
ROUND((vpc.price/1.20)*1.665) AS 'price', 
ROUND((vpc.fluo_printed/1.20)*1.665) AS 'fluo_printed' , 
2 AS 'currency'
FROM vehicle_price_currencies vpc 
LEFT JOIN vue_vehicle_2 v ON v.id = vpc.vehicle
LEFT JOIN graphic_kit_types gkt ON gkt.id = vpc.template_type
WHERE 
vpc.id = (SELECT vp.id
from vehicle_price_currencies  vp
where vp.vehicle = vpc.vehicle
and vp.currency = vpc.currency
and vp.template IS NULL
and vp.valid_since <= current_timestamp()
and (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)
and vp.template_type = vpc.template_type
ORDER BY vp.id DESC
LIMIT 1
)
AND vpc.currency = 1
AND vpc.template IS  NULL
AND v.l10n = 1
AND v.universe = 10
AND vpc.valid_since <= current_timestamp()
AND (vpc.valid_until IS NULL OR current_timestamp() <= vpc.valid_until)
ORDER BY v.fam_name, v.brand_name, v.name, vpc.template_type

# Récup ids véhicules 
SELECT 
CONCAT(vpc.vehicle, ',')
FROM vehicle_price_currencies vpc 
LEFT JOIN vue_vehicle_2 v ON v.id = vpc.vehicle
LEFT JOIN graphic_kit_types gkt ON gkt.id = vpc.template_type
WHERE 
vpc.id = (SELECT vp.id
from vehicle_price_currencies  vp
where vp.vehicle = vpc.vehicle
and vp.currency = vpc.currency
and vp.template IS NULL
and vp.valid_since <= current_timestamp()
and (vp.valid_until IS NULL OR current_timestamp() <= vp.valid_until)
and vp.template_type = vpc.template_type
ORDER BY vp.id DESC
LIMIT 1
)
AND vpc.currency = 1
AND vpc.template IS  NULL
AND v.l10n = 1
AND v.universe = 18
AND vpc.valid_since <= current_timestamp()
AND (vpc.valid_until IS NULL OR current_timestamp() <= vpc.valid_until)
GROUP BY vpc.vehicle
ORDER BY vpc.vehicle