<?php
declare(strict_types=1);
namespace Core\Routing;

use Psr\Http\Message\ServerRequestInterface;

use Vendor\Nyholm\Http\Message\Factory\Psr17Factory;
use Vendor\Nyholm\Http\Message\ServerRequestCreator;

use ReflectionClass;
use ReflectionObject;
use stdClass;
use Core\Library\{TraitCookie,TraitModel};
use Core\Domain\Table;

class Router implements IRouter {    
    use TraitCookie, TraitModel;

    private ServerRequestInterface $_request;  
    private static $_instance;
    private function __construct(){}    
    private string $_routePath = '';
    private $_l10n;
    private $_currencyCode;
    private $_countryCode;
    private Table $_table;
    private $_parts = [];
    

    public static function getInstance(): IRouter { return self::$_instance;}

    public static function load():void {
        if(is_null(self::$_instance)) { self::$_instance = new Router();}
        $psr17Factory = new Psr17Factory();        
        $creator = new ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        );
        $request = $creator->fromGlobals();
        self::$_instance->_setRequest($request);
        self::$_instance->_init();
    }

    public function getRequest(): ServerRequestInterface {return $this->_request;} 
    private function _setRequest(ServerRequestInterface $request) {$this->_request = $request;} 

    /**
     * Renvoi une route ou une erreur
     * -1- On filtre l'url, supression du paramètre de langue et et du slash au début
     */
    private function _init(){ 
       
        $path = trim($this->_request->getUri()->getPath(), '/');
        $this->_parts = explode('/', $path);
        $l10n = $this->_parts[0];        

        $this->_l10n = match($l10n){
            'en','pt','de','es', 'it' => $this->_l10n($l10n),
            default => $this->_l10n('fr')
        };  
        $this->_currencyCode = 'EUR';
        $this->_countryCode = 'FR';       
        // On verifie le pays
        $cookieCurrency = $this->_request->getCookieParams()['currency'] ?? false;
        if($cookieCurrency){
            //$cookie = $this->getCookie($cookieCurrency); 
            $this->_currencyCode = $cookieCurrency;
            //$this->_countryCode = $cookie->country;           
        }      
    } 

    private function _l10n(string $l10n): stdClass {
        $this->_table = new Table($this->_setDb()); 
        $sql = "SELECT l10ns.id, l10ns.i18n, CASE WHEN l10ns.id = 1 THEN null ELSE l10ns.url END AS 'code', l10ns._locale FROM l10ns WHERE l10ns.url = :url; ";
        $q = $this->_table->query($sql, ['url' => $l10n], true);
        if($l10n !== 'fr') unset($this->_parts[0]);
        $this->_routePath = '/' . implode('/', $this->_parts);
        return $q ? $q : (object)['id' => 1, 'i18n' => 3, 'code' => null, '_locale' => 'fr'];
    }   

    /*public function cast($sourceObject):RouteInterface
    {    
        $route = ( new ReflectionClass( new MyRoute ) )->newInstanceWithoutConstructor();    
        $sourceReflection = new ReflectionObject($sourceObject);

        $destinationReflection = new ReflectionClass($route);


        $sourceProperties = $sourceReflection->getProperties();

        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($route,$value);
            } else {
                $route->$name = $value;
            }
        }
        $route->setL10n($this->_l10n);
        $route->setCurrencyCode($this->_currencyCode);
        $route->setCountryCode($this->_countryCode);
        $route->setRequest($this->_request);
        return $route;
    }*/

    private function _getRoutes() {
        $filename = CONFIG.DS.'routes.json';        
        $json = file_get_contents($filename);
        return json_decode($json);
    }   

    public function run(){        
        $routes = $this->_getRoutes();
        $myRoute = new MyRoute();
        $froutes = [];
        $queries = $this->getRequest()->getQueryParams();

        unset($queries['url']);
        unset($queries['debug']);
        
        // On ne travaille que sur les routes qui on la methode de la requête comme POST GET ...
        $filtered = array_filter($routes, fn($r) => $r->method === $this->getRequest()->getMethod());
        
        foreach($filtered as $k => $route) {
            $clone = clone $myRoute;
            // On a des query params (GET)
            if(count($queries) > 0):
                $http_queries = http_build_query($queries);
                $url =  $this->_routePath . '/' . $http_queries;
            else:
                $url = $this->_routePath;
            endif;            
            // On parcours les routes, ici on devrait vérifier qu'on a tous les paramètres dans l'url 
            // exemple : erp.kutvek.com/users/223/orders?year=2026
            // ici on doit bien avoir les parametres year et id soit dans l'url soit dans les queryparameters
            $clone->path = $route->path;
            $clone->url = $this->_routePath;
            $clone->url_with_queries = $url;
            $clone->params = $route->params??[];
            $clone->method = $route->method;
            $clone->callable = $route->callable;
            $clone->middlewares = $route->middlewares;
            $clone->setL10n($this->_l10n);
            $clone->setCurrencyCode($this->_currencyCode);
            $clone->setCountryCode($this->_countryCode);
            $clone->setRequest($this->_request);
            $clone->with();               
            $clone->middlewares();            
            
            $clone->is_match = (int)$clone->match_2();
            $froutes[$k] = $clone;
            if($clone->match_2()) return $clone->call();
        }
            echo '<pre>';
           
            print_r($froutes);
           
            echo '<pre>';
            die();
        return false;
    }    

    

    public function __debugInfo() {
       return json_decode(json_encode($this), true);
    }

   
}