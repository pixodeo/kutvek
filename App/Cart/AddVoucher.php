<?php
declare(strict_types=1);
namespace App\Cart;

use Core\Action;
use Domain\Table\Checkout;
use Domain\Entity\Checkout\Cart as CheckoutCart;
use App\Cart\Voucher\Validator;
use Core\Routing\RouterInterface;
use Library\HTML\{Form};

class AddVoucher extends Action {
	private Checkout $_checkoutTable;
	public Form $form;

	public function __construct(protected RouterInterface $_router)
    {       
        $this->_route = $this->_router->getRoute();
        $this->form = new Form;
    }	

	public function __invoke()
	{
		$this->_checkoutTable = new Checkout($this->_setDb());
		$this->_checkoutTable->setRoute($this->_route);		
		try {
			
			$validator = new Validator($this->_route);
			$validator->setRequest($this->getRequest());
			//$this->_response->getBody()->write($validator->check());
			//return $this->_response;	
			$validator->check();
			$queries = $this->getRequest()->getQueryParams();
			$id = (int)$queries['id'];
			$cart = $this->_checkoutTable->cart($id);
			$this->_view = $cart instanceof CheckoutCart ? 'cart.overview' : 'checkout.cart-empty';	
			$body = $this->partial(compact('cart', 'id'));
			$this->_response->getBody()->write($body);
			return $this->_response;	
		}
		catch(\Exception $e){
			$this->_response = $this->_response->withStatus(400); 
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');					
			$this->_response->getBody()->write($e->getMessage());
			return $this->_response;
		}	
	}
}