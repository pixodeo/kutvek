<?php
declare(strict_types=1);
namespace Core\Routing;

trait TraitRoute {
    public function setRoute(RouteInterface $route): void {$this->_route = $route;}
}