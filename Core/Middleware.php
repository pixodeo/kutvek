<?php
declare(strict_types=1);
namespace Core;

use Core\Routing\{RouterInterface, RouteInterface};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionMethod;


abstract class Middleware implements MiddlewareInterface, RequestHandlerInterface {

	use \Core\Library\TraitCore, \Core\Library\TraitCookie, \Core\Library\TraitModel;

	protected RouterInterface $_router;
    protected RouteInterface $_route;
	protected ServerRequestInterface $_request;
    protected ResponseInterface $_response;	
    protected $_payload;  
    public $lang;  
    protected ?MiddlewareInterface $_middleware = null;

    public function setRouter(RouterInterface $router): void { 
        $this->_router = $router; 
        
    }

    public function setRoute(RouteInterface $route):void {$this->_route = $route;}

    public function getRouter(): RouterInterface { return $this->_router; }

    public function setRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $this->_request = $request;
        return $this->_request;
    }

    public function getRequest(): ServerRequestInterface { return $this->_request;}

    public function setResponse(ResponseInterface $response) {$this->_response = $response;}

    public function getResponse(): ResponseInterface {return $this->_response;}  

    public function uri(string $name, ?array $params = [], string $method = 'GET'): string {
        $uri=  $this->_router->uri($name, $params, $method);      
        // Soit on cherche en-fr et on le dégage ou on le cherche et on met pas de prefix        
        $last = substr($uri, -1);
        if($last == '/') $uri = substr($uri, 0, -1);
        $ds = (\strpos($uri, '/') !== 0) ? DS : '';        
        if($this->_prefixUrl){
            return strlen($uri) > 0 ? DS . $this->_prefixUrl .$ds. $uri : DS . $this->_prefixUrl;
        }
        return strlen($uri) > 0 ? $ds . $uri : $ds;        
    }  

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->_middleware instanceof MiddlewareInterface) {
            $this->_response =  $this->_middleware->process($request, $this->_router->getRoute());
            $this->_middleware = null;            
        }
        return $this->_response;        
    }

    public function __set(string $name, mixed $value):void {
        $this->{$name} = $value;
    }
}