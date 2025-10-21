<?php
declare(strict_types=1);
namespace App\Product;

use Core\Routing\RouteInterface;

/**
 * @pattern Decorator
 */
interface Strategy {	
	public function __invoke(int $id);
	public function setRoute(RouteInterface $route): void;	
	public function vehicles():array;	
}