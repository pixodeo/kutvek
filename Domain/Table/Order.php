<?php
declare(strict_types=1);
namespace Domain\Table;
use Core\Domain\Table;

class Order extends Table { 
    protected $table = '_order';

    public function tasks(int $order_id){
        $sql = " SELECT
            DATE_FORMAT(o.created, '%d/%m/%Y %H:%i') AS 'created',
            o.id AS 'order_id',
            a_c.country_iso AS 'country_shipping',
            a_c.vat,
            CASE WHEN a_c.id IS NOT NULL THEN CONCAT_WS('', '/img/flags/1x1/', LOWER(a_c.country_iso), '.svg') ELSE '/img/blank.png' END AS 'country_flag',
            JSON_OBJECT(
                'id', u.id,
                'fullname', CONCAT_WS(' - ', UPPER(bc.company), CONCAT_WS(' ', u.firstname, UPPER(u.lastname))),
                'rebate',   CASE WHEN bc.rebate IS NOT NULL THEN bc.rebate ELSE '0.00' END,
                'payLater', CASE WHEN bc.deferred_payment IS NOT NULL THEN bc.deferred_payment ELSE 0 END,
                'type',  CASE WHEN bc.id IS NOT NULL THEN 'pro' ELSE 'std' END,
                'email', u.email
            ) AS 'customer',  
            CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END AS 'com_shipping', 
            delivery_type.name AS 'delivery_name',      
            CASE 
            WHEN o.delivery_type = 1 THEN (
                SELECT 
                JSON_OBJECT(                        
                    'firstname', c_rc.firstname,
                    'lastname', c_rc.lastname,
                    'fullname', CONCAT_WS(' - ', UPPER(c_rc.company), CONCAT_WS(' ', c_rc.firstname, UPPER(c_rc.lastname))),
                    'company', c_rc.company,
                    'phone', c_rc.cellphone,
                    'phone2', c_rc.phone,
                    'address', JSON_OBJECT(
                        'id', a.id,
                        'postal_code', a.zipcode,
                        'admin_area_2', a.city,
                        'admin_area_1', UPPER(a.line4),
                        'address_line_1', CONCAT_WS('<br>',  CONCAT_WS('',c_r.type, c_r.id), c_r.name, a.line1),
                        'address_line_2', a.line2,
                        'country_code', UPPER(a_c.country_iso),
                        'country_name', UPPER(a_c.name_fr)
                    ),
                    'flag', CONCAT('/img/flags/1x1/', CASE WHEN a_c.id IS NULL THEN 'fr' ELSE LOWER(a_c.country_iso) END, '.svg'),
                    'type', delivery_type.name,
                    'cost', CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END
                )
                FROM chrono_relay c_r 
                LEFT JOIN chrono_relay_customers c_rc ON (c_rc.chronoRelay = c_r.id AND c_rc.orderId = o.id)
                WHERE c_r.address = o.delivery_address                      
            ) 
            WHEN o.delivery_type = 2 THEN (
                SELECT 
                JSON_OBJECT(                        
                    'firstname', pickup.firstname,
                    'lastname', pickup.lastname,
                    'fullname', CONCAT_WS(' - ', UPPER(pickup.company), CONCAT_WS(' ', pickup.firstname, UPPER(pickup.lastname))),
                    'company', pickup.company,
                    'phone', pickup.cellphone,
                    'phone2', pickup.phone,
                    'address', JSON_OBJECT(
                        'id', a.id,
                        'postal_code', a.zipcode,
                        'admin_area_2', a.city,
                        'admin_area_1', UPPER(a.line4),
                        'address_line_1', a.line1,
                        'address_line_2', a.line2,
                        'country_code', UPPER(a_c.country_iso),
                        'country_name', UPPER(a_c.name_fr)
                    ),
                    'flag', CONCAT('/img/flags/1x1/', CASE WHEN a_c.id IS NULL THEN 'fr' ELSE LOWER(a_c.country_iso) END, '.svg'),
                    'type', delivery_type.name,
                    'cost', 0.00
                )
                FROM pickup_customers pickup
                WHERE pickup.orderId = o.id
                ORDER BY pickup.id DESC 
                LIMIT 1                     
            ) 
            WHEN o.delivery_type = 4 THEN JSON_OBJECT (
                'firstname', CASE WHEN a_o.address IS NOT NULL THEN a_o.firstname ELSE a_u.firstname END,
                'lastname', UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.lastname ELSE a_u.lastname END),
                'company', UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.company ELSE a_u.company END),
                'fullname', CONCAT_WS(
                    ' - ', 
                    UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.company ELSE a_u.company END), 
                    CONCAT_WS(
                        ' ', 
                        CASE WHEN a_o.address IS NOT NULL THEN a_o.firstname ELSE a_u.firstname END, 
                        UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.lastname ELSE a_u.lastname END)
                        )
                ),
                'phone', CASE WHEN a_o.address IS NOT NULL THEN a_o.cellphone ELSE a_u.cellphone END,
                'phone2', CASE WHEN a_o.address IS NOT NULL THEN a_o.phone ELSE a_u.phone END,
                'email', CASE WHEN a_o.address IS NOT NULL THEN a_o.email ELSE a_u.email END,
                'address', JSON_OBJECT(
                    'id', a.id,
                    'postal_code', a.zipcode,
                    'admin_area_2', a.city,
                    'admin_area_1', UPPER(a.line4),
                    'address_line_1', a.line1,
                    'address_line_2', a.line2,
                    'country_code', UPPER(a_c.country_iso),
                    'country_name', UPPER(a_c.name_fr)
                ),
                'flag', CONCAT('/img/flags/1x1/', CASE WHEN a_c.id IS NULL THEN 'fr' ELSE LOWER(a_c.country_iso) END, '.svg'),
                'type', delivery_type.name,
                'cost', CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END
            ) 
            ELSE JSON_OBJECT ('address', false, 'flag', false, 'type', delivery_type.name,'cost', CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END)          
            END  AS 'delivery', 
            (SELECT 
                JSON_OBJECT(

                    'firstname', CASE WHEN au.firstname IS NOT NULL THEN au.firstname ELSE u.firstname END,
                    'lastname', UPPER(CASE WHEN au.lastname IS NOT NULL THEN au.lastname ELSE u.lastname END),
                    'company', UPPER(CASE WHEN au.company IS NOT NULL THEN au.company ELSE bc.company END),
                    'fullname', CONCAT_WS(' - ', UPPER(CASE WHEN au.company IS NOT NULL THEN au.company ELSE bc.company END), CONCAT_WS(' ', CASE WHEN au.firstname IS NOT NULL THEN au.firstname ELSE u.firstname END, UPPER(CASE WHEN au.lastname IS NOT NULL THEN au.lastname ELSE u.lastname END))),
                    'phone', CASE WHEN au.cellphone IS NOT NULL THEN  au.cellphone ELSE u.cellphone END,
                    'phone2', CASE WHEN au.phone IS NOT NULL THEN  au.phone ELSE u.phone END,
                    'email', au.email,
                    'address', JSON_OBJECT( 
                        'id', a.id,                           
                        'address_line_1', a.line1,
                        'address_line_2', a.line2,
                        'admin_area_1', a.line4,
                        'admin_area_2', a.city,
                        'postal_code', a.zipcode,
                        'country_code', UPPER(c.country_iso),
                        'country_name',  UPPER(c.name_fr)
                    ),
                    'flag', CONCAT('/img/flags/1x1/', CASE WHEN c.id IS NULL THEN 'fr' ELSE LOWER(c.country_iso) END, '.svg')            
                )              
                FROM address_user au
                LEFT JOIN addresses a ON a.id = au.address
                JOIN country c ON c.id = a.country            
                WHERE au.user = o.id_user AND au.is_active = 1 AND au.is_billing = 1
                ORDER BY a.id DESC
                LIMIT 1
            ) AS 'billing',      
            
            oi.id AS 'item_id',
            oi.description AS 'name',   
            oi.qty,
            CASE WHEN oi.item_price IS NULL THEN JSON_OBJECT('product', oi.webshop_price) ELSE oi.item_price END AS 'item_price',
            cur.currency_lib AS 'currency_code',
            oi.tax_included,
            oi.item_category,
            oi.weight,
            oi.item_type, 
            oi.item_comment, 
            oi.item_paid,
            oi.sku, 
            oi.product,
            oi.item_files,
            oi.product_url AS 'url',
            oi.product_img AS 'img', 
            CASE WHEN oi.item_category = 60 THEN 'DIGITAL_GOODS' ELSE 'PHYSICAL_GOODS' END AS 'category' ,
            CASE WHEN JSON_QUERY(oi.item_custom, '$.options.plate') IS NOT NULL THEN JSON_QUERY(oi.item_custom, '$.options.plate')  ELSE NULL END AS 'race',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.options.sponsor') IS NOT NULL THEN JSON_QUERY(oi.item_custom, '$.options.sponsor')  ELSE NULL END AS 'sponsor',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.options.switch') IS NOT NULL THEN JSON_QUERY(oi.item_custom, '$.options.switch') ELSE NULL END AS 'switch',
            CASE WHEN JSON_VALUE(oi.item_price, '$.seat_cover') IS NOT NULL AND JSON_VALUE(oi.item_price, '$.seat_cover') > 0 THEN 1 ELSE 0 END AS 'opt_saddle',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.premium') IS NOT NULL AND JSON_VALUE(oi.item_custom, '$.premium.name') != 'Aucune' THEN JSON_QUERY(oi.item_custom, '$.premium') ELSE NULL END AS 'premium',
            JSON_QUERY(oi.item_custom, '$.seat_cover') AS 'seat_cover',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.vehicle') IS NOT NULL THEN  JSON_QUERY(oi.item_custom, '$.vehicle') ELSE NULL END AS 'vehicle',       
            CASE WHEN o.promo_code IS NOT NULL THEN 
                (SELECT JSON_OBJECT(
                'id', o.promo_code,
                'designation', pcode.code, 
                'pro', pcode.pro_available,
                'type', pcode.code_type,
                'amount', CASE WHEN pcode.code_type = 'rate' THEN pcode.discount ELSE pcode.amount END,
                'shipping_included', pcode.shipping_discount,
                'min_purchase', pcode.min_purchase 
                ) 
                FROM promo_codes pcode
                WHERE pcode.id = o.promo_code
                ) 
                ELSE JSON_OBJECT('id', NULL)
            END AS 'coupon',
            o.invoice
            FROM _order o 
            JOIN order_item oi  ON oi.id_order = o.id           
            LEFT JOIN user u ON u.id = o.id_user 
            LEFT JOIN business_customer bc ON bc.id = o.id_user         
            LEFT JOIN address_order a_o ON a_o._order = o.id
            LEFT JOIN addresses a ON a.id = (
                CASE WHEN o.delivery_address IS NOT NULL THEN o.delivery_address 
                ELSE (
                    SELECT au.address 
                    FROM address_user au 
                    WHERE au.user = o.id_user 
                    AND au.is_active = 1 
                    AND au.is_delivery = 1 
                    ORDER BY au.address DESC
                    LIMIT 1) 
                END)
            LEFT JOIN address_user a_u ON (a_u.address = a.id AND a_u.user = o.id_user)
            LEFT JOIN country a_c ON a_c.id = a.country
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN delivery_type ON delivery_type.id = o.delivery_type
            WHERE o.id = :id ;
        ";

        return $this->query($sql, ['id' => $order_id]);
    }

    public function order(int $id): array {
        
        $sql = "SELECT oi.id,
            DATE_FORMAT(o.created, '%d/%m/%Y %H:%i') AS 'created',
            o.order_treated AS 'treated',            
            o.invoice,     
           cur.currency_lib AS 'currency_code',
            replace(step.method, '_', '') AS 'step_class',
            o.promo_code,
            o.delivery_type,
            country.vat,
            country.country_iso AS 'country_shipping',
            CONCAT_WS('', '/img/flags/1x1/', LOWER(country.country_iso), '.svg') AS 'country_flag',
            CASE WHEN o.promo_code IS NOT NULL THEN 
                (SELECT JSON_OBJECT(
                'id', o.promo_code,
                'designation', pcode.code, 
                'pro', pcode.pro_available,
                'type', pcode.code_type,
                'amount', CASE WHEN pcode.code_type = 'rate' THEN pcode.discount ELSE pcode.amount END,
                'shipping_included', pcode.shipping_discount,
                'min_purchase', pcode.min_purchase 
                ) 
                FROM promo_codes pcode
                WHERE pcode.id = o.promo_code
                ) 
                ELSE JSON_OBJECT('id', NULL)
            END AS 'coupon',
            CASE WHEN o.delivery_type = 2 THEN 0.00 WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END AS 'com_shipping',
            (SELECT count(*) FROM order_item WHERE order_item.id_order = o.id) AS 'nbItems',           
             CASE WHEN bc.rebate IS NOT NULL THEN bc.rebate ELSE '0.00' END AS 'rebate',
            CASE WHEN bc.deferred_payment IS NOT NULL THEN bc.deferred_payment ELSE 0 END AS 'payLater',
            CASE WHEN bc.id IS NOT NULL THEN 'pro' ELSE 'std' END AS 'customer_type',
            u.email AS 'customer_email',
            u.id AS 'customer_id',
            CASE WHEN bc.id IS NOT NULL 
                THEN CONCAT_WS(' - ', UPPER(bc.company), CONCAT_WS(' ', u.firstname, UPPER(u.lastname))) 
                ELSE CONCAT_WS(' ', u.firstname, UPPER(u.lastname))
            END AS 'customer_name',
            CASE 
                # ADDRESS RELAY
                WHEN o.delivery_type = 1 AND o.delivery_address IS NOT NULL  THEN 
                (
                    SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(                            
                            'address_line_1', CONCAT_WS(' ', 'Point Relais', chrono_relay.name),
                            'address_line_2', CONCAT_WS(' ',  a.line1, a.line2),
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso),
                            'name', JSON_OBJECT(
                                'full_name', CONCAT_WS(' ', order_relay.firstname, UPPER(order_relay.lastname)),
                                'company', order_relay.company
                            ),
                         'contact', CONCAT_WS(' / ', order_relay.cellphone, order_relay.phone),
                        'type', 'SHIPPING',
                        'flag', CONCAT('/img/flags/1x1/', LOWER(c.country_iso), '.svg'),
                        'country_name', c.name_fr
                        )
                    FROM addresses a
                    JOIN country c ON c.id = a.country
                    LEFT JOIN chrono_relay_customers AS order_relay 
                    ON ( 
                        order_relay.orderId = o.id
                        AND order_relay.id = (SELECT MAX(id) FROM chrono_relay_customers WHERE orderId = o.id)
                    )           
                    LEFT JOIN chrono_relay ON (chrono_relay.address = o.delivery_address AND chrono_relay.id = order_relay.chronoRelay)
                    WHERE a.id = o.delivery_address
                )
                # RETRAIT SUR PLACE
                WHEN  o.delivery_address IS NOT NULL AND o.delivery_type = 2 THEN 
                (
                   SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(                            
                            'address_line_1', a.line1,
                            'address_line_2',  a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso),
                        'name', JSON_OBJECT(
                           'full_name', CONCAT_WS(' ', pickup.firstname, UPPER(pickup.lastname)),
                           'company', UPPER(pickup.company)
                        ),
                      'contact', CONCAT_WS(' / ', pickup.cellphone, pickup.phone),
                        'type', 'PICKUP_IN_PERSON',
                        'flag', '/img/flags/1x1/fr.svg',
                        'country_name', 'France'
                    )
                    FROM addresses a
                    JOIN country c ON c.id = a.country
                    LEFT JOIN pickup_customers pickup ON(pickup.orderId = o.id AND pickup.id = (SELECT MAX(id) FROM pickup_customers WHERE orderId = o.id ))  
                    WHERE a.id = o.delivery_address
                )
                # CHRONOPOST
                WHEN  o.delivery_address IS NOT NULL AND o.delivery_type = 4 THEN
                (
                    SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(                            
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso),
                            'name', JSON_OBJECT(                                
                                'full_name', CONCAT_WS(' ', (CASE WHEN au_1.firstname IS NOT NULL THEN au_1.firstname ELSE u.firstname END), 
                                    CASE WHEN au_1.lastname IS NOT NULL THEN UPPER(au_1.lastname) ELSE NULL END),
                                'company',  CASE WHEN au_1.company IS NOT NULL THEN UPPER(au_1.company) ELSE NULL END                           
                            ),
                        'contact', CONCAT_WS(' / ', au_1.cellphone, au_1.phone),
                        'type', 'SHIPPING',
                        'flag', CONCAT('/img/flags/1x1/', LOWER(c.country_iso), '.svg'),
                        'country_name', c.name_fr
                    )
                    FROM addresses a
                    JOIN country c ON c.id = a.country
                    LEFT JOIN address_user au_1 ON au_1.address = a.id AND au_1.user = u.id
                    WHERE a.id = o.delivery_address
                )                
                WHEN  o.delivery_address IS NULL THEN
                (
                    SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(                            
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso
                            ),
                           
                            'name', JSON_OBJECT(                                
                                'full_name', CONCAT_WS(' ', (CASE WHEN au.firstname IS NOT NULL THEN au.firstname ELSE u.firstname END), 
                                    CASE WHEN au.lastname IS NOT NULL THEN UPPER(au.lastname) ELSE UPPER(u.lastname) END),
                                'company', (CASE WHEN au.company IS NOT NULL THEN UPPER(au.company) ELSE UPPER(bc.company) END )
                            ),
                       'contact', CONCAT_WS(' / ', au.cellphone, au.phone),
                        'type', 'SHIPPING',
                        'flag', CONCAT('/img/flags/1x1/', CASE WHEN c.id IS NULL THEN 'fr' ELSE LOWER(c.country_iso) END, '.svg'),
                        'country_name', CASE WHEN c.id IS NULL THEN '' ELSE c.name_fr END
                    )
                    FROM address_user au
                    LEFT JOIN addresses a ON a.id = au.address
                    JOIN country c ON c.id = a.country            
                    WHERE au.user = o.id_user AND au.is_active = 1 AND (au.is_delivery = 1 OR au.is_billing = 1)
                    LIMIT 1
                )
                ELSE '{}'
            END AS 'shipping',
            (SELECT 
                JSON_OBJECT(
                    'address', 
                        JSON_OBJECT(                            
                        'address_line_1', a.line1,
                        'address_line_2', a.line2,
                        'admin_area_1', a.line4,
                        'admin_area_2', a.city,
                        'postal_code', a.zipcode,
                        'country_code', c.country_iso),
                        'name', JSON_OBJECT(                                
                            'full_name', CASE WHEN au.lastname IS NOT NULL THEN CONCAT_WS(' ', au.firstname, UPPER(au.lastname)) ELSE CONCAT_WS(' ', u.firstname, UPPER(u.lastname)) END,
                            'company', CASE WHEN au.company IS NOT NULL THEN UPPER(au.company) ELSE UPPER(bc.company) END
                        ),
                    'contact', CONCAT_WS(' / ', CASE WHEN au.cellphone IS NULL THEN u.cellphone END, CASE WHEN au.phone IS NULL THEN u.phone END),            
                    'type', 'SHIPPING',                    
                    'country_name', CASE WHEN c.id IS NULL THEN '' ELSE c.name_fr END
                )              
                FROM address_user au
                LEFT JOIN addresses a ON a.id = au.address
                JOIN country c ON c.id = a.country            
                WHERE au.user = o.id_user AND au.is_active = 1 AND au.is_billing = 1
                LIMIT 1
            ) AS 'billing',
            :reedem AS 'reedem'                    
            FROM order_item AS oi
            LEFT JOIN _order o ON o.id = oi.id_order 
            LEFT JOIN user u ON u.id = o.id_user 
            LEFT JOIN business_customer bc ON bc.id = u.id                
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN addresses AS address ON address.id = (CASE WHEN o.delivery_address IS NOT NULL THEN o.delivery_address ELSE 0 END)
            LEFT JOIN country ON country.id = (
                CASE WHEN address.country IS NOT NULL THEN address.country 
                ELSE (SELECT cv.id 
                    FROM address_user auc
                    LEFT JOIN addresses ac ON ac.id = auc.address
                    JOIN country cv ON cv.id = ac.country            
                    WHERE auc.user = o.id_user AND auc.is_active = 1 AND (auc.is_delivery = 1 OR auc.is_billing = 1)
                    LIMIT 1
                ) END 
            ) 
            LEFT JOIN step_status step ON step.id = oi.status                 
            LEFT JOIN category_accessories AS cat ON cat.id = oi.category                               
            WHERE o.id = :id;
        ";
        $gift_cards = $this->reedems($id);
        $this->setEntity('ItemEntity');
        $items = $this->query($sql, ['id'=> $id, 'reedem' => json_encode($gift_cards ?? [], JSON_NUMERIC_CHECK, JSON_PRESERVE_ZERO_FRACTION)]);        
        return $items;
    } 

    public function orders(string $year, string $month): array {
        $this->setEntity('ItemEntity');
        $sql = "SELECT oi.id,
            DATE_FORMAT(o.created, '%d/%m/%Y %H:%i') AS 'created',
            oi.sku, 
            o.id_user,
            oi.description AS 'name',   
            CASE WHEN o.order_treated = 0 THEN 'untreated' ELSE 'treated' END AS 'treated', 
            oi.qty,
            o.invoice,
            oi.id_order AS 'orderId',                       
            CASE WHEN oi.item_price IS NULL THEN JSON_OBJECT('product', oi.webshop_price) ELSE oi.item_price END AS 'item_price',
            oi.tax_included,
            cur.currency_lib AS 'currency_code',
            oi.item_category,
            oi.weight,
            oi.item_type, 
            oi.task,
            oi.status,
            replace(step.method, '_', '') AS 'step_class',
            oi.item_comment, 
            oi.item_paid,
            oi.product,
            oi.item_files,
            oi.product_url AS 'url',
            oi.product_img AS 'img', 
            o.promo_code,
            o.delivery_type,
            JSON_ARRAY(JSON_OBJECT('serial_key', '',  'designation', 'Carte(s) Cadeau ', 'used_amount', (SELECT SUM(used_amount) AS 'amount' FROM gift_card_orders WHERE _order = o.id))) AS 'reedem',  
            CASE 
                WHEN (country.country_iso <> 'FR' AND bc.id IS NOT NULL ) OR country.vat = 0 THEN 0
                WHEN country.id IS NULL THEN 1
                ELSE country.vat
            END AS 'vat',           
            country.country_iso AS 'country_shipping',
            CONCAT_WS('', '/img/flags/1x1/', LOWER(country.country_iso), '.svg') AS 'country_flag',
            CASE WHEN o.promo_code IS NOT NULL THEN 
                (SELECT JSON_OBJECT(
                'id', o.promo_code,
                'designation', pcode.code, 
                'pro', pcode.pro_available,
                'type', pcode.code_type,
                'amount', CASE WHEN pcode.code_type = 'amount' THEN pcode.amount ELSE pcode.discount END,
                'shipping_included', pcode.shipping_discount,
                'min_purchase', pcode.min_purchase 
                )
                FROM promo_codes pcode
                WHERE pcode.id = o.promo_code
                ) 
                ELSE JSON_OBJECT('id', NULL)
            END AS 'coupon',
            CASE WHEN o.delivery_type = 2 THEN 0.00 WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END AS 'com_shipping',
            (SELECT count(*) FROM order_item WHERE order_item.id_order = o.id) AS 'nbItems',                      
            CASE WHEN JSON_QUERY(oi.item_custom, '$.options.plate') IS NOT NULL THEN JSON_QUERY(oi.item_custom, '$.options.plate')  ELSE NULL END AS 'race',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.options.sponsor') IS NOT NULL THEN JSON_QUERY(oi.item_custom, '$.options.sponsor')  ELSE NULL END AS 'sponsor',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.options.switch') IS NOT NULL THEN JSON_QUERY(oi.item_custom, '$.options.switch') ELSE NULL END AS 'switch',
            CASE WHEN JSON_VALUE(oi.item_price, '$.seat_cover') IS NOT NULL AND JSON_VALUE(oi.item_price, '$.seat_cover') > 0 THEN 1 ELSE 0 END AS 'opt_saddle',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.premium') IS NOT NULL AND JSON_VALUE(oi.item_custom, '$.premium.name') != 'Aucune' THEN JSON_QUERY(oi.item_custom, '$.premium') ELSE NULL END AS 'premium',
            JSON_QUERY(oi.item_custom, '$.seat_cover') AS 'seat_cover',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.vehicle') IS NOT NULL THEN  JSON_QUERY(oi.item_custom, '$.vehicle') ELSE NULL END AS 'vehicle',      
            CASE WHEN oi.item_category = 60 THEN 'DIGITAL_GOODS' ELSE 'PHYSICAL_GOODS' END AS 'category' ,
            CASE WHEN bc.rebate IS NOT NULL THEN bc.rebate ELSE '0.00' END AS 'rebate',
            CASE WHEN bc.deferred_payment IS NOT NULL THEN bc.deferred_payment ELSE 0 END AS 'payLater',
            CASE WHEN bc.id IS NOT NULL THEN 'pro' ELSE 'std' END AS 'customer_type',
            u.email AS 'customer_email',
            CASE WHEN bc.id IS NOT NULL 
                THEN CONCAT_WS(' - ', UPPER(bc.company), CONCAT_WS(' ', u.firstname, UPPER(u.lastname))) 
                ELSE CONCAT_WS(' ', u.firstname, UPPER(u.lastname))
            END AS 'customer_name',
            CASE 
                # ADDRESS RELAY
                WHEN o.delivery_type = 1 AND o.delivery_address IS NOT NULL  THEN 
                (
                    SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(                            
                            'address_line_1', CONCAT_WS(' ', 'Point Relais', chrono_relay.name),
                            'address_line_2', CONCAT_WS(' ',  a.line1, a.line2),
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso),
                            'name', JSON_OBJECT(
                                'full_name', CONCAT_WS(' ', order_relay.firstname, UPPER(order_relay.lastname)),
                                'company', order_relay.company
                            ),
                         'contact', CONCAT_WS(' / ', order_relay.cellphone, order_relay.phone),
                        'type', 'SHIPPING',
                        'flag', CONCAT('/img/flags/1x1/', LOWER(c.country_iso), '.svg'),
                        'country_name', c.name_fr
                        )
                    FROM addresses a
                    JOIN country c ON c.id = a.country
                    LEFT JOIN chrono_relay_customers AS order_relay 
                    ON ( 
                        order_relay.orderId = o.id
                        AND order_relay.id = (SELECT MAX(id) FROM chrono_relay_customers WHERE orderId = o.id)
                    )           
                    LEFT JOIN chrono_relay ON (chrono_relay.address = o.delivery_address AND chrono_relay.id = order_relay.chronoRelay)
                    WHERE a.id = o.delivery_address
                )
                # RETRAIT SUR PLACE
                WHEN  o.delivery_address IS NOT NULL AND o.delivery_type = 2 THEN 
                (
                   SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(                            
                            'address_line_1', a.line1,
                            'address_line_2',  a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso),
                        'name', JSON_OBJECT(
                           'full_name', CONCAT_WS(' ', pickup.firstname, UPPER(pickup.lastname)),
                           'company', UPPER(pickup.company)
                        ),
                      'contact', CONCAT_WS(' / ', pickup.cellphone, pickup.phone),
                        'type', 'PICKUP_IN_PERSON',
                        'flag', '/img/flags/1x1/fr.svg',
                        'country_name', 'France'
                    )
                    FROM addresses a
                    JOIN country c ON c.id = a.country
                    LEFT JOIN pickup_customers pickup ON(pickup.orderId = o.id AND pickup.id = (SELECT MAX(id) FROM pickup_customers WHERE orderId = o.id ))  
                    WHERE a.id = o.delivery_address
                )
                # CHRONOPOST
                WHEN  o.delivery_address IS NOT NULL AND o.delivery_type = 4 THEN
                (
                    SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(                            
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso),
                            'name', JSON_OBJECT(                                
                                'full_name', CONCAT_WS(' ', (CASE WHEN au_1.firstname IS NOT NULL THEN au_1.firstname ELSE u.firstname END), 
                                    CASE WHEN au_1.lastname IS NOT NULL THEN UPPER(au_1.lastname) ELSE NULL END),
                                'company',  CASE WHEN au_1.company IS NOT NULL THEN UPPER(au_1.company) ELSE NULL END                           
                            ),
                        'contact', CONCAT_WS(' / ', au_1.cellphone, au_1.phone),
                        'type', 'SHIPPING',
                        'flag', CONCAT('/img/flags/1x1/', LOWER(c.country_iso), '.svg'),
                        'country_name', c.name_fr
                    )
                    FROM addresses a
                    JOIN country c ON c.id = a.country
                    LEFT JOIN address_user au_1 ON au_1.address = a.id AND au_1.user = u.id
                    WHERE a.id = o.delivery_address
                )                
                WHEN  o.delivery_address IS NULL THEN
                (
                    SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(                            
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso
                            ),
                           
                            'name', JSON_OBJECT(                                
                                'full_name', CONCAT_WS(' ', (CASE WHEN au.firstname IS NOT NULL THEN au.firstname ELSE u.firstname END), 
                                    CASE WHEN au.lastname IS NOT NULL THEN UPPER(au.lastname) ELSE UPPER(u.lastname) END),
                                'company', (CASE WHEN au.company IS NOT NULL THEN UPPER(au.company) ELSE UPPER(bc.company) END )
                            ),
                       'contact', CONCAT_WS(' / ', au.cellphone, au.phone),
                        'type', 'SHIPPING',
                        'flag', CONCAT('/img/flags/1x1/', CASE WHEN c.id IS NULL THEN 'fr' ELSE LOWER(c.country_iso) END, '.svg'),
                        'country_name', CASE WHEN c.id IS NULL THEN '' ELSE c.name_fr END
                    )
                    FROM address_user au
                    LEFT JOIN addresses a ON a.id = au.address
                    JOIN country c ON c.id = a.country            
                    WHERE au.user = o.id_user AND au.is_active = 1 AND (au.is_delivery = 1 OR au.is_billing = 1)
                    LIMIT 1
                )
                ELSE '{}'
            END AS 'shipping',
            (SELECT 
                JSON_OBJECT(
                    'address', 
                        JSON_OBJECT(                            
                        'address_line_1', a.line1,
                        'address_line_2', a.line2,
                        'admin_area_1', a.line4,
                        'admin_area_2', a.city,
                        'postal_code', a.zipcode,
                        'country_code', c.country_iso),
                        'name', JSON_OBJECT(                                
                            'full_name', CASE WHEN au.lastname IS NOT NULL THEN CONCAT_WS(' ', au.firstname, UPPER(au.lastname)) ELSE CONCAT_WS(' ', u.firstname, UPPER(u.lastname)) END,
                            'company', CASE WHEN au.company IS NOT NULL THEN UPPER(au.company) ELSE UPPER(bc.company) END
                        ),
                    'contact', CONCAT_WS(' / ', CASE WHEN au.cellphone IS NULL THEN u.cellphone END, CASE WHEN au.phone IS NULL THEN u.phone END),            
                    'type', 'SHIPPING',                    
                    'country_name', CASE WHEN c.id IS NULL THEN '' ELSE c.name_fr END
                )              
                FROM address_user au
                LEFT JOIN addresses a ON a.id = au.address
                JOIN country c ON c.id = a.country            
                WHERE au.user = o.id_user AND au.is_active = 1 AND au.is_billing = 1
                LIMIT 1
            ) AS 'billing'
            FROM order_item AS oi
            LEFT JOIN _order o ON o.id = oi.id_order 
            LEFT JOIN user u ON u.id = o.id_user 
            LEFT JOIN business_customer bc ON bc.id = u.id                
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN addresses AS address ON address.id = (CASE WHEN o.delivery_address IS NOT NULL THEN o.delivery_address ELSE 0 END)
            LEFT JOIN country ON country.id = (
                CASE WHEN address.country IS NOT NULL THEN address.country 
                ELSE (SELECT cv.id 
                    FROM address_user auc
                    LEFT JOIN addresses ac ON ac.id = auc.address
                    JOIN country cv ON cv.id = ac.country            
                    WHERE auc.user = o.id_user AND auc.is_active = 1 AND (auc.is_delivery = 1 OR auc.is_billing = 1)
                    LIMIT 1
                ) END 
            ) 
            LEFT JOIN step_status step ON step.id = oi.status                 
            LEFT JOIN category_accessories AS cat ON cat.id = oi.category                               
            WHERE o.order_state NOT IN(9,10)
            AND YEAR(o.created) = :year
            AND MONTH(o.created) = :month 
            AND o.workspace IN(:workspace)            
            AND o.paid = 1
            AND o.v2 = 0
            ORDER BY o.date_paid DESC, o.created DESC;           
        "; 
        $orders = $this->query($sql, ['year' => $year, 'month' => $month, ':workspace' => WORKSPACE_ID]);
        return $orders;
    } 


    public function items(int $order_id){
        $this->setEntity('Item');
        $sql = " SELECT
            DATE_FORMAT(o.created, '%d/%m/%Y %H:%i') AS 'created',
            o.id AS 'order_id',
            o.order_state,
            CASE WHEN a_c.country_iso IS NOT NULL THEN a_c.country_iso ELSE 'FR' END AS 'country_shipping',
            a.zipcode  AS 'postal_code_default',
            CASE 
                WHEN a_c.vat = 1 AND a_c.id != 62 AND bc.rebate IS NOT NULL AND bc.rebate > 0 THEN 0
                WHEN a_c.vat IS NOT NULL  THEN a_c.vat 
                ELSE 1 
            END AS 'vat',
            CASE WHEN a_c.id IS NOT NULL THEN CONCAT_WS('', '/img/flags/1x1/', LOWER(a_c.country_iso), '.svg') ELSE '/img/blank.png' END AS 'country_flag',
            JSON_OBJECT(
                'id', u.id,
                'fullname', CONCAT_WS(' - ', UPPER(bc.company), CONCAT_WS(' ', u.firstname, UPPER(u.lastname))),
                'rebate',   CASE WHEN bc.rebate IS NOT NULL THEN bc.rebate ELSE '0.00' END,
                'payLater', CASE WHEN bc.deferred_payment IS NOT NULL THEN bc.deferred_payment ELSE 0 END,
                'type',  CASE WHEN bc.id IS NOT NULL THEN 'pro' ELSE 'std' END,
                'email', u.email
            ) AS 'customer',  
            CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END AS 'com_shipping', 
            delivery_type.name AS 'delivery_name',      
            CASE 
            WHEN o.delivery_type = 1 THEN (
                SELECT 
                JSON_OBJECT(                        
                    'firstname', c_rc.firstname,
                    'lastname', c_rc.lastname,
                    'fullname', CONCAT_WS(' - ', UPPER(c_rc.company), CONCAT_WS(' ', c_rc.firstname, UPPER(c_rc.lastname))),
                    'company', c_rc.company,
                    'phone', c_rc.cellphone,
                    'phone2', c_rc.phone,
                    'address', JSON_OBJECT(
                        'id', a.id,
                        'postal_code', a.zipcode,
                        'admin_area_2', a.city,
                        'admin_area_1', UPPER(a.line4),
                        'address_line_1', CONCAT_WS('<br>',  CONCAT_WS('',c_r.type, c_r.id), c_r.name, a.line1),
                        'address_line_2', a.line2,
                        'country_code', UPPER(a_c.country_iso),
                        'country_name', UPPER(a_c.name_fr)
                    ),
                    'flag', CONCAT('/img/flags/1x1/', CASE WHEN a_c.id IS NULL THEN 'fr' ELSE LOWER(a_c.country_iso) END, '.svg'),
                    'type', delivery_type.name,
                    'cost', CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END
                )
                FROM chrono_relay c_r 
                LEFT JOIN chrono_relay_customers c_rc ON (c_rc.chronoRelay = c_r.id AND c_rc.orderId = o.id)
                WHERE c_r.address = o.delivery_address                      
            ) 
            WHEN o.delivery_type = 2 THEN (
                SELECT 
                JSON_OBJECT(                        
                    'firstname', pickup.firstname,
                    'lastname', pickup.lastname,
                    'fullname', CONCAT_WS(' - ', UPPER(pickup.company), CONCAT_WS(' ', pickup.firstname, UPPER(pickup.lastname))),
                    'company', pickup.company,
                    'phone', pickup.cellphone,
                    'phone2', pickup.phone,
                    'address', JSON_OBJECT(
                        'id', a.id,
                        'postal_code', a.zipcode,
                        'admin_area_2', a.city,
                        'admin_area_1', UPPER(a.line4),
                        'address_line_1', a.line1,
                        'address_line_2', a.line2,
                        'country_code', UPPER(a_c.country_iso),
                        'country_name', UPPER(a_c.name_fr)
                    ),
                    'flag', CONCAT('/img/flags/1x1/', CASE WHEN a_c.id IS NULL THEN 'fr' ELSE LOWER(a_c.country_iso) END, '.svg'),
                    'type', delivery_type.name,
                    'cost', 0.00
                )
                FROM pickup_customers pickup
                WHERE pickup.orderId = o.id
                ORDER BY pickup.id DESC 
                LIMIT 1                     
            ) 
            WHEN o.delivery_type = 4 THEN JSON_OBJECT (
                'firstname', CASE WHEN a_o.address IS NOT NULL THEN a_o.firstname ELSE a_u.firstname END,
                'lastname', UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.lastname ELSE a_u.lastname END),
                'company', UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.company ELSE a_u.company END),
                'fullname', CONCAT_WS(
                    ' - ', 
                    UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.company ELSE a_u.company END), 
                    CONCAT_WS(
                        ' ', 
                        CASE WHEN a_o.address IS NOT NULL THEN a_o.firstname ELSE a_u.firstname END, 
                        UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.lastname ELSE a_u.lastname END)
                        )
                ),
                'phone', CASE WHEN a_o.address IS NOT NULL THEN a_o.cellphone ELSE a_u.cellphone END,
                'phone2', CASE WHEN a_o.address IS NOT NULL THEN a_o.phone ELSE a_u.phone END,
                'email', CASE WHEN a_o.address IS NOT NULL THEN a_o.email ELSE a_u.email END,
                'address', JSON_OBJECT(
                    'id', a.id,
                    'postal_code', a.zipcode,
                    'admin_area_2', a.city,
                    'admin_area_1', UPPER(a.line4),
                    'address_line_1', a.line1,
                    'address_line_2', a.line2,
                    'country_code', UPPER(a_c.country_iso),
                    'country_name', UPPER(a_c.name_fr)
                ),
                'flag', CONCAT('/img/flags/1x1/', CASE WHEN a_c.id IS NULL THEN 'fr' ELSE LOWER(a_c.country_iso) END, '.svg'),
                'type', delivery_type.name,
                'cost', CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END
            ) 
            ELSE (
                SELECT 
                JSON_OBJECT(
                    'firstname', CASE WHEN au.firstname IS NOT NULL THEN au.firstname ELSE u.firstname END,
                    'lastname', UPPER(CASE WHEN au.lastname IS NOT NULL THEN au.lastname ELSE u.lastname END),
                    'company', UPPER(CASE WHEN au.company IS NOT NULL THEN au.company ELSE bc.company END),
                    'fullname', CONCAT_WS(' - ', UPPER(CASE WHEN au.company IS NOT NULL THEN au.company ELSE bc.company END), CONCAT_WS(' ', CASE WHEN au.firstname IS NOT NULL THEN au.firstname ELSE u.firstname END, UPPER(CASE WHEN au.lastname IS NOT NULL THEN au.lastname ELSE u.lastname END))),
                    'phone', CASE WHEN au.cellphone IS NOT NULL THEN  au.cellphone ELSE u.cellphone END,
                    'phone2', CASE WHEN au.phone IS NOT NULL THEN  au.phone ELSE u.phone END,
                    'email', au.email,
                    'address', JSON_OBJECT( 
                        'id', a.id,                           
                        'address_line_1', a.line1,
                        'address_line_2', a.line2,
                        'admin_area_1', a.line4,
                        'admin_area_2', a.city,
                        'postal_code', a.zipcode,
                        'country_code', UPPER(c.country_iso),
                        'country_name',  UPPER(c.name_fr)
                    ),
                    'type', delivery_type.name,
                    'cost', CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END                             
                )              
                FROM address_user au
                LEFT JOIN addresses a ON a.id = au.address
                JOIN country c ON c.id = a.country            
                WHERE au.user = o.id_user AND au.is_active = 1 AND au.is_delivery = 1
                ORDER BY a.id DESC
                LIMIT 1
            )          
            END  AS 'delivery', 
            (SELECT 
                JSON_OBJECT(

                    'firstname', CASE WHEN au.firstname IS NOT NULL THEN au.firstname ELSE u.firstname END,
                    'lastname', UPPER(CASE WHEN au.lastname IS NOT NULL THEN au.lastname ELSE u.lastname END),
                    'company', UPPER(CASE WHEN au.company IS NOT NULL THEN au.company ELSE bc.company END),
                    'fullname', CONCAT_WS(' - ', UPPER(CASE WHEN au.company IS NOT NULL THEN au.company ELSE bc.company END), CONCAT_WS(' ', CASE WHEN au.firstname IS NOT NULL THEN au.firstname ELSE u.firstname END, UPPER(CASE WHEN au.lastname IS NOT NULL THEN au.lastname ELSE u.lastname END))),
                    'phone', CASE WHEN au.cellphone IS NOT NULL THEN  au.cellphone ELSE u.cellphone END,
                    'phone2', CASE WHEN au.phone IS NOT NULL THEN  au.phone ELSE u.phone END,
                    'email', au.email,
                    'address', JSON_OBJECT( 
                        'id', a.id,                           
                        'address_line_1', a.line1,
                        'address_line_2', a.line2,
                        'admin_area_1', a.line4,
                        'admin_area_2', a.city,
                        'postal_code', a.zipcode,
                        'country_code', UPPER(c.country_iso),
                        'country_name',  UPPER(c.name_fr)
                    ),
                    'flag', CONCAT('/img/flags/1x1/', CASE WHEN c.id IS NULL THEN 'fr' ELSE LOWER(c.country_iso) END, '.svg')            
                )              
                FROM address_user au
                LEFT JOIN addresses a ON a.id = au.address
                JOIN country c ON c.id = a.country            
                WHERE au.user = o.id_user AND au.is_active = 1 AND au.is_billing = 1
                ORDER BY a.id DESC
                LIMIT 1
            ) AS 'billing',      
            
            oi.id AS 'item_id',
            oi.description AS 'name',   
            oi.qty,
            CASE WHEN oi.item_price IS NULL THEN JSON_OBJECT('product', oi.webshop_price) ELSE oi.item_price END AS 'item_price',
            cur.currency_lib AS 'currency_code',
            oi.tax_included,
            oi.item_category,
            oi.weight,
            oi.item_type, 
            oi.item_comment, 
            oi.item_paid,
            oi.sku, 
            oi.product,
            oi.item_files,
            oi.product_url AS 'url',
            oi.product_img AS 'img', 
            CASE WHEN oi.item_category = 60 THEN 'DIGITAL_GOODS' ELSE 'PHYSICAL_GOODS' END AS 'category' ,
            CASE WHEN JSON_QUERY(oi.item_custom, '$.options.plate') IS NOT NULL THEN JSON_QUERY(oi.item_custom, '$.options.plate')  ELSE NULL END AS 'race',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.options.sponsor') IS NOT NULL THEN JSON_QUERY(oi.item_custom, '$.options.sponsor')  ELSE NULL END AS 'sponsor',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.options.switch') IS NOT NULL THEN JSON_QUERY(oi.item_custom, '$.options.switch') ELSE NULL END AS 'switch',
            CASE WHEN JSON_VALUE(oi.item_price, '$.seat_cover') IS NOT NULL AND JSON_VALUE(oi.item_price, '$.seat_cover') > 0 THEN 1 ELSE 0 END AS 'opt_saddle',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.premium') IS NOT NULL AND JSON_VALUE(oi.item_custom, '$.premium.name') != 'Aucune' THEN JSON_QUERY(oi.item_custom, '$.premium') ELSE NULL END AS 'premium',
            JSON_QUERY(oi.item_custom, '$.seat_cover') AS 'seat_cover',
            CASE WHEN JSON_QUERY(oi.item_custom, '$.vehicle') IS NOT NULL THEN  JSON_QUERY(oi.item_custom, '$.vehicle') ELSE NULL END AS 'vehicle',       
            CASE WHEN o.promo_code IS NOT NULL THEN 
                (SELECT JSON_OBJECT(
                'id', o.promo_code,
                'designation', pcode.code, 
                'pro', pcode.pro_available,
                'type', pcode.code_type,
                'amount', CASE WHEN pcode.code_type = 'rate' THEN pcode.discount ELSE pcode.amount END,
                'shipping_included', pcode.shipping_discount,
                'min_purchase', pcode.min_purchase 
                ) 
                FROM promo_codes pcode
                WHERE pcode.id = o.promo_code
                ) 
                ELSE JSON_OBJECT('id', NULL)
            END AS 'coupon',
            o.invoice
            FROM _order o 
            JOIN order_item oi  ON oi.id_order = o.id           
            LEFT JOIN user u ON u.id = o.id_user 
            LEFT JOIN business_customer bc ON bc.id = o.id_user         
            LEFT JOIN address_order a_o ON a_o._order = o.id
            LEFT JOIN addresses a ON a.id = (
                CASE WHEN o.delivery_address IS NOT NULL THEN o.delivery_address 
                ELSE (
                    SELECT au.address 
                    FROM address_user au 
                    WHERE au.user = o.id_user 
                    AND au.is_active = 1 
                    AND au.is_delivery = 1 
                    ORDER BY au.address DESC
                    LIMIT 1) 
                END
            )
            LEFT JOIN address_user a_u ON (a_u.address = a.id AND a_u.user = o.id_user)
            LEFT JOIN country a_c ON a_c.id = a.country
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN delivery_type ON delivery_type.id = o.delivery_type
            WHERE o.id = :id ;
        ";

        return $this->query($sql, ['id' => $order_id]);
    }

    public function state(int $id):int{
        $query = $this->query("SELECT order_state FROM _order WHERE id = :id",['id'=>$id], true);
        return !$query ? 0 : (int)$query->order_state;
    }

    public function shippingCost(array $info = []){

        $sql = "
            SELECT 
            c.id AS 'country_id',            
            c.vat AS 'country_vat',
            area.area,
            area.classic,
            area.express, 
            ship_cost.w_from, 
            ship_cost.w_to, 
            ship_cost.chrono_classic, 
            c_params.shipping_surcharge_c4,
            :suppl_corse AS 'suppl_corse',
            :customer_type AS 'c_type',
            (ship_cost.chrono_express + (ship_cost.chrono_express * c_params.fuel_surchage_express / 100 ) + c_params.security_fee + c_params.eco_participation) * 1.20 AS 'chrono_express',
            CASE WHEN ship_cost.chrono_classic = 0 THEN 0
            ELSE (ship_cost.chrono_classic + (ship_cost.chrono_classic * c_params.fuel_surchage_classic / 100 ) + c_params.security_fee + c_params.eco_participation) * 1.50 END  AS 'chrono_classic', 
            CASE
                WHEN :customer_type = 'pro' AND ship_cost.chrono_13 IS NOT NULL
                    THEN (ship_cost.chrono_13 + (ship_cost.chrono_13 * c_params.fuel_surchage_13 / 100 ) + c_params.security_fee + c_params.eco_participation ) * 1.13
                WHEN :customer_type != 'pro' AND ship_cost.chrono_13 IS NOT NULL
                    THEN (ship_cost.chrono_13 + (ship_cost.chrono_13 * c_params.fuel_surchage_13 / 100 ) + c_params.security_fee + c_params.eco_participation + c_params.shipping_surcharge_13 ) * 1.10
                ELSE  ship_cost.chrono_13
            END  AS 'chrono_13',                   
            (ship_cost.chrono_relay + (ship_cost.chrono_relay * c_params.fuel_surchage_relay / 100 ) + c_params.security_fee + c_params.eco_participation) * 0.86 AS 'chrono_relay'
            FROM country c       
            LEFT JOIN chronopost_areas AS area ON area.country = c.id
            LEFT JOIN chronopost_prices AS ship_cost ON ship_cost.area = area.area
            LEFT JOIN chronopost_params AS c_params ON c_params.id = 1
            WHERE  c.country_iso = :country_code  
            AND :weight > 0
            AND :weight BETWEEN ship_cost.w_from AND ship_cost.w_to;
        ";

        return $this->query($sql, $info, true);



    }

    public function reedems(int $id){
        $this->setEntity(null);
        return $this->query("SELECT gift_card.serial_key,  'Carte cadeau' AS 'designation', gift_card_orders.used_amount FROM gift_card_orders  LEFT JOIN gift_card ON gift_card.gift_card_id = gift_card_orders.gift_card WHERE gift_card_orders._order = :id", ['id'=> $id]);

    }    
}