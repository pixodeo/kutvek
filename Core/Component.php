<?php
declare(strict_types=1);
namespace Core;
use \App;
use Core\Domain\Table;
use Core\Library\{TraitCookie,TraitResponse,TraitL10n};
use Core\Routing\{RouteInterface, RouterInterface};
use Psr\Http\Message\ServerRequestInterface;

abstract class Component 
{ 
    use TraitCookie, TraitResponse, TraitL10n; 
    protected array $_tables = [];


    public function __construct(protected RouteInterface $_route){}
    
    protected function _setDb($dbConf = null) {
            $app = App::getInstance();
            return $app->setDb($dbConf);           
    }

    public function setRouter(RouterInterface $router){ $this->_router = $router;}

    public function setDomain(Table $table, $key){
        $this->_tables[$key] = $table;
    }

    public function getDomain(string $key): Table {
        return $this->_tables[$key];
    }
    public function setRequest(ServerRequestInterface $request): ServerRequestInterface{    
        $this->_request = $request;
        return $this->_request;
    }
    public function getRequest(): ServerRequestInterface
    {        
        return $this->_request;
    }

    
}