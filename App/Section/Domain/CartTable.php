<?php
//declare(strict_types=1);
namespace App\Checkout\Domain;

use Core\Model\Table;

class CartTable extends Table {

    public function order(int $orderId, int $workplace = 2):object
    {
        
        


        $order = $this->order_1($orderId, $workplace);
        if($order)
            $order->items = $this->items($orderId);
        else
            $order = (object)['id'=>$orderId,'items'=>[]];

        return $order;


        $data = $this->query($sql, ['order' => $orderId, 'wp' => $workplace]);

              
        return json_decode(json_encode(['cart' => $order, 'quantity' => 1],JSON_NUMERIC_CHECK|JSON_PRESERVE_ZERO_FRACTION|JSON_UNESCAPED_SLASHES));
    }


    public function order_0(int $orderId, int $workplace = 2):object
    {
        
        $sql =" SELECT 
            oi.id, 
            oi.sku, 
            oi.qty,
            oi.id_order,
            o.id_user,
            oi.webshop_price AS 'item_value',
            oi.item_price,
            cur.currency_lib AS 'currency_code',
            oi.item_category,
            CASE WHEN oi.item_category = 60 THEN 1 ELSE 0 END AS 'email_delivery',
            oi.weight,
            oi.item_type, 
            oi.item_comment, 
            oi.item_paid,
            oi.tax_included,
            CASE 
                WHEN oi.description = '' THEN CONCAT_WS(' ', 'KUTVEK GRAPHIC KIT', oi.product)  
                WHEN oi.description IS NULL THEN CONCAT_WS(' ', 'KUTVEK GRAPHIC KIT', oi.product)
                ELSE oi.description
            END AS 'description',
            oi.product,
            oi.product_img AS 'item_visual',
            oi.product_url AS 'item_url',
            o.billing,
            o.contact,
            o.platform,
            o.paid, 
            o.promo_code,
            country.id AS 'country_id',
            o.country_code,
            country.country_iso_num,           
            CASE WHEN o.delivery_address IS NULL THEN 4 ELSE o.delivery_type END AS 'delivery_type',
            CASE WHEN o.delivery_type = 2 THEN 0.00 ELSE o.com_shipping END AS 'com_shipping',  
            o.delivery_address,            
            CASE WHEN o.promo_code IS NOT NULL THEN 
                (SELECT JSON_OBJECT(
                'id', o.promo_code,
                'designation', pcode.code, 
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
            CASE 
                # ADDRESS RELAY
                WHEN o.delivery_address IS NOT NULL AND o.delivery_type = 1 THEN 
                (
                    SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(
                            'id', a.id,
                            'address_line_1', CONCAT_WS(' ', 'Point Relais', chrono_relay.name),
                            'address_line_2', CONCAT_WS(' ',  a.line1, a.line2),
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', CASE WHEN c.country_iso = 'FR' AND  SUBSTR(a.zipcode, 1, 2) = 20 THEN 1 ELSE 0 END               
                        ),
                        'name', JSON_OBJECT(
                            'lastname', UPPER(order_relay.lastname),
                            'firstname', order_relay.firstname,
                            'full_name', CONCAT_WS(' ', order_relay.firstname, UPPER(order_relay.lastname)),
                            'company', order_relay.company
                        ),
                        'contact', JSON_OBJECT(
                            'phone', order_relay.phone,
                            'cellphone', order_relay.cellphone
                            ),
                        'type', 'SHIPPING',
                        'type_id', o.delivery_type

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
                            'id', a.id,
                            'address_line_1', a.line1,
                            'address_line_2',  a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', 0
                        ),                            
                        'name', JSON_OBJECT(
                            'lastname', UPPER(pickup.lastname),
                            'firstname', pickup.firstname,
                            'full_name', CONCAT_WS(' ', pickup.firstname, UPPER(pickup.lastname)),
                            'company', pickup.company
                        ),
                        'contact', JSON_OBJECT(
                            'phone', pickup.phone,
                            'cellphone', pickup.cellphone
                        ),
                        'type', 'PICKUP_IN_PERSON',
                        'type_id', o.delivery_type
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
                            'id', a.id,
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', CASE WHEN c.country_iso = 'FR' AND  SUBSTR(a.zipcode, 1, 2) = 20 THEN 1 ELSE 0 END
                        ),                            
                        'name', JSON_OBJECT(
                            'lastname', UPPER(ai.lastname),
                            'firstname', ai.firstname,
                            'full_name', CONCAT_WS(' ', ai.firstname, UPPER(ai.lastname)),
                            'company', ai.company
                        ),
                        'contact', JSON_OBJECT(
                            'phone', ai.phone,
                            'cellphone', ai.cellphone
                        ),
                        'type', 'SHIPPING',
                        'type_id', o.delivery_type
                    )
                    FROM addresses a
                    LEFT JOIN address_user ai ON ai.address = o.delivery_address AND ai.user = u.id
                    JOIN country c ON c.id = a.country
                    WHERE a.id = o.delivery_address
                )                
                ELSE NULL                
            END AS 'order_shipping',
            JSON_OBJECT(
                'id', u.id,                
                'lastname', UPPER(u.lastname),
                'firstname', u.firstname,
                'full_name', CONCAT_WS(' ', u.firstname, UPPER(u.lastname)),
                'company', bc.company,
                'type', CASE WHEN bc.id IS NOT NULL AND bc.rebate > 0 THEN 'pro' ELSE 'std' END,
                'payLater', CASE WHEN bc.deferred_payment IS NOT NULL THEN bc.deferred_payment ELSE 0 END,
                'rebate', CASE WHEN bc.rebate IS NOT NULL THEN bc.rebate ELSE '0.00' END,
                'contact', JSON_OBJECT(
                    'phone', u.phone,
                    'cellphone', u.cellphone
                ),
                'shipping_address',
                (
                    SELECT CASE WHEN au.address IS NOT NULL 
                    THEN JSON_OBJECT(
                        'address', 
                        JSON_OBJECT(
                            'id', a.id,
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', CASE WHEN c.country_iso = 'FR' AND  SUBSTR(a.zipcode, 1, 2) = 20 THEN 1 ELSE 0 END
                        ),
                        'name', 
                        JSON_OBJECT(
                            'lastname', UPPER(au.lastname),
                            'firstname', au.firstname,
                            'full_name', CONCAT_WS(' ', au.firstname, UPPER(au.lastname)),
                            'company', au.company
                        ),
                        'contact', 
                        JSON_OBJECT(
                            'phone', au.phone,
                            'cellphone', au.cellphone
                        ),
                        'type', 'SHIPPING',
                        'type_id', 4,
                        'user', au.user
                    )
                    ELSE NULL
                    END
                    FROM address_user au
                    LEFT JOIN addresses a ON a.id = au.address
                    JOIN country c ON c.id = a.country            
                    WHERE au.user = o.id_user AND au.is_active = 1 AND au.is_delivery = 1
                    ORDER BY au.address DESC
                    LIMIT 1
                ),
                'billing_address',
                (
                    SELECT CASE WHEN au.address IS NOT NULL 
                    THEN JSON_OBJECT(
                        'address', 
                        JSON_OBJECT(
                            'id', a.id,
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', CASE WHEN c.country_iso = 'FR' AND  SUBSTR(a.zipcode, 1, 2) = 20 THEN 1 ELSE 0 END
                        ),
                        'name', 
                        JSON_OBJECT(
                            'lastname', UPPER(au.lastname),
                            'firstname', au.firstname,
                            'full_name', CONCAT_WS(' ', au.firstname, UPPER(au.lastname)),
                            'company', au.company
                        ),
                        'contact', 
                        JSON_OBJECT(
                            'phone', au.phone,
                            'cellphone', au.cellphone
                        ),
                        'type', 'SHIPPING',
                        'type_id', 4,
                        'user', au.user
                    )
                    ELSE NULL END
                    FROM address_user au
                    LEFT JOIN addresses a ON a.id = au.address
                    JOIN country c ON c.id = a.country            
                    WHERE au.user = o.id_user AND au.is_active = 1 AND au.is_billing = 1
                    ORDER BY au.address DESC
                    LIMIT 1
                )
            ) AS 'customer',            
            
