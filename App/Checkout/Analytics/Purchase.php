<?php 
declare(strict_types=1);
namespace App\Checkout\Analytics;
use Core\Component;
use Domain\Table\Table\AnalyticsEvent;
use Exception;

/**
 *  Envoi à analytics (GA4) l'event purchase via js (push dans le dataLayer) 
 */
class Purchase  extends Component{
	protected AnalyticsEvent $table;

	public function __invoke(int $id){
		$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');    
		try{
			$this->table = new AnalyticsEvent($this->_setDb());    
        	$this->table->setRoute($this->_route);
        	$dataLayer = $this->table->eventPurchase($id);
        	$this->_response->getBody()->write($dataLayer);
        	return $this->_response;
	    }
	    catch(Exception $e){
	    	$this->_response = $this->_response->withStatus(400);
	    	$json = (object)['msg' => $e->getMessage()];
	    	$this->_response->getBody()->write($json);
	    	return $this->_response;
				
		}
    }
}