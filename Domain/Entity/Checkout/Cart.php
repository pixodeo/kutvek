<?php
declare(strict_types=1);

namespace Domain\Entity\Checkout;
use Core\Domain\Entity;

class Cart extends Entity {
	public int $id;    
	public string $created;
    public object|false $reedem = false;
    public object $customer;
    public object $delivery;    
    public object $bill;
	public array $items = [];    
    

    public function __get($key) {       
        $method = "_{$key}";
        if(!method_exists($this, $method)) return '';
        $this->{$key} = $this->{$method}();     
        return $this->{$key};
    }

    public function hasShippingAddress(){
        return property_exists($this->delivery, 'address');
    }

    public function addressId(){
        return property_exists($this->delivery, 'address') ? 'd-'.$this->delivery->address->id : (property_exists($this->bill, 'address') ? 'd-'.$this->bill->address->id : false);
    }

	private function _delivery_address(){
        
        $address = (array)$this->delivery->address; 
        $head = $this->delivery->type; 
        $relay_name = !empty($address['relay_name']) ? "<p>{$address['relay_name']}</p>" : "";
        $line_1 = !empty($address['address_line_1']) ? "<p>{$address['address_line_1']}</p>" : "";
        $line_2 = !empty($address['address_line_2']) ? "<p>{$address['address_line_2']}</p>" : "";
        $line_3 = isset($address['admin_area_1']) ? "<p>{$address['admin_area_1']}, {$address['admin_area_2']}, {$address['postal_code']}, {$address['country_name']} </p>" : "<p>{$address['admin_area_2']}, {$address['postal_code']}, {$address['country_name']} </p>"; 
        $name =  $this->delivery->fullname;
        $phones = array_filter([$this->delivery->phone, $this->delivery->phone2]);
        $contact = implode(' / ', $phones);
        return <<<TEXT
        <div id="personal-address" data-id="d-{$address['id']}" >
        <p class="address-head"><span>{$head}</span></p>
        <p><b>{$name}</b><p>
        {$relay_name}
        {$line_1}
        {$line_2}
        {$line_3}
        <p>{$contact}</p>
        </div>
        TEXT;     
    }

    private function _customer_address(){        
        $address = (array)$this->customer->shipping_at->address;  
        if(empty($address)) return '';
        $this->customer_address_id =  "d-{$address['id']}";     
        $line_1 = !empty($address['address_line_1']) ? "<p>{$address['address_line_1']}</p>" : "";
        $line_2 = !empty($address['address_line_2']) ? "<p>{$address['address_line_2']}</p>" : "";
        $line_3 = isset($address['admin_area_1']) ? "<p>{$address['admin_area_1']}, {$address['admin_area_2']}, {$address['postal_code']}, {$address['country_name']} </p>" : "<p>{$address['admin_area_2']}, {$address['postal_code']}, {$address['country_name']} </p>"; 
        $name =  $this->customer->shipping_at->fullname;
        $phones = array_filter([$this->customer->shipping_at->phone, $this->customer->shipping_at->phone2]);
        $contact = implode(' / ', $phones);
        return <<<TEXT
        <div id="personal-address" class="address" data-id="d-{$address['id']}" >        
        <p class="title">{$name}<p>
        $line_1
        $line_2
        $line_3
        <p>$contact</p>
        </div>
        TEXT;     
    }

    private function _customer_address_id(){
        $address = (array)$this->customer->shipping_at->address;  
        if(empty($address)) return '';
        return "d-{$address['id']}";    
    }

    private function _default_zipcode(){
        $address = (array)$this->delivery->address;
        return !empty($address) ? $address['postal_code'] ?? '01190' : '01190';  
    }
    private function _default_city(){
        $address = (array)$this->delivery->address;
        return !empty($address) ? $address['admin_area_2'] ?? 'Saint Bénigne' : 'Saint Bénigne'; 
    }
    private function _billing_address(){        
        $address = (array)$this->bill->address; 
        $bill_line_1 = !empty($address['address_line_1']) ? "<p>{$address['address_line_1']}</p>" : "";
        $bill_line_2 = !empty($address['address_line_2']) ? "<p>{$address['address_line_2']}</p>" : "";
        $bill_line_3 = isset($address['admin_area_1']) ? "<p>{$address['admin_area_1']}, {$address['admin_area_2']}, {$address['postal_code']}address['country_name']} </p>" : "<p>{$address['admin_area_2']}, {$address['postal_code']}, {$address['country_name']} </p>"; 
        $bill_name =  $this->bill->fullname;
        $phones = array_filter([$this->bill->phone, $this->bill->phone2]);
        $bill_contact = implode(' / ', $phones);
        return <<<TEXT
        <p><b>$bill_name</b><p>
        $bill_line_1
        $bill_line_2
        $bill_line_3
        <p>$bill_contact</p>
        TEXT;
    }

