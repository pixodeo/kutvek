<?php
declare(strict_types=1);
namespace Core\Routing;
use Psr\Http\Message\ServerRequestInterface;
trait TraitRequest {
    
    public function setRequest(ServerRequestInterface $request): ServerRequestInterface{    
        $this->_request = $request;
        return $this->_request;
    }
    public function getRequest(): ServerRequestInterface
    {        
        return $this->_request;
    }
}