            JSON_OBJECT(
                'id', w_pickup.id,
                'address_line_1', w_pickup.line1,
                'address_line_2', w_pickup.line2,
                'admin_area_1', w_pickup.line4,
                'admin_area_2', w_pickup.city,
                'postal_code', w_pickup.zipcode,
                'country_code', w_country.country_iso
            ) AS 'pickup_addr',                   
            oi.workspace,                                   
            CASE 
                WHEN country_adr.id IS NULL THEN 1
                WHEN (country_adr.id IS NOT NULL AND country_adr.country_iso <> 'FR' AND bc.id IS NOT NULL ) OR country_adr.vat = 0 THEN 0                
                ELSE country_adr.vat
            END AS 'apply_vat',
            bc.rebate,
            CASE WHEN cat.behavior IS NULL THEN 'GraphicKitBehavior' ELSE cat.behavior END AS 'behavior'            
            FROM order_item AS oi
            LEFT JOIN _order AS o ON o.id = oi.id_order            
            LEFT JOIN country ON country.country_iso = o.country_code
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN user u ON u.id = o.id_user
            LEFT JOIN business_customer AS bc ON bc.id = o.id_user            
            LEFT JOIN category_accessories AS cat ON cat.id = oi.category
            LEFT JOIN websites website ON website.id = o.website
            LEFT JOIN workplaces wp ON wp.id = :wp
            LEFT JOIN addresses w_pickup ON w_pickup.id = wp.address
            LEFT JOIN country w_country ON w_country.id = w_pickup.country 
            LEFT JOIN addresses AS del_addr ON del_addr.id = o.delivery_address
            LEFT JOIN country AS country_adr ON country_adr.id = del_addr.country           
            WHERE id_order = :order;
        "; 