    private function _pickup_address() {
        $name = $this->customer->fullname ?? '';
        return <<<ADDRESS
        <div id="pickup-address" class="address" data-id="52742">
        <p class="title">{$name}<p>
        <p class="address_line_1">KUTVEK</p>
        <p class="address_line_2">ZA Pont de Vaux Est</p>
        <p><span class="postal_code">01190</span>. <span class="admin_area_2">Saint Bénigne</span>, <span>FR</span></p>
        </div>
        ADDRESS;
    }



    public function amount(){
		$tax_total = 0;
		$item_total = 0;		
		$com_shipping = (float)$this->delivery->cost;
		$rebate = $this->customer->rebate;
        $array_item_total = [];
        $array_tax_total = [];
		foreach($this->items as $item):		
			$prices = array_filter((array)$item->item_price);
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
                'value' =>  number_format($item_ht, 2, '.', ''),
                'format'    =>  $this->_setPrice($item_ht , 2, $item->currency_code)               
            ];
            $item->tax = (object)[
                'currency_code' => $item->currency_code, 
                'value' => $this->vat > 0 ? number_format($item_tax, 2, '.', '') : '0.00' ,
                'format'    =>  $this->_setPrice($this->vat > 0 ? number_format($item_tax, 2, '.', '') : '0.00'  , 2, $item->currency_code)       
            ];
            
