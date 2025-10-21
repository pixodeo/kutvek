<?php
declare(strict_types=1);
namespace App\DealsCorner;

use App\AppAction;

use Domain\Table\{Order AS O, GoodDeal};
use Domain\Entity\{Order};
use Exception;
use Library\HTML\{TraitView,TraitSanitize};

final class AddToCart extends AppAction {
	use TraitView, TraitSanitize;

	protected GoodDeal $_table;
	protected O $_orderTable;

	public false|object $product;
	private int $_cartId;

	public function __invoke(){
		$queries = $this->getRequest()->getQueryParams();
		$post = $this->getRequest()->getParsedBody();
		
		$this->_orderTable = new O($this->_setDb());
		$this->_orderTable->setRoute($this->_route);
		
		$this->_orderTable->beginTransaction();			
		try {	
			if(!isset($post['item']['id_order'])){		
				$this->_order();
			}else {			
				$this->_cartId = (int)$post['item']['id_order'];
			}
			$this->_item($post);
			$this->_orderTable->commit();

			$items = $this->_orderTable->items($this->_cartId);	
			$cart = $this->_cart($items);

			$this->_view = 'checkout.cart';			
			$this->setResponse($this->getResponse()->withStatus(201)); 
			$this->getResponse()->getBody()->write($this->partial(compact('cart')));
		}
		catch(Exception $e){
			$this->_orderTable->rollback();
			$this->setResponse($this->getResponse()->withStatus(400)); 	
			$this->setResponse($this->getResponse()->withHeader('Content-Type', 'application/json;charset=utf-8')); 	
			$this->getResponse()->getBody()->write(json_encode(['error' => $e->getMessage()]));
		}
		finally {
			return $this->getResponse();
		}		
	}

	private function _order(){
		$user = $this->getCookie($this->getRequest()->getCookieParams()['session_token'] ?? false);
		$country = $this->getCookie($this->getRequest()->getCookieParams()['country_currency'] ?? false);			
		$order = ['ip_address' => $_SERVER['REMOTE_ADDR'], 'locale' => $this->getL10n(), 'paid' => 0];
		if($user) $order['id_user'] = $user->uid;
		if($country) $order['country_code'] = $country->country;
		$this->_orderTable->create($order);
		$this->_cartId = (int)$this->_orderTable->lastInsertId();
	}

	private function _cart(array $items = []){

		$order = new Order;
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



	private function _item(array $post = []){
		$item_price = $this->_calculatePrice($post);
		$data = [
			'id_order'              => $this->_cartId,
            'qty'                   => $post['item']['qty'],  
            'weight'                => $post['item']['weight'],           
            'product'               => $post['item']['product'],            
            'description'           => $this->_designation($post['item']['description']),
            'webshop_currency'      => $post['item']['webshop_currency'],
            'currency'              => $post['item']['currency'],
            'product_url'           => $post['item']['product_url'],
            'product_img'           => $post['item']['product_img'],
            'webshop_price'         => array_sum($post['item']['price']),
            'item_price'            => $item_price,
            'tax_included'          => $post['item']['tax_included'],      
            'item_paid'             => 0,               
            'item_category'         => $post['item']['item_category'],     
            'item_treated'          => 0,        
            'workspace'             => $post['item']['workspace']
		];
		$args = [
			'id_order'				=> FILTER_VALIDATE_INT,
			'qty'					=> FILTER_VALIDATE_INT,
			'weight'				=> FILTER_VALIDATE_INT,
			'product'               => FILTER_VALIDATE_INT,
			'description'           => FILTER_DEFAULT,
			'webshop_currency'      => FILTER_DEFAULT,
            'currency'              => FILTER_VALIDATE_INT,
            'product_url'           => FILTER_VALIDATE_URL,
            'product_img'           => FILTER_VALIDATE_URL,
            'webshop_price'         => FILTER_VALIDATE_FLOAT,
            'item_price'            => FILTER_DEFAULT,
            'tax_included'          => FILTER_VALIDATE_INT,
            'item_paid'             => FILTER_VALIDATE_INT,
            'item_category'         => FILTER_VALIDATE_INT, 
            'item_treated'          => FILTER_VALIDATE_INT,       
            'workspace'             => FILTER_VALIDATE_INT
        ];

		$line = filter_var_array($data, $args, false);
		$this->_orderTable->setTable('order_item');
		$this->_orderTable->create($line);
	}

	

	private function _calculatePrice(array $post): string {
        // Attention la finition c'est *qty de produit
        $qty = (int)$post['item']['qty'];
        $price = $post['item']['price'];
        if(array_key_exists('discount', $price)):
            //$sales = $price['discount'];
            //unset($price['discount']); 
        else: 
            //$sales = 0;           
        endif;
        $prices = array_merge(['finish' => 0, 'product' => 0, 'opts' => 0, 'accessories' => 0, 'premium' => 0, 'seat_cover' => 0, 'rim_sticker' => 0, 'door_stickers' => 0, 'plastics' => 0], $price);

        $prices['finish'] = (float)$prices['finish'] * $qty;
        /*if($sales > 0):
            $p = $prices['product'] * $qty;
            $res = $p - ($p / (100/$sales));
            $prices['product'] = (float) $res;
        else:
            $prices['product'] = (float)$prices['product'] * $qty;
        endif;*/
        $prices['product'] = (float)$prices['product'] * $qty;
        $prices['accessories'] = (float)$prices['accessories'] * $qty;
        $prices['premium'] = (float)$prices['premium'] * $qty;
        $prices['seat_cover'] = (float)$prices['seat_cover'] * $qty;
        $prices['rim_sticker'] = (float)$prices['rim_sticker'] * $qty;
        $prices['door_stickers'] = (float)$prices['door_stickers'] * $qty;
        return json_encode($prices,JSON_PRESERVE_ZERO_FRACTION);
    }

    private function _designation(string $designation){
    	return $this->specialchars_decode($designation . '- Bonnes affaires');
    }


}	