<?php
declare(strict_types=1);
namespace Core\Routing;

trait TraitUri {
    protected array $_routes = [];

    public function url(string $callable, array $params = [], string $method = 'GET')
    {       
        $routes = $this->_getRoutes();
        $search = array_filter($routes, fn($r) => $r->method === $method && $r->callable === $callable);
        if(!empty($search)){
            $object = array_pop($search);
            $route = new MyRoute();
            $route->path = $object->path;
         $route->params = $object->params??[];
         $route->method = $object->method;
         $route->callable = $object->callable;
         $route->middlewares = $object->middlewares;
         $route->setL10n($this->getL10n());
         $route->setCurrencyCode($this->getCurrencyCode());
         $route->setCountryCode($this->getCountryCode());
         $route->setRequest($this->_request);
         $route->with()               
            ->middlewares();
         $queries = array_key_exists('queries', $params) ? $params['queries'] : [];
         $_matchParams = $this->_matchParams($route, $queries);
         // Manque le '/' à la base en gros si l'url ne commence ni par http ou '/' on rajoute

        if($_matchParams): 
            $url = $route->getUrl($params);
            if(strpos($url, '/') !== 0 &&  !array_key_exists('fqdn', $params)) return '/'.$url;
            return $url;
        endif;
        
        return '#';         
        }       
    }

    private function _getRoutes(): array
    {
        if(empty($this->_routes)):         
            $filename = CONFIG.DS.'routes.json';        
            $json = file_get_contents($filename);
            $this->_routes = json_decode($json);
        endif;
        return $this->_routes;
    } 

    /**
     * s'assure que toutes les clés de la route se retrouvent dans les paramètres définies dans la construction de l'url via la methode $this->uri;
     *
     * @return     bool  ( description_of_the_return_value )
     */
    private function _matchParams(RouteInterface $route, array $queries = []): bool {
        $params = (array)$route->params;
        $keys = array_keys($params);
          foreach($keys as $k) {
             if(!array_key_exists($k, $queries)) {
                return false;
             }
          }
        return true;
    }
}