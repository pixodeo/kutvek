<?php
declare(strict_types=1);
namespace App\Section;

use Core\Action;
use Core\Request\UrlQueryResult;
use Domain\Table\Section;
use Library\HTML\{TraitSanitize, TraitPagination};
use App\Section\Filters\{Vehicle};
use stdClass;


/**
 * Renvoie la liste des véhicules liés à la catégorie produits
 */
final class VehicleFilter extends Action implements UrlQueryResult {
	
    use TraitSanitize, TraitPagination;
    private  false|object $_customer = false;
    private false|object $_params = false;  
    public stdClass $queryResult;
    private Section $_table;
    private string $_cards;

    /**
     *
     * Récupérer toutes les valeurs correspondant à un filtre 
     * Exemple design  on récupère tous les designs qu'on retrouve sur les produits de cette category
     * 
     */
    public function __invoke()        
    {
        
        $queries = $this->getRequest()->getQueryParams();        
        $this->_filters($queries);        
        $this->_table = new Section($this->_setDb());
        $this->_table->setRoute($this->_route); 
        $this->_table->setSection($this->queryResult); 
        $vehicles = $this->_table->filtervehicles();
        $this->_path = dirname(__FILE__);
        $this->_view = 'Filters.vehicles';
        $body = $this->partial(compact('vehicles'));       
        $this->_response->getBody()->write($body);
        return $this->_response;       
    }

    /**
     * paramètres de filtres envoyés via url 
     * 
     *
     * @param      <type>  $queries  The queries
     */
    private function _filters($queries) {
        $queries = array_filter($queries);
        foreach($queries as $k => $query){
            switch ($k) {
                case 'family':
                    $this->queryResult->family = explode(',',$query);
                    break;
                case 'brand':
                    $this->queryResult->brand = explode(',',$query);
                    break;
                case 'model':
                    $this->queryResult->model = explode(',',$query);
                    break;
                case 'vehicle':
                    $this->queryResult->vehicle = explode(',',$query);
                    break;
                case 'color':
                    $this->queryResult->color = explode(',',$query);
                    break;
                case 'design':
                    $this->queryResult->design = explode(',',$query);
                    break;               
            }
        }
    }

    public function setQueryResult(stdClass $query): void {$this->queryResult = $query; }    

    public function setCustomer(){
        $session_token = $this->getRequest()->getCookieParams()['session_token'] ?? false;
        if($session_token) $this->_customer = $this->getCookie($session_token);
    }
    
    public function isCustomerPro(): bool {        
        return $this->_customer ? $this->_customer->type === 'pro' : false;        
    }      
}