<?php
declare(strict_types=1);
namespace App\Checkout\Domain\Table;
use Core\Domain\Table;


class PayPal extends Table {

    public function items(int $order_id){
        //$this->setEntity('Item');
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
                    'address', JSON_OBJECT(                       
                        'postal_code', a.zipcode,
                        'admin_area_2', a.city,
                        'admin_area_1', UPPER(a.line4),
                        'address_line_1', a.line1,
                        'address_line_2', a.line2,
                        'country_code', UPPER(a_c.country_iso)                        
                    ),   
                    'name', JSON_OBJECT(
                        'full_name', CONCAT_WS(' - ', UPPER(pickup.company), CONCAT_WS(' ', pickup.firstname, UPPER(pickup.lastname)))   
                    ),
                    'contact', JSON_OBJECT(
                        'phone', pickup.phone,
                        'cellphone', pickup.cellphone
                    ),                
                    'type', 'PICKUP_IN_PERSON',
                    'cost', 0.00
                )               
                FROM pickup_customers pickup
                WHERE pickup.orderId = o.id
                ORDER BY pickup.id DESC 
                LIMIT 1                     
            ) 
            WHEN o.delivery_type = 4 THEN JSON_OBJECT (                      
                'address', JSON_OBJECT(                    
                    'postal_code', a.zipcode,
                    'admin_area_2', a.city,
                    'admin_area_1', UPPER(a.line4),
                    'address_line_1', a.line1,
                    'address_line_2', a.line2,
                    'country_code', UPPER(a_c.country_iso)                    
                ), 
                'name', JSON_OBJECT(
                    'full_name', CONCAT_WS(
                        ' - ', 
                        UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.company ELSE a_u.company END), 
                        CONCAT_WS(
                            ' ', 
                            CASE WHEN a_o.address IS NOT NULL THEN a_o.firstname ELSE a_u.firstname END, 
                            UPPER(CASE WHEN a_o.address IS NOT NULL THEN a_o.lastname ELSE a_u.lastname END)
                            )
                        )  
                    ),
                'contact', JSON_OBJECT(
                    'phone', CASE WHEN a_o.address IS NOT NULL THEN a_o.phone ELSE a_u.phone END,
                    'cellphone', CASE WHEN a_o.address IS NOT NULL THEN a_o.cellphone ELSE a_u.cellphone END
                ),               
                'type', 'SHIPPING',
                'cost', CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END
            )                    
            END  AS 'delivery',    
            REPLACE(oi.description, '/', '-') AS 'name',   
            oi.qty,
            CASE WHEN oi.item_price IS NULL THEN JSON_OBJECT('product', oi.webshop_price) ELSE oi.item_price END AS 'item_price',
            cur.currency_lib AS 'currency_code',
            oi.tax_included,  
            oi.sku, 
            oi.product,     
            CASE WHEN oi.item_category = 60 THEN 'DIGITAL_GOODS' ELSE 'PHYSICAL_GOODS' END AS 'category',            
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
}