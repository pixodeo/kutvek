<?php
declare(strict_types=1);
namespace App\Product;

use App\AppAction;
use Core\Request\UrlQueryResult;
use stdClass;
use Domain\Table\Product;
use App\Product\Types\{AccessorySticker, PlateStickers,Basics, EngineGuard, Graphics};

final class Read extends AppAction implements UrlQueryResult{	
	public $app;
    public stdClass $queryResult;
    private Product $_table;	
	
	public function __invoke(){  
        $queries = $this->getRequest()->getQueryParams();
        $this->_table = new Product($this->_setDb());
        $this->_table->setRoute($this->_route);
        if(isset($queries['id'])): 
            $id = (int)$queries['id'];  
            $product = $this->_table->read($id);
        else: 
            $slug = $queries['slug'];
            $product = $this->_table->readBySlug($slug);
        endif; 

        if(isset($_GET['test_m'])):
            die(print_r($product));
        endif;
        $strategy = match($product->behavior_type){
            'PlateStickers' => new PlateStickers($this->_route),
            'Graphics' => new Graphics($this->_route),
            'AccessoryStickers' => new AccessorySticker($this->_route),
            'EngineGuard'       => new EngineGuard($this->_route),
            default => new Basics($this->_route)
        };
        $strategy->setDomain($this->_table, 'Product');
        $strategy->setProduct($product);
        $strategy->setRouter($this->_router);
        $this->_middleware = $strategy;
        return $this->handle($this->getRequest());       
	}	

    public function setQueryResult(stdClass $query): void {$this->queryResult = $query; }
}