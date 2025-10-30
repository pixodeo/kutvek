<?php 
declare(strict_types=1);
namespace Domain\Table;
use Core\Domain\Table;
use stdClass;

class AnalyticsEvent extends Table {
  
    /**
     * Récupère les données nécessaires pour l'event purchase
     * Différences par rapport à Paypal, le total c'est prix HT item * sa qty
     * La livraison n'est pas inclue
     *
     * @param      int   $order  The order
     */
    public function eventPurchase(int $order)
    {
        $sql = "SELECT 
            oi.description,
            oi.qty,                       
            CONCAT_WS('_', 'T', oi.id_order) AS 'transaction_id', 
            CONCAT_WS('_', 'I', oi.product) AS 'item_id',            
            oi.item_price,
            CASE WHEN oi.webshop_currency IS NULL THEN cur.currency_lib ELSE oi.webshop_currency END AS 'currency_code',            
            o.promo_code,
            p_code.code AS 'coupon',
            p_code.discount AS 'code_percent',                     
            o.com_shipping,            
            user.workspace,  
            user.email,          
            btob.rebate AS 'pro_rebate',
            btob.id AS 'pro',
            CASE 
                WHEN (o.country_code <> 'FR' AND btob.id IS NOT NULL ) OR country.vat = 0 THEN 0
                ELSE country.vat
            END AS 'apply_vat'            
            FROM order_item AS oi
            JOIN _order AS o ON o.id = oi.id_order  
            LEFT JOIN country ON country.country_iso = o.country_code
            LEFT JOIN promo_codes AS p_code ON p_code.id = o.promo_code
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN user ON user.id = o.id_user
            LEFT JOIN business_customer AS btob ON btob.id = o.id_user                     
            WHERE id_order = :order;";
            
            $orders = $this->query($sql, [':order' => $order]);
            $items = [];
            $tax_total = 0;
            $item_total = 0;            
            $discount = 0;
            $apply_vat = (int)$orders[0]->apply_vat;
            $currencyCode = $orders[0]->currency_code;
            $com_shipping = (int)$orders[0]->workspace === 3 ? (float)$orders[0]->ship_canam ?? (float)$orders[0]->com_shipping  : (float)$orders[0]->com_shipping;
            foreach ($orders as $item) {
                // montant sans tva
                $prices = json_decode($item->item_price, true); 
                $item_cost = array_sum($prices)/ $item->qty; // coût total par ligne, selon quantité                
                $unit_amount = number_format($item_cost / 1.20, 2, '.', '');
                $item_total += ($unit_amount * $item->qty);                
                
                if($apply_vat) $tax = number_format($item_cost - $unit_amount, 2, '.', '');               
                else $tax = 0;               
                $tax_total += ($tax * $item->qty);  
                $i = (object)[
                    'item_id' => $item->item_id,
                    'item_name' => $item->description,                   
                    'price' =>  $unit_amount,
                    'quantity' => $item->qty                  
                ];              
                $items[] = $i;
            }         

            if($orders[0]->promo_code !== null && $orders[0]->pro === null )
            {
                $sql = "SELECT id, discount, amount, shipping_discount 
                FROM promo_codes
                WHERE id = :code"; 
                $pcode = $this->query($sql, [':code' => $orders[0]->promo_code], true);

                // remise ou % ?
                if($pcode->amount !== null) {
                    // si supérieur au montant de la commande 
                    $discount = $pcode->amount > $item_total + $tax_total ?  $item_total + $tax_total : $pcode->amount;
                } else {
                    $discount = number_format(($item_total + $tax_total) * ($orders[0]->code_percent / 100), 2, '.', '');
                } 
                //$amount['breakdown']['discount']['value'] = $discount;
                //$amount['breakdown']['discount']['currency_code'] = $currencyCode;                                   
            }          

            if($orders[0]->pro !== null && $orders[0]->pro_rebate > 0){
                 
                $discount = number_format(($item_total + $tax_total) * ($orders[0]->pro_rebate / 100), 2, '.', '');  
                //$amount['breakdown']['discount']['value'] = $discount;
                //$amount['breakdown']['discount']['currency_code'] = $currencyCode;    
            }            
            $data = new stdClass;
            $data->transactionEmail = $orders[0]->email;
            $data->purchase = (object)[
                'event' => "purchase",
                'ecommerce' => (object)[
                    'transaction_id' => $orders[0]->transaction_id,
                    'value' =>  number_format($item_total , 2, '.', ''),
                    'tax'   => number_format($tax_total, 2, '.', ''),
                    'shipping' => number_format($com_shipping, 2, '.', ''),
                    'currency'  => $currencyCode,
                    'items' => $items
                ]

            ];
            return $data;      
    } 

