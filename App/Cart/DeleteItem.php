<?php
declare(strict_types=1);
namespace App\Cart;

use Core\Action;
use Domain\Table\Checkout;
use Domain\Entity\Checkout\Cart as CheckoutCart;

class DeleteItem extends Action {
	private Checkout $_checkoutTable;	

	public function __invoke()
	{
		$this->_checkoutTable = new Checkout($this->_setDb());
		$this->_checkoutTable->setRoute($this->_route);	
		$this->_checkoutTable->beginTransaction();	
		try {		
			$queries = $this->getRequest()->getQueryParams();
			$id = (int)$queries['id'];
			$itemId = (int)$queries['item'];
			$this->_checkoutTable->setTable('order_item');
			$this->_checkoutTable->delete($itemId);
			$this->_checkoutTable->setTable('_order');
			$cart = $this->_checkoutTable->cart($id);
			$this->_checkoutTable->commit();
			$this->_view = $cart instanceof CheckoutCart ? 'cart.overview' : 'checkout.cart-empty';	
			$body = $this->partial(compact('cart', 'id'));
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
}