<?php
declare(strict_types=1);
namespace Library;
use Domain\Entity\{Order AS OE};
use Exception;

trait TraitCart {
	public array $data = [];

	protected function createOrder(){
		try {
			if(!isset($this->data['item']['id_order'])): 
				$user = $this->getCookie($this->getRequest()->getCookieParams()['session_token'] ?? false);
				$country = $this->getCookie($this->getRequest()->getCookieParams()['country_currency'] ?? false);		
				$order = ['ip_address' => $_SERVER['REMOTE_ADDR'], 'locale' => 'fr', 'paid' => 0];
				if($user) $order['id_user'] = $user->uid;
				if($country) $order['country_code'] = $country->country;
				$this->_orderTable->create($order);
				$this->data['item']['id_order'] = (int)$this->_orderTable->lastInsertId();
			endif;
			$this->data['item']['id_order'] = (int)$this->data['item']['id_order'];
		}
		catch(Exception $e){throw $e;}		
	}

	public function getCart(array $items = []){
		$order = new OE;
		if(count($items) <= 0) return $order;
		$order->id = $items[0]->order_id;
		$order->customer = json_decode($items[0]->customer);
		$order->country_shipping = $items[0]->country_shipping;    	
    	$order->currency_code = $items[0]->currency_code;
    	$order->created = $items[0]->created;
    	$order->vat = $items[0]->vat;
    	$order->items = $items;
    	$order->delivery = $items[0]->delivery !== null ? json_decode($items[0]->delivery) :  (object)['type'=>$items[0]->delivery_name, 'cost' => $items[0]->com_shipping];
    	$order->bill = json_decode($items[0]->billing ?? '{}');
    	$order->coupon = json_decode($items[0]->coupon);
    	$order->invoice = $items[0]->invoice;  
    	$order->amount();  	
    	return $order;
	}
}