    public function eventBeginCheckout(int $order)
    {
        $sql = "SELECT 
            oi.description,
            oi.qty,                       
            CONCAT_WS('_', 'T', oi.id_order) AS 'transaction_id', 
            CONCAT_WS('_', 'I', oi.product) AS 'item_id',            
            oi.item_price,
            CASE WHEN oi.webshop_currency IS NULL THEN cur.currency_lib ELSE oi.webshop_currency END AS 'currency_code',            
            o.promo_code,
            p_code.code AS 'coupon',
            p_code.discount AS 'code_percent',                     
            o.com_shipping,            
            user.workspace,  
            user.email,          
            btob.rebate AS 'pro_rebate',
            btob.id AS 'pro',
            CASE 
                WHEN (o.country_code <> 'FR' AND btob.id IS NOT NULL ) OR country.vat = 0 THEN 0
                ELSE country.vat
            END AS 'apply_vat'            
            FROM order_item AS oi
            JOIN _order AS o ON o.id = oi.id_order  
            LEFT JOIN country ON country.country_iso = o.country_code
            LEFT JOIN promo_codes AS p_code ON p_code.id = o.promo_code
            LEFT JOIN currency AS cur ON cur.currency_id = oi.currency
            LEFT JOIN user ON user.id = o.id_user
            LEFT JOIN business_customer AS btob ON btob.id = o.id_user                     
            WHERE id_order = :order;";
            
            $orders = $this->query($sql, [':order' => $order]);
            $items = [];
            $tax_total = 0;
            $item_total = 0;            
            $discount = 0;
            $apply_vat = (int)$orders[0]->apply_vat;
            $currencyCode = $orders[0]->currency_code;
           $com_shipping = (int)$orders[0]->workspace === 3 ? (float)$orders[0]->ship_canam ?? (float)$orders[0]->com_shipping  : (float)$orders[0]->com_shipping;
            foreach ($orders as $item) {
                // montant sans tva
                $prices = json_decode($item->item_price, true); 
                $item_cost = array_sum($prices)/ $item->qty; // coût total par ligne, selon quantité                
                $unit_amount = number_format($item_cost / 1.20, 2, '.', '');
                $item_total += ($unit_amount * $item->qty);                
                
                if($apply_vat) $tax = number_format($item_cost - $unit_amount, 2, '.', '');               
                else $tax = 0;               
                $tax_total += ($tax * $item->qty);  
                $i = (object)[
                    'item_id' => $item->item_id,
                    'item_name' => $item->description,                   
                    'price' =>  $unit_amount,
                    'quantity' => $item->qty                  
                ];              
                $items[] = $i;
            }           
           

            if($orders[0]->promo_code !== null && $orders[0]->pro === null )
            {
                $sql = "SELECT id, discount, amount, shipping_discount 
                FROM promo_codes
                WHERE id = :code"; 
                $pcode = $this->query($sql, [':code' => $orders[0]->promo_code], true);

                // remise ou % ?
                if($pcode->amount !== null) {
                    // si supérieur au montant de la commande 
                    $discount = $pcode->amount > $item_total + $tax_total ?  $item_total + $tax_total : $pcode->amount;
                } else {
                    $discount = number_format(($item_total + $tax_total) * ($orders[0]->code_percent / 100), 2, '.', '');
                } 
                //$amount['breakdown']['discount']['value'] = $discount;
                //$amount['breakdown']['discount']['currency_code'] = $currencyCode;                                   
            }          

            if($orders[0]->pro !== null && $orders[0]->pro_rebate > 0){
                 
                $discount = number_format(($item_total + $tax_total) * ($orders[0]->pro_rebate / 100), 2, '.', '');  
                //$amount['breakdown']['discount']['value'] = $discount;
                //$amount['breakdown']['discount']['currency_code'] = $currencyCode;    
            }
            
            $data = new stdClass;
            $data->transactionEmail = $orders[0]->email;
            $data->beginCheckout = (object)[
                'event' => "begin_checkout",
                'ecommerce' => (object)[                    
                    'value' =>  number_format($item_total , 2, '.', ''),                    
                    'currency'  => $currencyCode,
                    'items' => $items
                ]
            ];
            return $data;      
    }  
}	