        $order = $this->order_1($orderId, $workplace);
        if($order)
            $order->items = $this->items($orderId);
        else
            $order = (object)['id'=>$orderId,'items'=>[]];

        return $order;


        $data = $this->query($sql, ['order' => $orderId, 'wp' => $workplace]);

        // Une etape supplémentaire pour les cartes cadeaux, il peut y avoir plusieurs carte sur une commande
        $sql = "SELECT SUM(used_amount) AS 'amount' FROM gift_card_orders WHERE _order = :order;";
        $gift_card_amounts = $this->query($sql, ['order' => $orderId], true);
        $quantity = 0;
        $currencyCode = 'EUR';
        $items = [];
        $tax_total = 0;
        $item_total = 0;               
        $discount = 0;
        $gift_amount = $gift_card_amounts->amount ?? 0;
        $shippingType = 'shipping';
        $shippingCost = 0;
        $shippingVat = 0;
        $pro_rebate = 0;
        $cart = [
            'id' => $orderId,
            'shipping'  => (object)['email_delivery'=> 1,'estimation' => false],           
            'platform'  => 'fr',
            'promocode' => null,
            'country_code' => 'FR',            
            'weight_total' => 0,            
            'step' => null,            
            'gift_card_amounts' => $gift_card_amounts->amount ?? '0.00',
            'discount' => []
        ];
        $amount = [
            'currency_code' => $currencyCode,
            'value' => number_format(($tax_total + $item_total + $shippingCost), 2, '.', ''),
            'breakdown' => [ 
                'item_total' => [ 'value' =>  number_format($item_total, 2, '.', '') , 'currency_code' => $currencyCode],
                'shipping' => [ 'value' => number_format($shippingCost, 2, '.', ''), 'currency_code' => $currencyCode],
                'tax_total' => [ 'value' => $tax_total, 'currency_code' => $currencyCode],
                'discount' => [ 'value' => $discount, 'currency_code' => $currencyCode],
                'handling' => [ 'value' => 0, 'currency_code' => $currencyCode],
                'shipping_discount' => [ 'value' => 0, 'currency_code' => $currencyCode]
            ]
        ]; 
        if(count($data) > 0) {
            
            $amount = [
                'currency_code' => $data[0]->currency_code,
                'value' => number_format(($tax_total + $item_total + $shippingCost), 2, '.', ''),
                'breakdown' => [ 
                    'item_total' => [ 'value' =>  number_format($item_total, 2, '.', '') , 'currency_code' => $data[0]->currency_code],
                    'shipping' => [ 'value' => number_format($shippingCost, 2, '.', ''), 'currency_code' => $data[0]->currency_code],
                    'tax_total' => [ 'value' => $tax_total, 'currency_code' => $data[0]->currency_code],
                    'discount' => [ 'value' => $discount, 'currency_code' => $data[0]->currency_code],
                    'handling' => [ 'value' => 0, 'currency_code' => $data[0]->currency_code],
                    'shipping_discount' => [ 'value' => 0, 'currency_code' => $data[0]->currency_code]
                ]
            ]; 

            // Récupérer le poids volumétric le plus élevé en fonction des articles présents dans le panier
            // Un placeholder, un tableau des id produits
            $vol_values = array_filter(array_column($data, 'product'));
            $vol_pl = $this->placeHolder($vol_values);
            $vol_sql = "SELECT MAX(volumetric_weight) AS w FROM volumetric_weights WHERE volumetric_weights.id IN ({$vol_pl});";
            $v_w = $this->query($vol_sql, array_values($vol_values), true);
            if($v_w) $v_w = $v_w->w;
            else $v_w = 0; 

            $data[0]->customer = json_decode($data[0]->customer);
            $cart['customer'] = $data[0]->customer;
            $cart['v_w'] = $v_w;
            $currencyCode = $data[0]->currency_code;
            $amount['currency_code'] = $data[0]->currency_code;
            $apply_vat = (int)$data[0]->apply_vat;            
            $cart['country_code'] =  $data[0]->country_code;
            $cart['weight_total'] =  array_sum(array_column($data, 'weight')) / 1000;
            $f = mb_strtolower($data[0]->country_code);
            $cart['country_flag'] = "/img/flags/1x1/{$f}.svg";
            $cart['paid'] =  $data[0]->paid;
            $cart['delivery_address'] = $data[0]->delivery_address;           
            $shippingCost = $data[0]->com_shipping ?? 0;
            foreach ($data as $item) {
                if($item->email_delivery < 1) $cart['shipping']->email_delivery = 0;

                $tax_included = $item->tax_included;
                // montant hors taxe de l'item                 
                $prices = json_decode($item->item_price, true);
                if($data[0]->rebate > 0) unset($prices['discount']);
                $item_cost = array_sum($prices) / $item->qty;

                if($tax_included > 0):
                    $unit_amount = number_format($item_cost / 1.20, 2, '.', '');
                    $unit_tax =  number_format($item_cost - $unit_amount , 2, '.', '');
                else:
                    $unit_amount = number_format($item_cost, 2, '.', '');
                    $unit_tax =  number_format(($unit_amount * 1.20) - $unit_amount , 2, '.', '');
                endif;
                
                $item_total += $unit_amount * $item->qty;
                $quantity += $item->qty;

                // calcul de la taxe sur l'item
                if($apply_vat)  $tax = $unit_tax;                    
                else  $tax = 0;               
                $tax_total += $tax * $item->qty;                              

                $i = [
                    'item_id' => $item->id,
                    'unit_amount' => [ 'currency_code' => $item->currency_code, 'value' =>  $unit_amount],
                    'tax' => [ 'currency_code' => $item->currency_code, 'value' => $tax],
                    'item_category' => $item->item_category,
                    'item_type' => $item->item_type,
                    'item_weight' => $item->weight,
                    'item_comment' => $item->item_comment,
                    'item_visual'   => $item->item_visual,
                    'item_url'      => $item->item_url,
                    'item_paid' => $item->item_paid,
                    'quantity' => $item->qty,
                    'sku' => $item->sku,
                    'category' => 'PHYSICAL_GOODS',
                    'name' => $item->description,
                    'links'     => [
                    'self' => '/api/items/' . $item->id, 
                    'order' => '/api/orders/' . $item->id_order                    
                    ],
                    'tax_included' => $item->tax_included,
                    'behavior' => $item->behavior
                ];                               
                $items[] = $i;
            } // end foreach on items

            $cart['platform'] = $data[0]->platform;           
            $cart['shipping']->pickup = json_decode($data[0]->pickup_addr);
            $cart['shipping']->address = $data[0]->order_shipping;

            $shipping = json_decode($data[0]->order_shipping, null, JSON_NUMERIC_CHECK); 
            $address = $shipping->address ?? (object)[];
            if($shipping){
                $client = $shipping->name;
                $contact = $shipping->contact;
                if($address->id && ($client->lastname || $client->company) && $contact->cellphone) $cart['step'] = 'payment';
                $cart['shipping']->address = $shipping->address ?? (object)[];
                $cart['shipping']->name = $shipping->name ?? (object)[];
                $cart['shipping']->contact = $shipping->contact ?? (object)[];
                $cart['shipping']->type = $shipping->type ?? 'SHIPPING';
                
                $cart['shipping']->type_id =  (int)$data[0]->delivery_type;
                if($cart['shipping']->type_id != 2 && $data[0]->com_shipping === null && $cart['weight_total'] > 0) $cart['step'] = null;
                //if($cart['shipping']->type_id != 3 && $data[0]->com_shipping < 1 && $cart['weight_total'] > 0) $cart['step'] = null;
                $cart['shipping']->cost = $data[0]->com_shipping;
                
                // on formatte l'adresse sélectionée
                $selected_shipping = '';
                $default = '';
                if($cart['shipping']->type_id === 2){
                    $selected_shipping .= '<p class="delivery-type" data-i18n="pickup">retrait sur place</p>';
                    $selected_shipping .= '<p>' . $cart['shipping']->address->address_line_1 . '</p>';
                    $selected_shipping .= '<p>' . $cart['shipping']->address->address_line_2 . '</p>';
                    $selected_shipping .= '<p>' . $cart['shipping']->address->admin_area_2. ', '  . $cart['shipping']->address->postal_code . ', ' . $cart['shipping']->address->country_code . '</p>';
                }
                if($cart['shipping']->type_id === 1){
                    $selected_shipping .= '<p class="delivery-type" data-i18n="click-and-collect">en point relais</p>';
                    $selected_shipping .= '<p>' . $cart['shipping']->name->full_name . '</p>';
                    if($cart['shipping']->name->company !== null)
                        $selected_shipping .= '<p>' . $cart['shipping']->name->company . '</p>';
                    $selected_shipping .= '<p>' . $cart['shipping']->address->address_line_1 . '</p>';
                    $selected_shipping .= '<p>' . $cart['shipping']->address->address_line_2 . '</p>';

                    $selected_shipping .= '<p>' . $cart['shipping']->address->admin_area_2 . ', ';
                    if($cart['shipping']->address->admin_area_1 !== null)
                        $selected_shipping .=    $cart['shipping']->address->admin_area_1 . ', ';
                    $selected_shipping .= $cart['shipping']->address->postal_code . ', ' . $cart['shipping']->address->country_code . '</p>';
                }
                if($cart['shipping']->type_id === 4){
                    $selected_shipping .= '<p class="delivery-type" data-i18n="home-delivery">à domicile</p>';
                    $selected_shipping .= '<p>' . $cart['shipping']->name->full_name . '</p>';
                    if($cart['shipping']->name->company !== null)
                        $selected_shipping .= '<p>' . $cart['shipping']->name->company . '</p>';
                    $selected_shipping .= '<p>' . $cart['shipping']->address->address_line_1 . '</p>';
                    $selected_shipping .= '<p>' . $cart['shipping']->address->address_line_2 . '</p>';

                    $selected_shipping .= '<p>' . $cart['shipping']->address->admin_area_2 . ', ';
                    if($cart['shipping']->address->admin_area_1 !== null)
                        $selected_shipping .=    $cart['shipping']->address->admin_area_1 . ', ';
                    $selected_shipping .= $cart['shipping']->address->postal_code . ', ' . $cart['shipping']->address->country_code . '</p>';
                }
                $cart['shipping']->current = $selected_shipping;
                $cart['shipping']->method = $selected_shipping;

                // On formatte l'adresse de livraison par défaut
                $cart['shipping']->default_shipping = $data[0]->default_shipping ? json_decode($data[0]->default_shipping) : false;
                if($cart['shipping']->default_shipping){
                    $default .= '<p class="delivery-type" data-i18n="home-delivery">à domicile</p>';
                    $default .= '<p>' . $cart['shipping']->default_shipping->name->full_name . '</p>';
                    if($cart['shipping']->default_shipping->name->company !== null)
                        $default .= '<p>' . $cart['shipping']->default_shipping->name->company . '</p>';
                    $default .= '<p>' . $cart['shipping']->default_shipping->address->address_line_1 . '</p>';
                    $default .= '<p>' . $cart['shipping']->default_shipping->address->address_line_2 . '</p>';

                    $default .= '<p>' . $cart['shipping']->default_shipping->address->admin_area_2 . ', ';
                    if($cart['shipping']->default_shipping->address->admin_area_1 !== null)
                        $default .=    $cart['shipping']->default_shipping->address->admin_area_1 . ', ';
                    $default .= $cart['shipping']->default_shipping->address->postal_code . ', ' . $cart['shipping']->default_shipping->address->country_code . '</p>';
                }

                $cart['shipping']->default = $default;               
            }              

            // Calcul des réductions code promo etc. Faire attention de bien prendre en compte les cartes cadeau  
            if($gift_amount > 0)
            {
                $cart['discount'][] = ['type' => 'gift-card', 'designation' => 'CARTE CADEAU', 'value' => $gift_amount];
            }

            // Calcul des réductions
            $coupon = json_decode($data[0]->coupon);
            $cart['coupon'] = $coupon;
            if($coupon->id !== null && $data[0]->customer->type !== 'pro')
            { 
                $amount['coupon'] = $coupon;
                $sum = $coupon->shipping_included == 1 ? $item_total + $tax_total + $shippingCost : $item_total + $tax_total;
                switch($coupon->type){
                    case 'rate':
                        $discount =  $sum * ($coupon->amount / 100);
                        break;
                    case 'amount':
                        // si supérieur au montant de la commande 
                        $discount = $coupon->amount >= $sum ? $sum : $coupon->amount;
                        break;
                }
                //$cart['promocode'] = $coupon->designation; 
                $cart['discount'][] = ['type' => 'coupon', 'designation' => $coupon->designation, 'value' => $discount]; 
            } 
            if($data[0]->customer->type === 'pro'  && $data[0]->customer->rebate > 0){
                $discount = ($item_total + $tax_total) * ($data[0]->customer->rebate / 100); 
                $percent = (int)$data[0]->customer->rebate; 
                $cart['discount'][] = ['type' => 'rebate', 'designation' => 'Remise PRO ' . $percent .'%', 'value' => $discount];     
            }
            $discount = $gift_amount + $discount;
                
                /** frais de port estimés  si pas de com_shipping */ 
                // Si client est pro et français alors chrono_13  = chrono_13_pro  customer_type
                //  Petite spécialité, la corse , si on est en france , que le code postal commence par 20 on a une surtaxe
            $this->entity = 'ShippingFees'; 

            $c_code = property_exists($cart['shipping'], 'address') ? $cart['shipping']->address->country_code : (property_exists($cart['shipping'], 'default_shipping') ? $cart['shipping']->default_shipping->address->country_code : $data[0]->country_code ?? 'FR');     
            $sql_fees = "
                SELECT c.id, 
                c.vat,
                area.area,
                area.classic,
                area.express, 
                ship_cost.w_from, 
                ship_cost.w_to, 
                ship_cost.chrono_classic, 
                c_params.shipping_surcharge_c4,
                :suppl_corse AS 'suppl_corse',
                :c_type AS 'c_type',
                (ship_cost.chrono_express + (ship_cost.chrono_express * c_params.fuel_surchage_express / 100 ) + c_params.security_fee + c_params.eco_participation) * 1.20 AS 'chrono_express',
                CASE WHEN ship_cost.chrono_classic = 0 THEN 0
                ELSE (ship_cost.chrono_classic + (ship_cost.chrono_classic * c_params.fuel_surchage_classic / 100 ) + c_params.security_fee + c_params.eco_participation) * 1.50 END  AS 'chrono_classic', 
                CASE
                    WHEN :c_type = 'pro' AND ship_cost.chrono_13 IS NOT NULL
                        THEN (ship_cost.chrono_13 + (ship_cost.chrono_13 * c_params.fuel_surchage_13 / 100 ) + c_params.security_fee + c_params.eco_participation ) * 1.13
                    WHEN :c_type != 'pro' AND ship_cost.chrono_13 IS NOT NULL
                        THEN (ship_cost.chrono_13 + (ship_cost.chrono_13 * c_params.fuel_surchage_13 / 100 ) + c_params.security_fee + c_params.eco_participation + c_params.shipping_surcharge_13 ) * 1.10
                    ELSE  ship_cost.chrono_13
                END  AS 'chrono_13',                   
                (ship_cost.chrono_relay + (ship_cost.chrono_relay * c_params.fuel_surchage_relay / 100 ) + c_params.security_fee + c_params.eco_participation) * 0.86 AS 'chrono_relay'
                FROM country c       
                LEFT JOIN chronopost_areas AS area ON area.country = c.id
                LEFT JOIN chronopost_prices AS ship_cost ON ship_cost.area = area.area
                LEFT JOIN chronopost_params AS c_params ON c_params.id = 1
                WHERE  c.country_iso = :code  
                AND :weight > 0
                AND :weight BETWEEN ship_cost.w_from AND ship_cost.w_to
            ";
            $surcharge_corse = 0;
            $_weight = $cart['weight_total'];  
            // le code postal : 

            if($shipping && $shipping->address->postal_code) {
                $postal_code = substr($shipping->address->postal_code, 0, 2);
                if($postal_code == '20' && $c_code == 'FR') $surcharge_corse = 1;
            }

            if($cart['weight_total'] < $v_w) {
                $_weight = $v_w;
            }
            $shipping_fees = $this->query($sql_fees, ['suppl_corse' => $surcharge_corse, 'code' => $c_code ?? 'FR', 'weight' => $_weight, 'c_type' => $data[0]->customer->type ?? 'std'], true);  
            $cart['shipping']->estimation  = $shipping_fees;    
            
            if($cart['weight_total'] <= 0) $shippingCost = 0;
            $amount['value'] = number_format(($tax_total + $item_total + $shippingCost + $shippingVat  - $discount), 2, '.', ''); 
            $amount['breakdown']['shipping']['value'] = number_format($shippingCost ?? 0, 2, '.', ''); 
            $amount['breakdown']['item_total']['value'] =  number_format($item_total ?? 0, 2, '.', '');
            $amount['breakdown']['tax_total']['value'] = number_format($tax_total ?? 0 + $shippingVat ?? 0, 2, '.', '');
            $amount['breakdown']['discount']['value'] = number_format($discount, 2, '.', ''); 
        }         
        $cart['items'] = $items;
        $cart['amount'] = $amount;        
        return json_decode(json_encode(['cart' => $cart, 'quantity' => $quantity],JSON_NUMERIC_CHECK|JSON_PRESERVE_ZERO_FRACTION|JSON_UNESCAPED_SLASHES));
    }



    public function items(int $order): array {
        $this->setEntity('CartItem');
        $sql =" SELECT 
            oi.id, 
            oi.sku, 
            oi.qty,
            oi.id_order AS 'order',            
            oi.webshop_price AS 'item_value',
            oi.item_price,
            cur.currency_lib AS 'currency_code',
            oi.item_category,
            CASE WHEN oi.item_category = 60 THEN 1 ELSE 0 END AS 'email_delivery',
            oi.weight,
            oi.item_type, 
            oi.item_comment, 
            oi.item_paid AS 'paid',
            oi.tax_included,
            CASE 
                WHEN oi.description = '' THEN CONCAT_WS(' ', 'KUTVEK GRAPHIC KIT', oi.product)  
                WHEN oi.description IS NULL THEN CONCAT_WS(' ', 'KUTVEK GRAPHIC KIT', oi.product)
                ELSE oi.description
            END AS 'description',
            oi.product,
            oi.product_img AS 'item_visual',
            oi.product_url AS 'item_url',                     
            oi.workspace, 
            bc.rebate, 
            CASE WHEN cat.behavior IS NULL THEN 'GraphicKitBehavior' ELSE cat.behavior END AS 'behavior'            
            FROM order_item AS oi
            JOIN _order o ON o.id = oi.id_order
            LEFT JOIN business_customer bc ON bc.id = o.id_user  
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency               
            LEFT JOIN category_accessories AS cat ON cat.id = oi.category                
            WHERE oi.id_order = :order;
        "; 
        $items = $this->query($sql,['order'=>$order]);
        $this->setEntity(null);
        return $items;

    }


    public function order_1(int $id, int $workplace = 2): false | CartEntity
    {

       $this->setEntity('Cart');
       $sql = "
            SELECT 
            o.id,
            o.id_user,    
            o.platform,
            o.paid, 
            o.promo_code,
            country.id AS 'country_id',
            o.country_code,
            :country_code AS 'cookie_country',
            country.country_iso_num,           
            CASE WHEN o.delivery_address IS NULL THEN 4 ELSE o.delivery_type END AS 'delivery_type',
            CASE WHEN o.delivery_type = 2 THEN 0.00 ELSE o.com_shipping END AS 'com_shipping',  
            o.delivery_address,            
            CASE WHEN o.promo_code IS NOT NULL THEN 
                (SELECT JSON_OBJECT(
                'id', o.promo_code,
                'designation', pcode.code, 
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
            CASE 
                # ADDRESS RELAY
                WHEN o.delivery_address IS NOT NULL AND o.delivery_type = 1 THEN 
                (
                    SELECT 
                    JSON_OBJECT(
                        'address', 
                            JSON_OBJECT(
                            'id', a.id,
                            'address_line_1', CONCAT_WS(' ', 'Point Relais', chrono_relay.name),
                            'address_line_2', CONCAT_WS(' ',  a.line1, a.line2),
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', CASE WHEN c.country_iso = 'FR' AND  SUBSTR(a.zipcode, 1, 2) = 20 THEN 1 ELSE 0 END               
                        ),
                        'name', JSON_OBJECT(
                            'lastname', UPPER(order_relay.lastname),
                            'firstname', order_relay.firstname,
                            'full_name', CONCAT_WS(' ', order_relay.firstname, UPPER(order_relay.lastname)),
                            'company', order_relay.company
                        ),
                        'contact', JSON_OBJECT(
                            'phone', order_relay.phone,
                            'cellphone', order_relay.cellphone
                            ),
                        'type', 'SHIPPING',
                        'type_id', o.delivery_type

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
                            'id', a.id,
                            'address_line_1', a.line1,
                            'address_line_2',  a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', 0
                        ),                            
                        'name', JSON_OBJECT(
                            'lastname', UPPER(pickup.lastname),
                            'firstname', pickup.firstname,
                            'full_name', CONCAT_WS(' ', pickup.firstname, UPPER(pickup.lastname)),
                            'company', pickup.company
                        ),
                        'contact', JSON_OBJECT(
                            'phone', pickup.phone,
                            'cellphone', pickup.cellphone
                        ),
                        'type', 'PICKUP_IN_PERSON',
                        'type_id', o.delivery_type
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
                            'id', a.id,
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', CASE WHEN c.country_iso = 'FR' AND  SUBSTR(a.zipcode, 1, 2) = 20 THEN 1 ELSE 0 END
                        ),                            
                        'name', JSON_OBJECT(
                            'lastname', UPPER(ai.lastname),
                            'firstname', ai.firstname,
                            'full_name', CONCAT_WS(' ', ai.firstname, UPPER(ai.lastname)),
                            'company', ai.company
                        ),
                        'contact', JSON_OBJECT(
                            'phone', ai.phone,
                            'cellphone', ai.cellphone
                        ),
                        'type', 'SHIPPING',
                        'type_id', o.delivery_type
                    )
                    FROM addresses a
                    LEFT JOIN address_user ai ON ai.address = o.delivery_address AND ai.user = u.id
                    JOIN country c ON c.id = a.country
                    WHERE a.id = o.delivery_address
                )                
                ELSE NULL                
            END AS 'order_shipping',
            JSON_OBJECT(
                'id', u.id,                
                'lastname', UPPER(u.lastname),
                'firstname', u.firstname,
                'full_name', CONCAT_WS(' ', u.firstname, UPPER(u.lastname)),
                'company', bc.company,
                'type', CASE WHEN bc.id IS NOT NULL AND bc.rebate > 0 THEN 'pro' ELSE 'std' END,
                'payLater', CASE WHEN bc.deferred_payment IS NOT NULL THEN bc.deferred_payment ELSE 0 END,
                'rebate', CASE WHEN bc.rebate IS NOT NULL THEN bc.rebate ELSE '0.00' END,
                'contact', JSON_OBJECT(
                    'phone', u.phone,
                    'cellphone', u.cellphone
                ),
                'shipping_address',
                (
                    SELECT CASE WHEN au.address IS NOT NULL 
                    THEN JSON_OBJECT(
                        'address', 
                        JSON_OBJECT(
                            'id', a.id,
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', CASE WHEN c.country_iso = 'FR' AND  SUBSTR(a.zipcode, 1, 2) = 20 THEN 1 ELSE 0 END
                        ),
                        'name', 
                        JSON_OBJECT(
                            'lastname', UPPER(au.lastname),
                            'firstname', au.firstname,
                            'full_name', CONCAT_WS(' ', au.firstname, UPPER(au.lastname)),
                            'company', au.company
                        ),
                        'contact', 
                        JSON_OBJECT(
                            'phone', au.phone,
                            'cellphone', au.cellphone
                        ),
                        'type', 'SHIPPING',
                        'type_id', 4,
                        'user', au.user
                    )
                    ELSE NULL
                    END
                    FROM address_user au
                    LEFT JOIN addresses a ON a.id = au.address
                    JOIN country c ON c.id = a.country            
                    WHERE au.user = o.id_user AND au.is_active = 1 AND au.is_delivery = 1
                    ORDER BY au.address DESC
                    LIMIT 1
                ),
                'billing_address',
                (
                    SELECT CASE WHEN au.address IS NOT NULL 
                    THEN JSON_OBJECT(
                        'address', 
                        JSON_OBJECT(
                            'id', a.id,
                            'address_line_1', a.line1,
                            'address_line_2', a.line2,
                            'admin_area_1', a.line4,
                            'admin_area_2', a.city,
                            'postal_code', a.zipcode,
                            'country_code', c.country_iso,
                            'corsica', CASE WHEN c.country_iso = 'FR' AND  SUBSTR(a.zipcode, 1, 2) = 20 THEN 1 ELSE 0 END
                        ),
                        'name', 
                        JSON_OBJECT(
                            'lastname', UPPER(au.lastname),
                            'firstname', au.firstname,
                            'full_name', CONCAT_WS(' ', au.firstname, UPPER(au.lastname)),
                            'company', au.company
                        ),
                        'contact', 
                        JSON_OBJECT(
                            'phone', au.phone,
                            'cellphone', au.cellphone
                        ),
                        'type', 'SHIPPING',
                        'type_id', 4,
                        'user', au.user
                    )
                    ELSE NULL END
                    FROM address_user au
                    LEFT JOIN addresses a ON a.id = au.address
                    JOIN country c ON c.id = a.country            
                    WHERE au.user = o.id_user AND au.is_active = 1 AND au.is_billing = 1
                    ORDER BY au.address DESC
                    LIMIT 1
                )
            ) AS 'customer',            
            
            JSON_OBJECT(
                'id', w_pickup.id,
                'address_line_1', w_pickup.line1,
                'address_line_2', w_pickup.line2,
                'admin_area_1', w_pickup.line4,
                'admin_area_2', w_pickup.city,
                'postal_code', w_pickup.zipcode,
                'country_code', w_country.country_iso
            ) AS 'pickup_addr',                   
            CASE 
                WHEN country_adr.id IS NULL THEN 1
                WHEN (country_adr.id IS NOT NULL AND country_adr.country_iso <> 'FR' AND bc.id IS NOT NULL ) OR country_adr.vat = 0 THEN 0                
                ELSE country_adr.vat
            END AS 'apply_vat',
            bc.rebate                 
            FROM _order AS o          
            LEFT JOIN country ON country.country_iso = o.country_code            
            LEFT JOIN user u ON u.id = o.id_user
            LEFT JOIN business_customer AS bc ON bc.id = o.id_user          
            LEFT JOIN websites website ON website.id = o.website
            LEFT JOIN workplaces wp ON wp.id = :wp
            LEFT JOIN addresses w_pickup ON w_pickup.id = wp.address
            LEFT JOIN country w_country ON w_country.id = w_pickup.country 
            LEFT JOIN addresses AS del_addr ON del_addr.id = o.delivery_address
            LEFT JOIN country AS country_adr ON country_adr.id = del_addr.country           
            WHERE o.id = :order;
       ";
       return $this->query($sql,['order'=>$id, 'wp' => $workplace, 'country_code'=>$this->getCountry()], 1); 

    }

    /**
     * Estimation des frais de livraison en fonction du pays de livraison et type client (pro/particulier)
     * Pays de livraison peu être :
     *  - celui de la dernière adr livr connue
     *  - celui renseigné dans le cookie country_currency
     *  - FR par défaut 
     */
    public function shippingFees() {




    }

    public function shippingDefaultAddress($id){
        $sql = "
            SELECT a_u.address
            FROM address_user a_u
            WHERE a_u.user = :user 
            AND a_u.is_active = 1 
            AND a_u.is_delivery = 1
            ORDER BY a_u.address DESC
            LIMIT 1;
        ";
        return $this->query($sql, ['user'=>$id],1);
    }

    public function billingDefaultAddress($id){
        $sql = "
            SELECT a_u.address
            FROM address_user a_u
            WHERE a_u.user = :user 
            AND a_u.is_active = 1 
            AND a_u.is_billing = 1
            ORDER BY a_u.address DESC
            LIMIT 1;
        ";
        return $this->query($sql, ['user'=>$id],1);
    }
}