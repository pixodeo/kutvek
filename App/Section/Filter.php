<?php
declare(strict_types=1);
namespace App\Section;

use Core\Action;
use Core\Component;
use Core\Request\UrlQueryResult;
use Domain\Table\Section;
use Exception;
use Library\HTML\{TraitString, TraitPagination};
use stdClass;
use Core\Library\TraitCookie;

/**
 * Filtre les produits de la section en cours 
 */
final class Filter extends Action implements UrlQueryResult {
	
    use TraitString, TraitPagination, TraitCookie;
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
        $this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
        $queryParams = $this->getRequest()->getQueryParams();        
        $this->_filters($queryParams);
        
        $this->_table = new Section($this->_setDb());
        $this->_table->setRoute($this->_route); 
        $this->_table->setSection($this->queryResult);        
        $this->_items = $this->_table->itemsInSectionWithFilters();
        if(array_key_exists('page', $queryParams)) $this->setCurrentPage((int)$queryParams['page']);    
        $this->paginate();
        $slices = $this->getSlices();   
        $cards =$this->_table->cards($slices);
        $this->_view = 'widgets.pagination';
        $pagination = $this->partial();
        $this->_path = dirname(__FILE__);
        $this->_view = 'cards';
        $this->_cards = $this->partial(compact('cards'));
        $json =   json_encode(['queryResult'=>$this->queryResult, 'queryParams' => $queryParams, 'cards'=>$this->_cards, 'pagination' => $pagination],JSON_UNESCAPED_SLASHES);
        $this->_response->getBody()->write($json);
        return $this->_response;

        $cookie = false;
        $countryCurrencyToken = $this->getRequest()->getCookieParams()['country_currency'] ?? false;        
        if ($countryCurrencyToken) {
           $cookie = $this->getCookie($countryCurrencyToken);
           $this->setCountry($cookie->country);
           $this->setCurrency($cookie->currency);                   
        }
        $this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');   
        $this->getContent($slug);
        $behavior = $this->loadBehavior();
        
        $json = json_encode(['slug'=>$slug, 'data'=>$behavior($filter)]);
        $this->_response->getBody()->write($json);
        return $this->_response;
    }

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