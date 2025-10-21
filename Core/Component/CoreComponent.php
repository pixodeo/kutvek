<?php
declare(strict_types=1);
namespace Core\Component;
use \App;
use Core\Routing\{RouterInterface, RouteInterface};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Core\Library\{TraitCore, TraitCookie};


abstract class CoreComponent
{ 
	use TraitCore, TraitCookie; 
    protected RouteInterface $_route;

    public function __construct(){

    }
    protected function _setDb($dbConf = null) {
            $app = App::getInstance();
            return $app->setDb($dbConf);           
    }

    public function setRoute(RouteInterface $route): void {$this->_route = $route;}   
}