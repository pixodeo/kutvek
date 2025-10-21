<?php
declare(strict_types=1);
namespace App\DealsCorner;

use App\AppAction;
use Domain\Table\GoodDeal;
use Library\HTML\{TraitSanitize};
use App\Component\Product\Widget\CrossSelling;


final class Read extends AppAction {
	use  TraitSanitize;

	protected GoodDeal $_table;
	public false|object $product;

	public function __invoke(int $id){		

		$queries = $this->getRequest()->getQueryParams();
		
		$slug = isset($queries['slug']) ? $queries['slug'] : '';
		
		$this->_table = new GoodDeal($this->_setDb());	
		$this->_table->setRoute($this->_route);
		
		$this->_path = dirname(__FILE__);
		$this->product = $this->_table->read($id);
		
		$this->meta_description = '';
		$this->meta_title =  'Kutvek'; 
		
		$this->_view = 'read';
		$this->_layout = 'read';
		
		$reinsurance_items = $this->_table->reinsuranceOnCart();
		$x_sell = new CrossSelling;
        $x_sell->setI18n($this->getI18n()); 
		$this->_content = $this->partial(compact('queries', 'reinsurance_items', 'x_sell'));		
		$this->getResponse()->getBody()->write($this->_print());
		return $this->getResponse();
	}

	
}	