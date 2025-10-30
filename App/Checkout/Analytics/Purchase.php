<?php 
declare(strict_types=1);
namespace App\Checkout\Analytics;

use Core\Action;
use Domain\Table\AnalyticsEvent;
use Exception;

/**
 *  Envoi à analytics (GA4) l'event purchase via js (push dans le dataLayer) 
 */
class Purchase  extends Action {
	protected AnalyticsEvent $table;

	public function __invoke(int $id){
		$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');    
		try{
			$this->table = new AnalyticsEvent($this->_setDb());    
        	$this->table->setRoute($this->_route);
        	$dataLayer = $this->table->eventPurchase($id);
        	$json = json_encode($dataLayer, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        	$this->_response->getBody()->write($json);
        	return $this->_response;
	    }
	    catch(Exception $e){
	    	$this->_response = $this->_response->withStatus(400);
	    	$json = json_encode((object)['msg' => $e->getMessage()]);
	    	$this->_response->getBody()->write($json);
	    	return $this->_response;				
		}
    }
}