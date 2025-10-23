<?php
declare(strict_types=1);
namespace App\Checkout;

use App\AppAction;
use Domain\Table\Checkout;
use Error;
use Exception;

/**
 * Ajoute une adresse de livraison à domicile 
 */
class AddShippingAddress extends AppAction {

	private Checkout $_checkoutTable;
	public function __invoke()
	{
		$this->_checkoutTable = new Checkout($this->_setDb());
		$this->_checkoutTable->setRoute($this->_route);		
		try {
			
			$method =  strtolower($this->getRequest()->getMethod());
			match($method){
				'post' => $this->_save(),
				default => $this->_popup()
			};
		}
		catch(Error | Exception $e){
			$this->_response = $this->_response->withStatus(400); 
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
			$body = json_encode(['msg' => $e->getMessage()]);			
			$this->_response->getBody()->write($body);
			return $this->_response;
		}	
	}

	private function _popup(){
		try{
			$this->_view = 'checkout.add-shipping-address';
			return $this->_response->getBody()->write($this->partial());
		}
		catch( Error | Exception $e){
			throw $e;
		}
	}

	private function _save(){}
}