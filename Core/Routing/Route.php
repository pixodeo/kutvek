<?php
namespace Core\Routing;

use function \Core\Http\Response\send;
use Core\Http\Message\Response;
use Core\Action;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Core\Library\TraitL10n;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

class Route implements RouteInterface, RequestHandlerInterface
{
    
    use TraitL10n;

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

    public function __get(string $match): mixed {        
        if (array_key_exists($match, $this->matches)) return $this->matches[$match];
        return null;
    }


    /**
     * Supprime le controller le la liste des middlewares
     */
    public function unpipe(){
        $this->_middlewares = [];
        //array_pop($this->_middlewares);     
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
     * Methode en remplacement de call() lorsqu'on utilise ADR : Action Domain Responder
     * L'action (ex controller) renvoi une réponse du Responder, pas de send() ici
     *
     * @throws     RouterException  (description)
     */
    public function call(){       
        
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
                $this->_request = $this->_request->withQueryParams($queries);
                $this->_action = new $action($this->_router);
                //$this->_action->setRoute($this->_router);
                unset($this->_router);
                $this->_pipe($this->_action);

                $this->response = new Response();

                $this->response =  $this->handle($this->_request);
                $this->response = $this->response->withHeader('X-Robots-Tag', 'noindex');
                return send($this->response); 
            }    
            catch (\ReflectionException $e){ die($e->getMessage()); }
            catch (\Exception $e) { die($e->getMessage()); }  
        else:
            header('HTTP/2.0 405 Method Not Allowed');
            throw new RouterException('No Controller' . $action);
        endif;
    }

    

    public function handle(ServerRequestInterface $request): ResponseInterface
    {        
        $middleware = $this->_getMiddleware();
        $this->_index = $this->_index + 1;  

        // On est arrivé à la fin de la pile
        if(is_null($middleware)) return $this->response;       

        if ($middleware instanceof MiddlewareInterface) {
            $this->response =  $middleware->process($request, $this);             
            return $this->response;
        }
        return $this->response;   
    }

    private function _getMiddleware(): ?MiddlewareInterface 
    {
        if(isset($this->_middlewares[$this->_index])) {
            return $this->_middlewares[$this->_index];
        }
        return null;
    }
    
    public function getMiddlewares(): array
    {
        return $this->_middlewares;
    }

    public function getStatus(){
      return $this->response->getStatusCode();
    }

    public function getMatches(): array 
    {
        return $this->matches;
    }

    public function lang()
    {
        $explode = explode('/', $this->uri);
        $langs = array('en', 'en-gb', 'en-uk', 'fr-ca', 'fr', 'it', 'pt', 'de','es', 'en-ca', 'en-us', 'en-fr', 'fr-fr');
        if(in_array($explode[0], $langs)){
            $this->lang = $explode[0];
        }    
        $l10n = new stdClass;

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
        if(is_string($middleware))  $class = MIDDLEWARE_DIR .$middleware;
        $middleware = new $class; 
        $middleware->setRouter($this->_router);             
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

    /**
     * Renvoi une url formatée exemple dashboard.html
     * alors qu'on a demandé admin.user.index
     * utilisé pour la construction des liens
     * Améliorer : les paramètres existants sont
     * 
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function url(array $params = []): string
    {
        $queries =  isset($params['queries']) ? $params['queries'] : [];        
        $url = ltrim($this->path, '/');
        $q = $queries;
        if(!empty($queries)){
            foreach($queries as $k => $v){
                $pos = strpos($url, ":$k");
                $url = str_replace(":$k", $v, $url);
                if($pos !== false)
                    unset($q[$k]);
            }
            $query = http_build_query($q);
            $url = (!empty($q) ? $url . '?' . $query : $url);
            if(array_key_exists('hash', $params)) {           
                $url .=   '#' . $params['hash'];
            }           
        }
        if($this->l10n->code) $url = $this->l10n->code . '/' . $url;
        $url = '/' . $url;
        if(isset($params['fqdn'])):
            $url = DOMAIN . $url;
        endif; 
        // sauf si on n'a qu'un slash dans l'url   
        if(strlen($url > 1)) rtrim($url, '/'); 
        return $url;
    }

    public function getRequest(): ServerRequestInterface {return $this->_request;}

    public function getRouter():RouterInterface{return $this->_router;}

    public function __debugInfo() {
       return json_decode(json_encode($this), true);
    }
}