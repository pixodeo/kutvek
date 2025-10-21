<?php
declare(strict_types=1);
namespace App\Product;
use App\Product\Types\AddToCart\{PlateStickers,EngineGuard,Basic, Graphics};
use Core\Action;

final class AddToCart extends Action  {

	public function __invoke() { 
		$post = $this->getRequest()->getParsedBody();
		$this->_middleware = match($post['behavior_type']){
            'PlateStickers' => new PlateStickers($this->_route),            
            'EngineGuard' => new EngineGuard($this->_route),
            'Graphics' => new Graphics($this->_route),
            default => new Basic($this->_route),
        };       
        $this->_middleware->setRouter($this->_router);        
        return $this->handle($this->getRequest());
	}
}	