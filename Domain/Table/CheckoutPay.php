<?php
declare(strict_types=1);
namespace Domain\Table;

use Domain\Entity\Checkout\Cart;

/**
 * Différences avec Domain\Table\Checkout :
 * On a obligatoirement une adresse de livraison et des frais de ports 
 * 
 */
class CheckoutPay extends Checkout { 
    public Cart $cart;   

    public function cart(int $id): ?Cart {
        $items = $this->items($id);
        if(count($items) < 1) return null;
        $this->cart = new Cart;
        $this->cart->ready_to_ship = (bool)$items[0]->ready_to_ship;
        $this->cart->id = $items[0]->order_id;
        $this->cart->customer = json_decode($items[0]->customer);
        $this->cart->country_shipping = $items[0]->country_shipping;     
        $this->cart->currency_code = $items[0]->currency_code;
        $this->cart->created = $items[0]->created;
        $this->cart->vat = $items[0]->vat;
        $this->cart->status = $items[0]->status;
        $this->cart->items = $items;
        $this->cart->delivery = $items[0]->delivery !== null ? json_decode($items[0]->delivery) :  (object)['type'=>$items[0]->delivery_name, 'cost' => $items[0]->com_shipping];        
        $this->cart->bill = json_decode($items[0]->billing ?? '{}');
        $this->cart->coupon = json_decode($items[0]->coupon);
        $this->cart->invoice = $items[0]->invoice;  
        $this->cart->amount();         
        return $this->cart;
    }

    public function items(int $order_id): array{
        $this->setEntity('Checkout\CartItem');
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
            CASE WHEN (o.delivery_type IS NOT NULL AND o.delivery_address IS NOT NULL AND o.com_shipping IS NOT NULL) THEN 1 ELSE 0 END AS 'ready_to_ship',           
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
            JSON_OBJECT(
                'firstname', CASE WHEN a_u.firstname IS NOT NULL THEN a_u.firstname ELSE u.firstname END,
                'lastname', UPPER(CASE WHEN a_u.lastname IS NOT NULL THEN a_u.lastname ELSE u.lastname END),
                'company', UPPER(CASE WHEN a_u.company IS NOT NULL THEN a_u.company ELSE bc.company END),
                'fullname', CONCAT_WS(' - ', UPPER(CASE WHEN a_u.company IS NOT NULL THEN a_u.company ELSE bc.company END), CONCAT_WS(' ', CASE WHEN a_u.firstname IS NOT NULL THEN a_u.firstname ELSE u.firstname END, UPPER(CASE WHEN a_u.lastname IS NOT NULL THEN a_u.lastname ELSE u.lastname END))),
                'phone', CASE WHEN a_u.cellphone IS NOT NULL THEN  a_u.cellphone ELSE u.cellphone END,
                'phone2', CASE WHEN a_u.phone IS NOT NULL THEN  a_u.phone ELSE u.phone END,
                'email', a_u.email,
                'address', JSON_OBJECT(
                    'id', a.id,                    
                    'address_line_1', a.line1,                    
                    'address_line_2', a.line2,                    
                    'postal_code', a.zipcode,
                    'admin_area_2', a.city,
                    'admin_area_1', UPPER(a.line4),
                    'country_code', UPPER(a_c.country_iso),
                    'country_name', UPPER(a_c.name_fr),
                    'relay_name', a.relay_name,
                    'relay_id', a.relay_id
                ),
                'flag', CONCAT('/img/flags/1x1/', CASE WHEN a_c.id IS NULL THEN 'fr' ELSE LOWER(a_c.country_iso) END, '.svg'),
                'type_id', o.delivery_type,
                'type', delivery_type.name,
                'cost', CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END
            ) AS 'delivery',            
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
                WHERE au.user = u.id AND au.is_active = 1 AND au.is_billing = 1
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
            LEFT JOIN user u ON u.id = CASE WHEN o.id_user IS NOT NULL THEN o.id_user ELSE :uid END
            LEFT JOIN business_customer bc ON bc.id = u.id            
            LEFT JOIN addresses a ON a.id = o.delivery_address
            LEFT JOIN address_user a_u ON (a_u.address = a.id AND a_u.user = u.id)
            LEFT JOIN country a_c ON a_c.id = a.country
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN delivery_type ON delivery_type.id = o.delivery_type
            WHERE o.id = :id ;
        ";

        return $this->query($sql, ['id' => $order_id, 'uid' => $this->userSessionId ? $this->userSessionId : null]);
    }       
}