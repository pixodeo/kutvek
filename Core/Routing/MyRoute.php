<?php

namespace Core\Routing;

use Core\Routing\RouterInterface;
use function \Core\Http\Response\send;
use Core\Http\Message\Response;

class Route implements IRouteInterface
{

    public $path;    
    public $callable;
    private $_router;
    public $matches = [];
    public $params = [];
    private $_middlewares = [];   
    public $_prefix = false;

    public function __construct(?string $path, $callable, RouterInterface $router){
        if($path !== null)
            $this->path = trim($path, '/');

        if(strpos($callable, '@') === 0 )                   
            $this->callable = trim($callable, '@'); 
        else  
            $this->callable = $callable;

        $this->_router = $router;
    }
    
    public function getMiddlewares(): array
    {
        return $this->_middlewares;
    }

    public function getMatches(): array 
    {
        return $this->matches;
    }

    /**
     * [with description]
     * @param  [type] $param [description]
     * @param  [type] $regex [description]
     * @return [type]        [description]
     */
    public function with($param, $regex)
    {
        $this->params[$param] = str_replace('(', '(?:', $regex);
        return $this;
    }

    public function prefix(string $prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }



    /**
     * Envoi les middleware dans le dispatcher
     * 
     * @param  [type] $middleware [description]
     * @return [type]             [description]
     */
    public function middleware($middleware) 
    {
        if(is_string($middleware)) 
            $middleware = new $middleware;

        $this->_middlewares[] = $middleware;    
        $this->_router->getDispatcher()->pipe($middleware);

        return $this;
    }

    public function match($url)
    {
        $url = trim($url, '/');

        if($this->_prefix && strpos($this->path, $this->_prefix) === false) return false;


        $path = preg_replace_callback('`:([\w]+)`', [$this, 'paramMatch'], $this->path);        
        $regex = "`^$path$`i";

        if(!preg_match($regex, $url, $matches)){
            return false;
        } 
              
        array_shift($matches);       
        $this->matches = array_combine(array_keys($this->params), $matches);       
        /*foreach ($r as $k => $v) {
            $this->matches[] = $v;           
        } */       
        return true;
    }

    private function paramMatch($match)
    {
        if(isset($this->params[$match[1]])){
            return '(' . $this->params[$match[1]] . ')/*';
        }
        
        return '([^/]+)/*';
    }

    /**
     * On vérifie que les paramatres requis pour la methode appelée sur un controller existent 
     * dans la REQUEST
     *
     * @param array $parameters
     * @return void
     */
    private function _setRequestParam(array $parameters)
    {
         /* 
                On a plusieurs paramètres
                chaque parametre a une propriete name
                [0] => ReflectionParameter Object
                (
                    [name] => universe
                )
                [1] => ReflectionParameter Object
                (
                    [name] => brand
                )
        */
       // Pour PUT 
        // Pour GET POST COOKIE
        
        $requestParams = $this->_buildParams();
        
        if(count($parameters) > 0)
        {            
            foreach($parameters as $k => $parameter)
            {
                // !! Attention de bien vérifier que ces paramètres sont envoyés via REQUEST et non pas dans l'url sinon ça les écrase !!
                if(!array_key_exists($parameter->getName(), $this->matches)) {

                    // si null autorisé et pas de correspondance dans la request, ou  null 
                    if(($parameter->allowsNull() && !array_key_exists($parameter->getName(), $requestParams)) || ($parameter->allowsNull() && !$requestParams[$parameter->getName()]))
                        $this->matches[$parameter->getName()] = null;
                    // si la clé existe
                    if(array_key_exists($parameter->getName(), $requestParams) && $requestParams[$parameter->getName()])
                        $this->matches[$parameter->getName()] = $requestParams[$parameter->getName()];
                } 
            }
        } 
    }

    /**
     * Renvoie la data reçue selon les différentes méthodes
     * $_SERVER['REQUEST_METHOD']
     * pour DELETE on a rien en REQUEST ou php://input,
     * le parametre est dans l'url
     * @return array parameters
     */
    
    private function _buildParams(): array {
        
        if($_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'DELETE'):
            // En put on récupère un json            
            $json = file_get_contents('php://input');           
            //return array_merge($_REQUEST, ['payload' => $json]);
            $data = json_decode($json, TRUE);            
            return $data !== null ? array_merge($_REQUEST, json_decode($json, TRUE)) : $_REQUEST;
        elseif($_SERVER['REQUEST_METHOD'] == 'POST'):
            return $_REQUEST;
        elseif($_SERVER['REQUEST_METHOD'] == 'GET'):
            return $_REQUEST;
      
        elseif($_SERVER['REQUEST_METHOD'] == 'COOKIE'):
            return $_REQUEST;
        else:
            return $_REQUEST;
        endif;
    }

