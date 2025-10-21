<?php
declare(strict_types=1);
namespace Middleware;

use Core\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionMethod;

/**
 * 	
 */
class PoweredBy extends  Middleware
{	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {            
        
       
        $this->lang = $request->getAttribute('lang', 'fr'); 
        $this->setRequest($request);
        //$this->_request = $request;
        $this->setI18n($this->lang);
        //$this->_response = $handler->handle($request);
        $this->setResponse($handler->handle($request));
        $reflectionMethod = new ReflectionMethod($this, '__invoke');      
        return $reflectionMethod->invoke($this);            
         

        //return $this->getResponse();      
    }

    public function __invoke(){
    	$this->_response = $this->_response->withHeader('Server', 'Peyredragon');
        $this->_response = $this->_response->withHeader('X-Middleware',array_merge([ __CLASS__],$this->_response->getHeader('X-Middleware'))); 
    	$this->_response = $this->_response->withHeader('X-Powered-By', 'Daenerys Targaryen');	
    	return $this->_response;
    }
}