<?php
declare(strict_types=1);
namespace Domain\Table;
use Core\Domain\Table;
use Domain\Entity\Checkout\Cart;

class Checkout extends Table { 
    protected $table = '_order'; 
    protected int|false $userSessionId = false;
    protected int $orderId;

    public function setUserSessionId(int|false $userSessionId): void {
        $this->userSessionId = $userSessionId;
    }

    public function setOrderId(int $id): void {$this->orderId = $id;}

    public function cart(int $id): ?Cart {
        $items = $this->items($id);
        if(count($items) < 1) return null;
        $cart = new Cart;
        $cart->id = $items[0]->order_id;
        $cart->customer = json_decode($items[0]->customer);
        $cart->country_shipping = $items[0]->country_shipping;     
        $cart->currency_code = $items[0]->currency_code;
        $cart->created = $items[0]->created;
        $cart->vat = $items[0]->vat;
        $cart->status = $items[0]->status;
        $cart->items = $items;
        $cart->delivery = $items[0]->delivery !== null ? json_decode($items[0]->delivery) :  (object)['type'=>$items[0]->delivery_name, 'cost' => $items[0]->com_shipping];        
        $cart->bill = json_decode($items[0]->billing ?? '{}');
        $cart->coupon = json_decode($items[0]->coupon);
        $cart->invoice = $items[0]->invoice;  
        $cart->amount();   
        return $cart;
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
            CASE WHEN a_c.id IS NOT NULL THEN CONCAT_WS('', '/img/flags/1x1/', LOWER(a_c.country_iso), '.svg') ELSE '/img/blank.png' END AS 'country_flag',
            JSON_OBJECT(
                'id', u.id,
                'fullname', CONCAT_WS(' - ', UPPER(bc.company), CONCAT_WS(' ', u.firstname, UPPER(u.lastname))),
                'rebate',   CASE WHEN bc.rebate IS NOT NULL THEN bc.rebate ELSE '0.00' END,
                'payLater', CASE WHEN bc.deferred_payment IS NOT NULL THEN bc.deferred_payment ELSE 0 END,
                'type',  CASE WHEN bc.id IS NOT NULL THEN 'pro' ELSE 'std' END,
                'email', u.email,
                'shipping_at',(   
                    SELECT JSON_OBJECT(
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
                        'country_code', UPPER(ac.country_iso),
                        'country_name',  UPPER(ac.name_fr)
                    ),
                    'flag', CONCAT('/img/flags/1x1/', CASE WHEN ac.id IS NULL THEN 'fr' ELSE LOWER(ac.country_iso) END, '.svg')         
                    )
                    FROM address_user au
                    LEFT JOIN  addresses a ON (a.id = au.address)
                    LEFT JOIN country ac ON ac.id = a.country 
                    WHERE au.user = u.id
                    AND au.is_active = 1                    
                    ORDER BY au.is_delivery DESC, au.address DESC
                    LIMIT 1 
                )
            ) AS 'customer',  
            CASE WHEN o.com_shipping IS NULL THEN 0.00 ELSE o.com_shipping END AS 'com_shipping', 
            delivery_type.name AS 'delivery_name',      
            CASE 
            WHEN o.delivery_address IS NULL THEN                
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
                        'admin_area_1', a.line4,
                        'admin_area_2', a.city,
                        'postal_code', a.zipcode,
                        'country_code', UPPER(a_c.country_iso),
                        'country_name',  UPPER(a_c.name_fr)
                    ),
                    'flag', CONCAT('/img/flags/1x1/', CASE WHEN a_c.id IS NULL THEN 'fr' ELSE LOWER(a_c.country_iso) END, '.svg')            
                )
            WHEN o.delivery_address IS NOT NULL AND o.delivery_type = 1 THEN (
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
            WHEN o.delivery_address IS NOT NULL AND o.delivery_type = 2 THEN (
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
            WHEN o.delivery_address IS NOT NULL AND o.delivery_type = 4 THEN JSON_OBJECT (
                'firstname', a_u.firstname,
                'lastname', UPPER(a_u.lastname),
                'company', UPPER(a_u.company),
                'fullname', CONCAT_WS(
                    ' - ', 
                    UPPER(a_u.company), 
                    CONCAT_WS(
                        ' ', 
                        a_u.firstname, 
                        UPPER(a_u.lastname)
                        )
                ),
                'phone', a_u.cellphone,
                'phone2', a_u.phone,
                'email', a_u.email,
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
            ) END  AS 'delivery',            
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
            LEFT JOIN addresses a ON a.id = (
                CASE WHEN o.delivery_address IS NOT NULL THEN o.delivery_address 
                ELSE (
                    SELECT au.address 
                    FROM address_user au 
                    WHERE au.user = u.id
                    AND au.is_active = 1                    
                    ORDER BY au.is_delivery DESC, au.address DESC
                    LIMIT 1) 
                END
            )
            LEFT JOIN address_user a_u ON (a_u.address = a.id AND a_u.user = u.id)
            LEFT JOIN country a_c ON a_c.id = a.country
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN delivery_type ON delivery_type.id = o.delivery_type
            WHERE o.id = :id ;
        ";

        return $this->query($sql, ['id' => $order_id, 'uid' => $this->userSessionId ? $this->userSessionId : null]);
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

    /**
     * Valeur du panier utile pour faire une comparaison avec min_purchase d'un code promo
     *
     * @param      int   $order  The order
     */
    public function amount(int $order, float $min_purchase){
        $this->setEntity(null);
        $sql = "SELECT                        
            oi.item_price,
            oi.qty,
            CASE 
                WHEN country_adr.id IS NULL THEN 1
                WHEN (country_adr.country_iso <> 'FR' AND bc.id IS NOT NULL ) OR country_adr.vat = 0 THEN 0
                ELSE country_adr.vat
            END AS 'apply_vat'                               
            FROM order_item AS oi
            LEFT JOIN _order AS o ON o.id = oi.id_order
            LEFT JOIN country ON country.country_iso = o.country_code
            LEFT JOIN addresses AS del_addr ON del_addr.id = o.delivery_address
            LEFT JOIN country AS country_adr ON country_adr.id = del_addr.country
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN user u ON u.id = o.id_user
            LEFT JOIN business_customer AS bc ON bc.id = o.id_user            
            LEFT JOIN category_accessories AS cat ON cat.id = oi.category
            LEFT JOIN websites website ON website.id = o.website                                  
            WHERE id_order = :order;
        ";            
        $items = $this->query($sql, [':order' => $order]);    
        $tax_total = 0;
        $item_total = 0;        
        $apply_vat = (int)$items[0]->apply_vat;              
       
        foreach ($items as $item) {
            // montant sans tva
            $prices = json_decode($item->item_price, true); 
            $item_cost = array_sum($prices)/ $item->qty; // coût total par ligne, selon quantité                
            $unit_amount = number_format($item_cost / 1.20, 2, '.', '');
            $item_total += ($unit_amount * $item->qty);            
            if($apply_vat)  $tax = number_format($item_cost - $unit_amount, 2, '.', '');              
            else $tax = 0;           
            $tax_total += ($tax * $item->qty);    
        } 
        return ($item_total + $tax_total) >= $min_purchase;
    } 


    public function countries(){
        $sql ="SELECT c.id AS 'country_id',
        c.country_iso AS 'country_code',
        c.name_fr AS 'country_name',
        c.states AS 'with_states'
        FROM country c
        JOIN country_domains c_d ON (c_d.country = c.id AND c_d.domain_name = :ws)
        WHERE c_d.visible_on_preferences = 1
        ORDER BY c_d.position_on_preferences DESC, c.name_fr ASC;";
        return $this->query($sql, ['ws' => WEBSITE_ID]);
    }

    public function userId(): null|int {
        $sql = "SELECT id_user FROM _order WHERE id = :id;";
        $q = $this->query($sql,['id'=> $this->orderId ], true);
        if($q) return $q->id_user;
        return  null;
    }

    public function countryId(string $country_code = 'FR'): null|int{
        $sql = "SELECT id FROM country WHERE country_iso = :c;";
        $q = $this->query($sql,['c'=>$country_code], true);
        if($q) return (int)$q->id;
        return null;
    }
}