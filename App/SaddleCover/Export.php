<?php
declare(strict_types=1);
namespace App\SaddleCover;
use App\AppAction;
use Core\Library\TraitCsv;
use Domain\Table\SaddleCover;
use Library\HTML\TraitSanitize;
use Library\TraitPagination;

final class Export extends AppAction {
	use TraitPagination, TraitSanitize, TraitCsv;

	protected SaddleCover $_table;
	public false|object $editorial;
	private $_filters = [];

	public function __invoke(){
		$this->_table = new SaddleCover($this->_setDb());
		$this->_table->setRoute($this->_route);		
		$this->_table->setRequest($this->getRequest());		
		$slug = isset($queries['slug']) ? $queries['slug'] : '';
		$this->editorial = $this->_table->editorial($slug);
		$cards = $this->listOfProducts();

		return $this->_makeCsv($cards, 'l10ns-selles-'.$this->getI18n() . '-' . date('Y-m-d'));
		//$this->setResponse($this->getResponse()->withHeader('Content-Type', 'application/json;charset=utf-8')); 	
		//$this->getResponse()->getBody()->write(json_encode($cards, JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
		//return $this->getResponse();
	}

	public function listOfProducts(): array {	
		$category = $this->editorial->category_id;
		$this->_table->setStore((int)$this->editorial->d_store ?? 1);	
		$cards = $this->_table->listOfProductsToExport((int)$category);		
		array_walk($cards, fn ($c) => $c->content());
		return $cards;
	}

	public function getFilters():array{return $this->_filters;}
}	