    /**
     * Lance le controller.
     * Utilisation de la reflexion
     * Penser à intéger le typage, la vérification du nombre de paramètre, leur place ....
     * Pour les requêtes en PUT il faut envoyer le flux (un json) en paramètres, 
     * pour éviter un traitement côté controller
     *
     * Avant l'appel à la métode passée en paramètres, verification des middlewares
     * Pb : les middlewares sont exécutés aprèS
     * @return object 
     */
    
    public function call() {

        if(is_string($this->callable)) {   
            $params = explode('.', $this->callable);
            if($params[0] === 'admin') {
                $controller = CONTROLLER_DIR . ucfirst($params[0]) .'\\'. ucfirst($params[1]) . "Controller";
                $action = $params[2] ?? 'index';
            } 
            elseif($params[0] === 'dev')
            {
                $controller = CONTROLLER_DIR .  ucfirst($params[0]) .'\\'. ucfirst($params[1]) . "Controller";
                $action = $params[2] ?? 'index';
            }
            elseif($this->_prefix){
                $controller = CONTROLLER_DIR . ucfirst($this->_prefix) .'\\'. ucfirst($params[0]) . "Controller";
                $action = $params[1] ?? 'index';
            }
            else 
            {
                $controller = CONTROLLER_DIR . ucfirst($params[0]) . "Controller";
                $action = $params[1] ?? 'index';
            }
            if (class_exists($controller)) {
                $controller = new $controller();
            } else {
                header('HTTP/1.0 404 Not Found');
                throw new RouterException('No Controller');
            }
                
            // Envoi du router au controller
            $controller->setRouter($this->_router);        
            
            // vérifications des paramètres de la méthode appelée 
                                       
            $reflexion = new \ReflectionClass($controller);
            $method = $reflexion->getMethod($action); 
            // On récupère les paramètres de la methode 
            // Récupère les paramètres, sous la forme d'un tableau d'objets ReflectionParameter, 
            //dans l'ordre dans laquelle ils sont définis dans la source.

            $parameters = $method->getParameters(); 
                
            // On vérifie si ces paramètres ne sont pas envoyés via REQUEST          
            $this->_setRequestParam($parameters);  

            // il serait bien d'initialiser les parametres avec d'eventuelles valeurs
            // voir     https://www.php.net/manual/fr/reflectionmethod.invokeargs.php#100041                          
            try 
            {
                $request = $this->_router->getRequest();  
                // si on envoyait les matches dans la request ?
                $request = $request->withAttribute('matches', $this->matches);
                $request = $request->withAttribute('params', $this->params);
                $request = $request->withAttribute('method', $method);
                $queries = $request->getQueryParams();
                unset($queries['url']);
                $request = $request->withAttribute('queries', $queries );
                $process = $request->getAttribute('process');

                if(!is_array($process) || $process == null)
                {
                    $process = ['Route' => 1];
                }

                $request = $request->withAttribute('process', $process);
                
                
                $uri = $request->getUri()->getPath();
                if(count($this->_middlewares)>0)
                {
                    $this->_router->getDispatcher()->pipe($controller);                
                    $response = $this->_router->getDispatcher()->handle($request);           
                    send($response); 
                }
                
                else {
                    $method->invokeArgs($controller,$this->matches);
                }                                
            } 
            catch (\ReflectionException $e){ die($e->getMessage()); }
            catch (\Exception $e) { die($e->getMessage()); } 

        } // is_string callable
        else {
            return call_user_func_array($this->callable, $this->matches);
        }     
    }   

    /**
     * Renvoi une url formatée exemple dashboard.html
     * alors qu'on a demandé admin.user.index
     * utilisé pour la construction des liens
     * Améliorer : les paramètres existants sont
     * 
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function getUrl($queries): string
    {
        $path = $this->path;
        $q = $queries;
        if(!empty($queries)){
            foreach($queries as $k => $v){
                $pos = strpos($path, ":$k");
                $path = str_replace(":$k", $v, $path);
                if($pos !== false)
                    unset($q[$k]);
            }
            $query = http_build_query($q);
            return (!empty($q) ? $path . '?' . $query : $path);
        }
        return $path;
    }

    public function __debugInfo() {
       return json_decode(json_encode($this), true);
    }
}