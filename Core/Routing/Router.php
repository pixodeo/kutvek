<?php

namespace Core\Routing;
use Psr\Http\Message\ServerRequestInterface;
use Core\Routing\RouterInterface;
use stdClass;
use Core\Library\{TraitCookie,TraitModel};
use Core\Domain\Table;


class Router implements RouterInterface {
    use TraitCookie, TraitModel;
    private $routes = [];

    public $routing = [];
    public Route $route;
    private ServerRequestInterface $_request;
    private $status = [
    303=>"HTTP/1.1 303 See Other",
    301=>"HTTP/1.1 301 Moved Permanently",
    302=>"HTTP/1.1 302 Moved Temporarily",
    404=>'HTTP/1.0 404 Not Found',
    410=>'HTTP/1.0 410 Gone'];   
    private $_l10n;
    private $_currencyCode = 'EUR';
    private int $_currencyId = 1;
    private $_countryCode = 'FR';
    private Table $_table;
    private $_parts = []; 
    private string $_routePath = '';

    public function __construct(ServerRequestInterface $request)
    {        
        $this->_request = $request;
        $this->_init();        
    }

    public function getRequest(): ServerRequestInterface {
        return $this->_request;
    } 
    
    public function setRequest() {
        
    }

    public function getParts():array{
        return $this->_parts;
    }

