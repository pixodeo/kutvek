<?php
declare(strict_types=1);
namespace App\Checkout;

use App\AppAction;
use Core\Routing\RouterInterface;
use Exception;

class Thanks extends AppAction {

	public function __construct(RouterInterface $_router)
	{
		parent::__construct($_router);
		$this->_layout = 'thanks';
		$this->_view = 'checkout.thanks';
	}	

	public function __invoke()
	{	
		try {			
			$this->_content = $this->partial();
			$this->_response->getBody()->write($this->getBody());			
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