<?php
declare(strict_types=1);
namespace App\Cart;

use App\AppAction;
use Domain\Table\Checkout;
use Domain\Entity\Checkout\Cart as CheckoutCart;
use Exception;

class UpdateItemQty extends AppAction {
	private Checkout $_checkoutTable;	
	private int $_itemId;
	private int $_newQty;
	private string $_prices;

	public function __invoke()
	{
		$this->_checkoutTable = new Checkout($this->_setDb());
		$this->_checkoutTable->setRoute($this->_route);
		$this->_checkoutTable->beginTransaction();
		try {		
			$queries = $this->getRequest()->getQueryParams();
			$body = json_decode($this->getRequest()->getBody()->getContents());
			$id = (int)$queries['id'];
			$this->_itemId = (int)$queries['item'];
			$this->_newQty = (int)$body->qty;
			$this->_updatePrices();
			$this->_checkoutTable->setTable('order_item');
			$this->_checkoutTable->update($this->_itemId, ['qty' => $this->_newQty, 'item_price' => $this->_prices]);
			$this->_checkoutTable->setTable('_order');
			$cart = $this->_checkoutTable->cart($id);
			$this->_view = $cart instanceof CheckoutCart ? 'cart.overview' : 'checkout.cart-empty';	
			$body = $this->partial(compact('cart', 'id'));
			$this->_checkoutTable->commit();
			$this->_response->getBody()->write($body);
			return $this->_response;	
		}
		catch(\Exception $e){
			$this->_checkoutTable->rollback();
			$this->_response = $this->_response->withStatus(400); 
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');					
			$this->_response->getBody()->write($e->getMessage());
			return $this->_response;
		}	
	}

	private function _updatePrices() {
		try{
			$sql = "SELECT qty, item_price FROM order_item WHERE id = :id";
			$q = $this->_checkoutTable->query($sql,['id' => $this->_itemId], true);		
			if(!$q) throw new Exception('No order_item found');
			$old = (int)$q->qty;
			$new = $this->_newQty;
			$prices = json_decode($q->item_price, true);
			array_walk($prices, function (&$value, $key) use ($old, $new ) {
				$value = match($key){
					'product', 'premium', 'finish', 'accessories' => ((float)$value / $old) * $new,
					default => $value
				};
				/*switch($key){
					case 'product':
						$unitaryPrice = (float)$value / $old;
						$value = $unitaryPrice * $new;
					break;
					case 'premium':
						$unitaryPrice = (float)$value / $old;
						$value = $unitaryPrice * $new;
					break;
					case 'finish':
						$unitaryPrice = (float)$value / $old;
						$value = $unitaryPrice * $new;
					break;
				}*/  			
			});	
			$this->_prices = json_encode($prices, JSON_PRESERVE_ZERO_FRACTION, JSON_NUMERIC_CHECK);	
		}
		catch(Exception $e){
			throw $e;
		}		
	}
}