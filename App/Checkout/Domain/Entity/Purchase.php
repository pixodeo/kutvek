<?php
declare(strict_types=1);

namespace App\Checkout\Domain\Entity;
use Core\Domain\Entity;

class Purchase extends Entity {
	public int $id;    
	public string $created;
    public object|false $reedem = false;
    public object $customer;
    public object $shipping;    
    public object $bill;
	public array $items = []; 

    public function __get($key)
    {        
        if(property_exists($this, $key)){return $this->{$key};}

        $method = 'get' . ucfirst($key);
        if(method_exists($this, $method))
        	parent::__get($key);
        return false;        
    }	

    public function amount(){
		$tax_total = 0;
		$item_total = 0;		
		$com_shipping = (float)$this->shipping->cost;
        unset($this->shipping->cost);
		$rebate = $this->customer->rebate;
        $array_item_total = [];
        $array_tax_total = [];
		foreach($this->items as $item):		
			$prices = array_filter(json_decode($item->item_price,true));
			if((int)$item->tax_included === 1 && $this->country_shipping !== 'CA'): // prix enregisrés ttc
                $item_ttc = (array_sum($prices)/ $item->qty);
                $item_ht = $item_ttc / 1.20; // coût total par ligne, selon 3
                $item_tax = ($item_ttc / 120)*20;                      
            else:
                $item_ht = array_sum($prices)/ $item->qty;              
                $item_tax = $item_ht*0.2;
                $item_ttc = $item_ht*1.20;
            endif;
            $item->unit_amount = (object)[
                'currency_code' => $item->currency_code, 
                'value' =>  number_format($item_ht, 2, '.', '')                             
            ];
            $item->tax = (object)[
                'currency_code' => $item->currency_code, 
                'value' => $this->vat > 0 ? number_format($item_tax, 2, '.', '') : '0.00'                       
            ];  
            $array_item_total[] =    number_format($item_ht, 2, '.', '');  
            $array_tax_total[] =  $this->vat > 0 ? number_format($item_tax, 2, '.', '') : '0.00';
            unset($item->invoice);
            unset($item->created);
            unset($item->order_id);
            unset($item->order_state);
            unset($item->country_shipping);
            unset($item->postal_code_default);
            $item->quantity = $item->qty;
            unset($item->qty);            
            unset($item->delivery_name);
            unset($item->delivery);
            unset($item->billing);
            unset($item->vehicle);
            unset($item->opt_saddle);
            unset($item->item_files);
            $item->sku = $item->sku??$item->product;
            unset($item->product);
            unset($item->item_price);
            unset($item->vat);
            unset($item->country_flag);
            unset($item->customer);
            unset($item->com_shipping);
            unset($item->currency_code);
            unset($item->tax_included);
            unset($item->coupon);
		endforeach;
		$item_total = array_sum($array_item_total);
        $tax_total = array_sum($array_tax_total);
		$discounts = [];
        
		if($this->coupon->id !== null && ($this->customer->type !== 'pro' || $this->coupon->pro > 0))
        { 
            //$amount['coupon'] = $coupon;
            $sum = $this->coupon->shipping_included == 1 ? $item_total + $tax_total + $this->com_shipping : $item_total + $tax_total;
            switch($this->coupon->type){
                case 'rate':
                    $rate_0 = $this->coupon->amount - ($rebate ?? 0);
                    $discount =  $sum * ($rate_0 / 100);
                    break;
                case 'amount':
                    // si supérieur au montant de la commande 
                    $discount = $this->coupon->amount >= $sum ? $sum : $this->coupon->amount;
                    break;
                case 'bonus':
                    // ne s'applique que pour les pro dont la remise est inférieure au bonus
                    if($this->customer->type === 'pro' && $rebate > 0 && $rebate <= $this->coupon->amount):
                        $rate = $this->coupon->amount - $rebate;
                        $discount = $sum * ($rate/100);
                    endif;
                    break;
            } 
            $discounts[] = (object)['id'=> $this->coupon->id, 'type' => 'coupon', 'designation' => $this->coupon->designation, 'value' => $discount];  
                       
        }
        if($this->customer->type === 'pro'  && $rebate > 0){
            $discount = ($item_total + $tax_total) * ($rebate / 100);
            $percent = (int)$rebate; 
            $discounts[] = (object)['type' => 'rebate', 'designation' => 'Remise PRO ' . $percent .'%', 'value' => $discount];                   
        } 
        if($this->reedem){
			foreach($this->reedem as $v) {
	        	$v->designation = $v->designation . ' ' . $v->serial_key;
	        	$v->type = 'reedem';
	        	$v->value = $v->used_amount;
	        	
	        	$discounts[] = $v;
		    }
        }        
        $discount = array_sum(array_column($discounts, 'value'));
        unset($this->discounts);

        
        $amount = [
            'value' => number_format(($tax_total + $item_total + $com_shipping - $discount - 0), 2, '.', ''),           
            'currency_code' => $this->currency_code,
            'breakdown' => [ 
                'item_total' => [ 
                    'value' =>  $item_total, 
                    'currency_code' => $this->currency_code
                    
                ],
                'shipping' => [
                 'value' => number_format($com_shipping,2,'.', ''), 
                 'currency_code' => $this->currency_code
                ],
                'tax_total' => [ 
                    'value' => number_format($tax_total, 2, '.', ''), 
                    'currency_code' => $this->currency_code 
                ],
                'discount' => [
                    'value' =>  number_format($discount, 2, '.', ''),
                    'currency_code' => $this->currency_code
                ],
                'handling' => [
                    'value' => '0.00', 
                    'currency_code' => $this->currency_code
                ],
                'shipping_discount' =>  [ 
                    'value' => '0.00', 
                    'currency_code' => $this->currency_code]
            ]
        ];
        $this->amount = json_decode(json_encode($amount, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES |JSON_INVALID_UTF8_SUBSTITUTE));
    }    

    /**
     * 
     *
     * @return     string  JSON
     */
    public function __toString()
    {  
        $deliverable = false;
        $categories = array_column($this->items, 'category');
        if(in_array('PHYSICAL_GOODS', $categories)) $deliverable = true;
        $purchase = [
            'intent'            => 'CAPTURE',
            'application_context'   => array(
                'shipping_preference'   => ($this->shipping->type === 'PICKUP_IN_PERSON' || !$deliverable) ? "NO_SHIPPING" : "SET_PROVIDED_ADDRESS"
            ),
            'purchase_units'    => [
                [
                    'reference_id'  => 'PU1',
                    'description'   => 'KUTVEK KIT GRAPHIK',
                    'custom_id'     => 'Commande ' . $this->id,
                    'invoice_id'    => 'i-'. $this->id . '-' . uniqid(),
                    'amount'        => $this->amount,
                    'items' => $this->items
                ]
            ]
        ];
        if($this->shipping->type === 'SHIPPING' && $deliverable){
            $purchase['purchase_units'][0]['shipping'] = $this->shipping;
        }
        return json_encode($purchase, JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRESERVE_ZERO_FRACTION); 
    }
}