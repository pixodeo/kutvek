<?php

namespace Core\Routing;


use function \Core\Http\Response\send;
use Core\Http\Message\Response;
use Core\Action;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Route implements IRouteInterface, RequestHandlerInterface
{

    public $path;    
    public $callable;
    private RouterInterface $_router;
    protected ServerRequestInterface $_request;
    public $matches = [];
    public $params = [];
    private $_middlewares = [];   
    public $_prefix = false;
    private $_index = 0;
    private Response $response;
    public $uri; 
    public $lang = false;
    private $_adr;
    private $_accept = 'html';
    protected Action $_action;
   
    public function __construct(?string $path, $callable, RouterInterface $router, ServerRequestInterface $request)
    {
        if($path !== null)  $this->path = trim($path, '/');       
        $this->callable = $callable;
        $this->_router = $router;
        $this->_request = $request;
        
        $this->uri = $request->getQueryParams()['url'] ?? '/';
        $this->lang();       
    }

    private function _getMiddleware(): ?MiddlewareInterface 
    {
        if(isset($this->_middlewares[$this->_index])) {
            return $this->_middlewares[$this->_index];
        }
        return null;
    }

    /**
     * Un middleware est une méthode / action du controller / un composant
     * chargé de faire qqchose (des vérifs, des modifs...)
     * sur la requête avant l'appel à méthode demandée dans la route
     * Ex: un composant d'authentification / gestion des droits utilisateur 
     * 
     * @param  callable | MiddlewareInterface $middleware
     * @return [type]             [description]
     */
    private function _pipe($middleware) { $this->_middlewares[] = $middleware; }

    /**
     * Lance le controller.
     * Utilisation de la reflexion
     * Penser à intéger le typage, la vérification du nombre de paramètre, leur place ....
     * Pour les requêtes en PUT il faut envoyer le flux (un json) en paramètres, 
     * pour éviter un traitement côté controller
     *
     * Avant l'appel à la métode passée en paramètres, verification des middlewares
     * 
     * @return object 
     */    
    public function call() {
        if($this->_adr):                
            return $this->_callWithAdr();
        else:
            $params = explode('.', $this->callable);
            $dir = '';
            if($this->_prefix):
                // Enlever les eventuels tirets du prefix;
                $dir = ucfirst(str_replace('-', '',  $this->_prefix)) .'\\';                    
            endif; 
            $controller = CONTROLLER_DIR . $dir . ucfirst($params[0]) . "Controller";
            $action = $params[1] ?? 'index';  
        endif;           
        if (class_exists($controller)):
            $controller = new $controller();
        else:
            header('HTTP/1.0 404 Not Found');
            throw new RouterException('No Controller');
        endif;
                
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
        try 
        {
            $this->response = new Response();
            if($this->lang) $this->_request = $this->_request->withAttribute('lang', $this->lang);
            $this->_request = $this->_request->withAttribute('matches', $this->matches);
            $this->_request = $this->_request->withAttribute('params', $this->params);
            $this->_request = $this->_request->withAttribute('method', $method);
            $queries = $this->_request->getQueryParams();
            unset($queries['url']);
            $this->_request = $this->_request->withAttribute('queries', $queries ); 
            $this->_pipe($controller);  
            $this->response =   $this->handle($this->_request);        
            send($this->response);                                                               
        } 
        catch (\ReflectionException $e){ die($e->getMessage()); }
        catch (\Exception $e) { die($e->getMessage()); }  
    }
    /**
     * Methode en remplacement de call() lorsqu'on utilise ADR : Action Domain Responder
     * L'action (ex controller) renvoi une réponse du Responder, pas de send() ici
     *
     * @throws     RouterException  (description)
     */
    private function _callWithAdr(){       
        
        $params = explode('.', $this->callable);       
        // le dernier index c'est tjs la method \classe \ controller   
        // le reste c'est des dossiers sous dossiers
        foreach($params as $k => $param){
            $params[$k] = ucfirst($param);
        }
         
        $dir = implode('\\', $params);
        $action = APP_DIR . $dir;   
         if (class_exists($action)):            
            $reflexion = new \ReflectionClass($action);
            $_method = $reflexion->getMethod('__invoke');
            $parameters = $_method->getParameters();
            $this->_setRequestParam($parameters);
            try {
               if($this->lang) $this->_request = $this->_request->withAttribute('lang', $this->lang);
                $this->_request = $this->_request->withAttribute('matches', $this->matches);
                $this->_request = $this->_request->withAttribute('params', $this->params);            
                $queries = $this->_request->getQueryParams();
                unset($queries['url']);
                $this->_request = $this->_request->withAttribute('queries', $queries ); 

                $this->_action = new $action();
                $this->_action->setRouter($this->_router);
                $this->_pipe($this->_action);

                $this->response = new Response();        
                send($this->handle($this->_request));
            }    
            catch (\ReflectionException $e){ die($e->getMessage()); }
            catch (\Exception $e) { die($e->getMessage()); }  
        else:
            header('HTTP/2.0 405 Method Not Allowed');
            throw new RouterException('No Controller' . $action);
        endif;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $middleware = $this->_getMiddleware();
        $this->_index++;
        
        // On est arrivé à la fin de la pile
        if(is_null($middleware)) return $this->response;
        return $middleware->process($request, $this);        
    }
    
    public function getMiddlewares(): array
    {
        return $this->_middlewares;
    }

    public function getMatches(): array 
    {
        return $this->matches;
    }

    public function lang()
    {
        $explode = explode('/', $this->uri);
        $langs = array('en', 'en-gb', 'en-uk', 'fr-ca', 'fr', 'en-ca', 'en-us', 'en-fr', 'fr-fr');
        if(in_array($explode[0], $langs)){
            $this->lang = $explode[0];
        }       
        return $this;
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

    public function adr(){
        $this->_adr = true;
        return $this;
    }

    public function accept(string $contentType){
        $this->_accept = $contentType;
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
            $class = MIDDLEWARE_DIR .$middleware;
            $middleware = new $class; 
                     
            $this->_middlewares[] = $middleware;        
            return $this;
    }   

    public function match($url)
    {
              
        $url = trim($url, '/');
       
        if($this->_prefix && strpos($this->path, $this->_prefix) === false) return false;      
        
        if($this->lang) {
            // si on a un / dans l'url, sinon on est sur l'accueil
            if(\strpos($url, '/') === false)                
                $url = str_replace([$this->lang], '', $url);
            else
                $url = str_replace([$this->lang.DS], '', $url);
        }
        //die($url . '  __lang : ' . $this->lang . ' __path : ' . $this->path);
        
            
        $path = preg_replace_callback('`:([\w]+)`', [$this, 'paramMatch'], $this->path);       
        $regex = "`^$path$`i";
        if(!preg_match($regex, $url, $matches)){
            return false;
        }                 
        array_shift($matches);       
        $this->matches = array_combine(array_keys($this->params), $matches);                
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
        
        if($_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'DELETE' || $_SERVER['REQUEST_METHOD'] == 'PATCH'):
            // En put on récupère un json            
            $json = file_get_contents('php://input');           
            //return array_merge($_REQUEST, ['payload' => $json]);
            $data = json_decode($json, TRUE);            
            //return $data !== null ? array_merge($_REQUEST, json_decode($json, TRUE)) : $_REQUEST;
            return $data !== null ? array_merge($_REQUEST, $data) : $_REQUEST;
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
     * Renvoi une url formatée exemple dashboard.html
     * alors qu'on a demandé admin.user.index
     * utilisé pour la construction des liens
     * Améliorer : les paramètres existants sont
     * 
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function getUrl($params): string
    {
        $queries =  array_key_exists('queries', $params) ? $params['queries'] : [];        
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

            $url = (!empty($q) ? $path . '?' . $query : $path);
            if(array_key_exists('hash', $params)) {           
                $url .=   '#' . $params['hash'];
            }
            return $url;
        }
        return $path;
    }

    public function __debugInfo() {
       return json_decode(json_encode($this), true);
    }
}