    public function getl10n():stdClass{
        return $this->_l10n;
    }


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
        // On verifie le pays
        $cookieCurrency = $this->_request->getCookieParams()['country_currency'] ?? false;
        if($cookieCurrency){
            $cookie = $this->getCookie($cookieCurrency); 
            $this->_currencyCode = $cookie->currency;
            $this->_countryCode = $cookie->country;  
            $this->_currencyId = match($cookie->currency){
                'EUR' => 1,
                'CAD' => 2,
                'USD' => 3,  
                'GBP' => 4
            };         
        }      
    } 

    private function _l10n(string $l10n): stdClass {
        $this->_table = new Table($this->_setDb()); 
        $sql = "SELECT l10ns.id, l10ns.i18n, CASE WHEN l10ns.id = 1 THEN null ELSE l10ns.url END AS 'code', l10ns._locale FROM l10ns WHERE l10ns.url = :url; ";
        $q = $this->_table->query($sql, ['url' => $l10n], true);
        //if($l10n !== 'fr') unset($this->_parts[0]);
        $this->_routePath = '/' . implode('/', $this->_parts);
        return $q ? $q : (object)['id' => 1, 'i18n' => 3, 'code' => null, '_locale' => 'fr'];
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getRoute(): RouteInterface
    {
        return $this->route;
    }

     public function getRouting(): array
    {
        return $this->routing;
    }

    public function get($path, $callable, $name = null){
        // Ex : $router->get('truc', "Posts.truc");
        return $this->_add($path, $callable, $name, 'GET');
    }

    public function post($path, $callable, $name = null){
        return $this->_add($path, $callable, $name, 'POST');
    }

    public function put($path, $callable, $name = null){
        return $this->_add($path, $callable, $name, 'PUT');
    }


    public function update($path, $callable, $name = null){
        return $this->_add($path, $callable, $name, 'PUT');
    }

    public function patch($path, $callable, $name = null){
        return $this->_add($path, $callable, $name, 'PATCH');
    }
    public function delete($path, $callable, $name = null){
        return $this->_add($path, $callable, $name, 'DELETE');
    }

    private function _add($path, $callable, $name, $method){
        $route = new Route($path, $callable, $this, $this->_request); 
        $route->setCurrencyCode($this->_currencyCode);
        $route->setCountryCode($this->_countryCode);
        $route->setCurrencyId($this->_currencyId);
        $route->setL10n($this->_l10n);       
        if(is_string($callable) && $name === null){
            $name = $callable;
        }
        $this->routes[$method][$name] = $route;
        $this->routing[$method][] = $route;
        return $route;
    }

    public function run() 
    {
        if(isset($this->routes[$this->_request->getMethod()]))
        {            
            foreach($this->routing[$this->_request->getMethod()] as $route)
            {                
                if($route->match($this->_request->getQueryParams()['url'] ?? '/')) {
                    $this->route = $route;

                    return $route->call();  
                } 
            }
        }
        // Aucune route trouvées mais un . : 
        //on appelle un controller sans routing                 
        if(strpos($this->_request->getQueryParams()['url'] ?? '/', '.') !== FALSE) {           
            $route = new Route(null, $this->_request->getQueryParams()['url'] ?? '/', $this, $this->_request); 
            $route->setCurrencyCode($this->_currencyCode);
            $route->setCountryCode($this->_countryCode);
            $route->setCurrencyId($this->_currencyId);
            $route->setL10n($this->_l10n);
            $this->route = $route;           
            return $route->call();
        }
        header("Status: 405 Not Allowed", false, 405);   
       
        exit();
    }
   
    /**
     * Retourne url formatée
     * @param  [type] $name   ex: admin.users.index
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    
    public function url(string $name, $params = [], string $method = 'GET'): string {
        $method = $method == 'UPDATE' ? 'PUT' : $method;
        if(array_key_exists($name, $this->routes[$method]))
            return $this->routes[$method][$name]->getUrl($params);       

        return $this->_buildQuery($name, $params);        
    }   


    /**
     * Ajouter le hash 
     * 
     */
    public function uri(string $name, $params = [], string $method = 'GET'): string 
    {
        $method = $method == 'UPDATE' ? 'PUT' : $method;
        if(array_key_exists('queries', $params)) $queries = $params['queries'];
        else $queries = [];
        $filter = array_filter(
            $this->routing[$method], 
            function($route)use($name, $params, $queries){ 
                if($route->callable === $name):
                    $_matchParams = $this->_matchParams($route, $queries);
                endif;
                //$_matchParams = $this->_matchParams($route, $queries);
                if(array_key_exists('prefix', $params))
                {                   
                    return ($route->_prefix === $params['prefix'] && $route->callable === $name && $_matchParams);
                }
               return  ($route->callable === $name && $_matchParams);
            }
        );
        if(!empty($filter))
        {
            $route = array_pop($filter);            
            return $route->getUrl($params);
        }
        return $this->_buildQuery($name, $params);        
    }


    /**
     * Ajouter le hash 
     * 
     */
    public function uri_2(string $name, $params = [], string $method = 'GET'): string 
    {
        $method = $method == 'UPDATE' ? 'PUT' : $method;
        if(array_key_exists('queries', $params)) $queries = $params['queries'];
        else $queries = [];

        $filter = array_filter(
            $this->routing[$method], 
            function($route)use($name, $params, $queries){ 
                if($route->callable === $name):
                    $_matchParams = $this->_matchParams($route, $queries);
                endif;
                //$_matchParams = $this->_matchParams($route, $queries);
                if(array_key_exists('prefix', $params))
                {                   
                    return ($route->_prefix === $params['prefix'] && $route->callable === $name && $_matchParams);
                }
               return  ($route->callable === $name && $_matchParams);
            }
        );
        
        if(!empty($filter))
        {
            $route = array_pop($filter);            
            return $route->url($params);
        }
        return $this->_buildQuery($name, $params);       
    }

    /* Gérer les erreurs, les 404 410 301 .... via RouterException */

    public function errors($status = null) {
         if($status) {
            header($this->status[$status]);
        }
        exit($this->status[$status]);
    }

    private function _buildQuery($name, $params) {
        if(array_key_exists('queries', $params)) {
            $query = http_build_query($params['queries']);
            $name =  $name . '?' . $query;
        }        
        return $this->_buildHash($name, $params);
    }

    private function _buildHash($name, $params) {
        if(array_key_exists('hash', $params)) {      
           
            $name =  $name . '#' . $params['hash'];
        }
        
        return $name;
    }



    public function __debugInfo() {
       return json_decode(json_encode($this), true);
    }


    /**
     * s'assure que toutes les clés de la route se retrouvent dans les paramètres définies dans la construction de l'url via la methode $this->uri;
     *
     * @return     bool  ( description_of_the_return_value )
     */
    private function _matchParams(Route $route, array $queries = []): bool {

        foreach($route->params as $k => $param) {
            if(!array_key_exists($k, $queries)) {
                return false;
            }
        }
        return true;
    }
}