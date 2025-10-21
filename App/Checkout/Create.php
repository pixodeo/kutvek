<?php
declare(strict_types=1);
namespace App\Checkout;

use App\Checkout\PSP\TraitServiceProvider;
use Core\Action;
use Exception;

final class Create extends Action
{
	use TraitServiceProvider;
	public function __invoke()
	{
		try {	
			$queries = $this->getRequest()->getQueryParams();
       		$psp = $queries['psp'];		
			$component = $this->_psp($psp);
			if(!$component) throw new Exception("Error Processing Request, No PaymentServiceProvider", 1);
			$json = $component->create();			
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');
			$this->_response->getBody()->write($json);
			return $this->_response;
		}
		catch(Exception $e){
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');
			$this->_response->getBody()->write(['msg' => $e->getMessage()]);
		}		
	}
}