<?php
declare(strict_types=1);
namespace App\DealsCorner;
use App\AppAction;
use Core\Library\TraitCsv;
use Domain\Table\GoodDeal;
use Library\TraitPagination;

final class Export extends AppAction {
	use TraitPagination, TraitCsv;

	protected GoodDeal $_table;
	public false|object $editorial;
	private $_filters = [];

	public function __invoke(){
		$this->_table = new GoodDeal($this->_setDb());	
		$this->_table->setRoute($this->_route);		
		$this->_table->setRequest($this->getRequest());
		$queries = $this->getRequest()->getQueryParams();		 
		$slug = isset($queries['slug']) ? $queries['slug'] : '';			
		$this->editorial = $this->_table->editorial($slug);
		$this->_table->setStore((int)$this->editorial->department_store);
		if(array_key_exists('page', $queries)) $this->setCurrentPage((int)$queries['page']);
		$cards = $this->listOfProducts($this->editorial->category_id);
		return $this->_makeCsv($cards, 'l10ns-deals-corner-'.$this->getI18n() . '-' . date('Y-m-d'));
		$this->setResponse($this->getResponse()->withHeader('Content-Type', 'application/json;charset=utf-8')); 	
		$this->getResponse()->getBody()->write(json_encode($cards, JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
		return $this->getResponse();
	}

	public function listOfProducts(int $id): array {		
		
		$cards = $this->_table->listOfProductsToExport((int)$id);		
		array_walk($cards, fn ($c) => $c->content());
		return $cards;
	}

	public function getFilters():array{return $this->_filters;}
}	