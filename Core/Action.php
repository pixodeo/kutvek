<?php
declare(strict_types=1);
namespace Core;

use Core\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\{MiddlewareInterface,RequestHandlerInterface};
use ReflectionMethod;
use Core\Library\{TraitCore,TraitCookie,TraitResponse,TraitModel, TraitL10n};


abstract class Action  implements MiddlewareInterface, RequestHandlerInterface {
	use TraitL10n, TraitCookie, TraitModel, TraitResponse;	
    
    public $lang;
    protected ?MiddlewareInterface $_middleware = null;
    
    protected array $_tables = [];

    public function __construct(protected RouterInterface $_router)
    {       
        $this->_route = $this->_router->getRoute();
    }

	public function setRouter(RouterInterface $router): void 
    {        
        // Si Responder 
        if($this->_responder && $this->_responder instanceof Responder) $this->_responder->setRouter($router);
        else $this->_router = $router;
    }

    public function getRouter(): RouterInterface
    {
        if($this->_responder && $this->_responder instanceof Responder) return $this->_responder->getRouter();
        return $this->_router;
    }

    public function setRequest(ServerRequestInterface $request): ServerRequestInterface{
        if($this->_responder && $this->_responder instanceof Responder) { return $this->_responder->setRequest($request);}
        else $this->_request = $request;
        return $this->_request;
    }

    public function getRequest(): ServerRequestInterface
    {
        if($this->_responder && $this->_responder instanceof Responder) return $this->_responder->getRequest();
        return $this->_request;
    }

    public function setResponse(ResponseInterface $response){
        if($this->_responder && $this->_responder instanceof Responder) $this->_responder->setResponse($response);
        else $this->_response = $response;
    }

    public function getResponse(): ResponseInterface {
        if($this->_responder && $this->_responder instanceof Responder) return $this->_responder->getResponse();
        return $this->_response;  
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $matches = $request->getAttribute('matches'); 
        
        
        $this->_payload = $request->getAttribute('payload', null);        
        $this->lang = $request->getAttribute('lang', 'fr'); 
        $this->setRequest($request);        
        

        $countryCurrencyToken = $this->getRequest()->getCookieParams()['country_currency'] ?? false;        
        if ($countryCurrencyToken) {
           $cookie = $this->getCookie($countryCurrencyToken);
           //$this->setCountry($cookie->country);
           //$this->setCurrency($cookie->currency);                   
        }
        
        $this->setResponse($handler->handle($request));
        $reflection = new ReflectionMethod($this, '__invoke');
        
        $parameters = $reflection->getParameters();
        if(count($matches) > 0):
            if(count($matches) !== count($parameters)):
                $query_params = [];
                $parameters_keys = array_column($parameters, 'name');
                $matches_keys = array_keys($matches);
                $diff = array_diff($matches_keys, $parameters_keys);
                foreach($diff as $key):
                    $query_params[$key] = $matches[$key];
                    unset($matches[$key]);
                endforeach;                
                // On combine les queries existantes avec les nouvelles
                $query_params = array_merge($this->getRequest()->getQueryParams(),$query_params);
                // On met à jour l'objet Request
                $this->setRequest($this->getRequest()->withQueryParams($query_params));        
            endif;
            $reflection->invokeArgs($this, $matches);
        else:
            $reflection->invoke($this);            
        endif;  

        return $this->getResponse();      
    } 

    

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->_middleware instanceof MiddlewareInterface) {
            $this->_response =  $this->_middleware->process($request, $this->_route);
            $this->_middleware = null;            
        }
        return $this->_response;        
    }

    protected function _unauthorized(){
        $this->_response =  $this->_response->withStatus(303); 
        $this->_response = $this->_response->withHeader('location', $this->uri('identities.login'));
        return '';

        //$this->_response = $this->_response->withHeader('WWW-Authenticate', 'realm="Please signin", error="Authorization is required"');
            
        //$this->_response = $this->_response->withHeader('X-Authenticate-Location', $this->uri('admin.users.signin'));
    }
}