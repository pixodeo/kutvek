<?php
declare(strict_types=1);
namespace App\Checkout;

use App\AppAction;
use Domain\Table\Checkout;
use Domain\Table\CheckoutPay;
use App\Checkout\PSP\PayPal\Sandbox AS PayPal;

class Payment extends AppAction {

	private Checkout $_checkoutTable;
	protected PayPal $paypal;

	public function __invoke(int $id)
	{
		$this->_checkoutTable = new CheckoutPay($this->_setDb());
		$this->_checkoutTable->setRoute($this->_route);
		try {
			$cart = $this->_checkoutTable->cart($id);
			if($cart === null || !$cart->ready_to_ship):
				$this->_response = $this->_response->withStatus(303);
				$location = $this->url('checkout.shipping', ['fqdn' => 1,'queries'=>['id'=> $id]]);
				$this->_response = $this->_response->withHeader('Location', $location);
				return $this->_response;
			endif;
			$this->paypal = new PayPal($this->_route);
			$this->paypal->setCurrencyCode($cart->currency_code);
			$this->_view = 'checkout.payments';	
			$this->_layout = 'payments';
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