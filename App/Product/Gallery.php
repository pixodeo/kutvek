<?php
declare(strict_types=1);
namespace App\Product;

use Core\Action;
use Domain\Table\Product;

final class Gallery extends Action {	
	public $app;    
    private Product $_table;	
	
	public function __invoke(){  
        $queries = $this->getRequest()->getQueryParams();
        $this->_table = new Product($this->_setDb());
        $this->_table->setRoute($this->_route);
        $id = (int)$queries['id'];
        $files = $this->_table->files($id);
        $this->_view = 'Products.gallery';
        $this->_response->getBody()->write($this->partial(compact('files')));
        return $this->_response;            
	}
}