            $item->value =  number_format($item_ht * $item->qty, 2, '.', '');            
            $item->value_format  = $this->_setPrice(($item_ht + ($this->vat > 0 ? $item_tax : 0)) * $item->qty, 2, $item->currency_code);
            $array_item_total[] =  $item->value;  
            $array_tax_total[] =  $this->vat > 0 ? number_format($item_tax * $item->qty, 2, '.', '') : '0.00';
            $item->item_price;
		endforeach;
		$item_total = array_sum($array_item_total);
        $tax_total = array_sum($array_tax_total);
        $this->array_itm_t = $item_total;
        $this->array_itm_tt = $tax_total;
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
            $discounts[] = (object)['id'=> $this->coupon->id, 'type' => 'coupon', 'designation' => $this->coupon->designation, 'value' => $discount, 'value_format' => $this->_setPrice($discount, 2, $this->currency_code)];               
        }
        if($this->customer->type === 'pro'  && $rebate > 0){
            $discount = ($item_total + $tax_total) * ($rebate / 100);
            $percent = (int)$rebate; 
            $discounts[] = (object)['type' => 'rebate', 'designation' => 'Remise PRO ' . $percent .'%', 'value' => $discount, 'value_format' => $this->_setPrice($discount, 2, $this->currency_code)];                   
        } 
        if($this->reedem){
			foreach($this->reedem as $v) {
	        	$v->designation = $v->designation . ' ' . $v->serial_key;
	        	$v->type = 'reedem';
	        	$v->value = $v->used_amount;
	        	$v->value_format = $this->_setPrice((float)$v->used_amount, 2, $this->currency_code);
	        	$discounts[] = $v;
		    }
        }        
        $discount = array_sum(array_column($discounts, 'value'));
        
        $this->discounts = $discounts;
        $amount = [
            'value' => number_format(($tax_total + $item_total + $com_shipping - $discount - 0), 2, '.', ''),
            'value_format' => $this->_setPrice(($tax_total + $item_total + $com_shipping - $discount - 0), 2, $this->currency_code),
            'currency_code' => $this->currency_code,
            'breakdown' => [ 
                'item_total' => [ 
                    'value' =>  number_format($item_total,2,'.', '') , 
                    'currency_code' => $this->currency_code,
                    'format'    => $this->_setPrice($item_total , 2, $this->currency_code)
                ],
                'shipping' => [
                 'value' => number_format($com_shipping,2,'.', ''), 
                 'currency_code' => $this->currency_code,
                 'format'    => $this->_setPrice($com_shipping , 2, $this->currency_code) 
                ],
                'tax_total' => [ 
                    'value' => number_format($tax_total, 2, '.', ''), 
                    'currency_code' => $this->currency_code,
                    'format'    =>  $this->_setPrice($tax_total , 2, $this->currency_code)   
                ],
                'discount' => [
                    'value' =>  number_format($discount, 2, '.', ''),
                    'currency_code' => $this->currency_code,
                    'format'    =>  $this->_setPrice($discount , 2, $this->currency_code)   
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
     * Retourne les infos nécessaires pour estimer les frais de port pour la livraiosn à domicile
     * Chercher dans adresse de livraison si existante / adresse de livraison par défaut / adresse de facturation par défaut
     * @return     array  ( description_of_the_return_value )
     */
    public function shippingFeesInfo(): array{
        $infos = ['country_code' => 'FR', 'suppl_corse'=> 0, 'customer_type' => property_exists($this->customer, 'type') ?  $this->customer->type : 'std'];
        $total_weight = array_sum(array_column($this->items, 'weight')) / 1000;
        $infos['weight'] = $this->volumetric_weight > $total_weight ? $this->volumetric_weight : $total_weight;

        if(property_exists($this->delivery, 'address')) {
            $infos = array_merge($infos, [
                'country_code' => $this->delivery->address->country_code, 
                'suppl_corse' => substr($this->delivery->address->postal_code, 0, 2) == '20' && $this->delivery->address->country_code == 'FR' ? 1 : 0 ]);
            return $infos;
        }
        if(property_exists($this->bill, 'address')) {
            $infos = array_merge($infos, [
                'country_code' => $this->bill->address->country_code, 
                'suppl_corse' => substr($this->bill->address->postal_code, 0, 2) == '20' && $this->bill->address->country_code == 'FR' ? 1 : 0 ]);
            return $infos;
        }
        if($this->country_shipping !== null && $this->postal_code_default !== null) {
            $infos = array_merge($infos, [
                'country_code' => $this->country_shipping, 
                'suppl_corse' => substr($this->postal_code_default, 0, 2) == '20' && $this->country_shipping == 'FR' ? 1 : 0 ]);
            return $infos;
        }
        return $infos;
    }

    public function products(): array {
        return array_filter(array_column($this->items, 'product'));        
    }

	protected function _setPrice($price = 0,  $digits = 0, $currency = 'EUR', $l10n = 'fr')
    {
        $a = new \NumberFormatter($l10n, \NumberFormatter::CURRENCY);
        $a->setAttribute(\NumberFormatter::FRACTION_DIGITS, $digits);
        return $a->formatCurrency((float)$price, $currency); // outputs €12.345,12
    }	

    public function chronoClassic(){
        $fees = $this->estimations;
        if($fees->chrono_classic !== null && $fees->classic > 0):
            $cost = $fees->c_classic();
            $price = $fees->format($cost);
        return <<<TEXT
        <div class="delivery-method chrono-classic ">
            <p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO Classic</small></p>
            <p><span data-i18n="cost">Coût</span> <small class="cost">{$price}</small><small class="free hide" data-i18n="free-shipping">Offerts</small></p>
            <p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="3-working-days">3 jours ouvrables</span></p>
            <p class="btns action">                                     
                <button type="submit" data-type="classic" class="contained small dark" name="com_shipping" value="{$cost}" data-i18n="choose-delivery-method" id="std-classic">Choisir</button>
            </p>
        </div>
        TEXT;   
        endif;
        return '';
    }
    /**
     * On a un prix chrono express et clasic à 0 express c'est par avion
     */
    public function chronoExpressIntl(){
        $fees = $this->estimations;
        if($fees->chrono_express !== null && (int)$fees->classic < 1 && $fees->chrono_13 === null):
            $cost = $fees->c_express();
            $price = $fees->format($cost);
        return <<<TEXT
        <div class="delivery-method chrono-intl">
            <p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO Internationnal</small></p>
            <p><span data-i18n="cost">Coût</span> <small class="cost">{$price}</small><small class="free hide" data-i18n="free-shipping">Offerts</small></p>
            <p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="3-working-days">3 jours ouvrables</span></p>
            <p class="btns action">                                     
                <button type="submit" data-type="internationnal" class="contained small dark" name="com_shipping" value="{$cost}" data-i18n="choose-delivery-method" id="std-intl">Choisir</button>
            </p>
        </div>
        TEXT;   
        endif;
        return '';
    }

    /**
     * On a un prix chrono express et clasic à 1 express c'est par avion
     */
    public function chronoExpress(){
        $fees = $this->estimations;
        if($fees->chrono_express !== null && (int)$fees->classic > 0):
            $cost = $fees->c_express();
            $price = $fees->format($cost);
        return <<<TEXT
        <div class="delivery-method chrono-express">
            <p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO Express</small></p>
            <p><span data-i18n="cost">Coût</span> <small class="cost">{$price}</small></p>
            <p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="1-working-days">1 jour ouvrable</span></p>    
            <p class="btns action">                                         
                <button type="submit" data-type="express" class="contained small dark" name="com_shipping" value="{$cost}" data-i18n="choose-delivery-method" id="std-express">Choisir</button>
            </p>    
        </div> 
        TEXT;
        endif;
        return '';
    }

    public function chrono13(){
        $fees = $this->estimations;
        if($fees->chrono_13 !== null && (int)$fees->classic <= 0):
            $cost = $fees->c_13();
            $price = $fees->format($cost);
        return <<<TEXT
        <div class="delivery-method chrono-13">
            <p class="shipping-head">
                <span>CHRONO 13</span>
                <span class="cost">{$price}</span> 
                <input type="radio" class="input-radio" name="delivery_type" id="chrono-13" data-cost="{$cost}" value="4" />
                <label for="chrono-13"></label> 
            </p>
            <p class="shipping-info">
                <span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="1-working-days">1 jour ouvrable</span>
            </p>                
        </div> 
        TEXT;
        endif;
        return '';    
    }
}