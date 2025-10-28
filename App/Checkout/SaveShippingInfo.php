<?php
declare(strict_types=1);
namespace App\Checkout;

use Core\Action;
use App\Checkout\Types\{Pickup, ChronoRelay, ChronoStd};
use Exception;

class SaveShippingInfo extends Action {	

	public function __invoke(int $id)
	{	
		try {
			$post = $this->getRequest()->getParsedBody();
			$this->_middleware = match((int)$post['delivery_type']){
				1 => new ChronoRelay($this->_router),
				2 => new Pickup($this->_router),
				default => new ChronoStd($this->_router)
			};
			$this->handle($this->getRequest());
            return $this->_response;		
		}
		catch(Exception $e){
			$this->_response = $this->_response->withStatus(400); 
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
			$body = json_encode(['msg' => $e->getMessage()]);			
			$this->_response->getBody()->write($body);
			return $this->_response;
		}
	}
}