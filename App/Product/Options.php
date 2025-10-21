<?php
declare(strict_types=1);
namespace App\Product;

use Core\Action;
use Domain\Table\Product;
use App\Product\Types\{AccessorySticker, PlateStickers,Basic, EngineGuard, Options\Graphics};

final class Options extends Action {   
    private Product $_table;	
	
	public function __invoke(){  
        $queries = $this->getRequest()->getQueryParams();
        $this->_table = new Product($this->_setDb());
        $this->_table->setRoute($this->_route);
        $id = (int)$queries['id'];
        $product = $this->_table->read($id);
        $type = match($product->behavior_type){
            'PlateStickers' => new PlateStickers($this->_route),
            'Graphics' => new Graphics($this->_route),
            'AccessoryStickers' => new AccessorySticker($this->_route),
            'EngineGuard'       => new EngineGuard($this->_route),
            default => new Basic($this->_route)
        };
        $type->setDomain($this->_table, 'Product');        
        $type->setRouter($this->_router);
        $type->setProduct($product);
        $this->_middleware = $type;
        return $this->handle($this->getRequest());                    
	}
}