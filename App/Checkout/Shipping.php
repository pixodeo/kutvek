<?php
declare(strict_types=1);
namespace App\Checkout;
use App\AppAction;
use Domain\Table\Checkout;
use Domain\Entity\Checkout\Cart as CheckoutCart;

class Shipping extends AppAction {
	private Checkout $_checkoutTable;
	protected int $orderId;
	protected false|int $userSessionId = false;

	public function __invoke(int $id)
	{
		$method =  strtolower($this->getRequest()->getMethod());
		if($method === 'post'):
			$this->_middleware = new SaveShippingInfo($this->_router);
			$this->handle($this->getRequest());
            return $this->_response;
		endif;
		try {
			$cookies = $this->getRequest()->getCookieParams();
			$userConnected = $this->getCookie($cookies['session_token']??false);
			if($userConnected) $this->userSessionId = (int)$userConnected->uid;			
			$this->_checkoutTable = new Checkout($this->_setDb());
			$this->_checkoutTable->setRoute($this->_route);
			$this->_checkoutTable->setUserSessionId($this->userSessionId);
			$this->orderId = $id;	
			$cart = $this->_checkoutTable->cart($this->orderId);
			$this->_view = $cart instanceof CheckoutCart ? 'checkout.delivery' : 'checkout.cart-empty';	
			$this->_layout = 'delivery';
			$this->_content = $this->partial(compact('cart', 'id'));
			$currency_code = $cart->currency_code;
			$this->_response->getBody()->write($this->getBody(compact('currency_code')));
			return $this->_response;				
		}
		catch(\Exception $e){
			$this->_response = $this->_response->withStatus(400); 
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
			$body = json_encode(['msg' => $e->getMessage()]);			
			$this->_response->getBody()->write($body);
			return $this->_response;
		}
	}	
}