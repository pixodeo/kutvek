<?php
declare(strict_types=1);
namespace App\Sportswear;
use App\AppAction;
use Core\Request\UrlQueryResult;
use Domain\Table\Sportswear;
use Library\HTML\TraitSanitize;
use Library\HTML\TraitPagination;
use stdClass;

final class Index extends AppAction implements UrlQueryResult {
	use TraitPagination, TraitSanitize;

	protected Sportswear $_table;
	public false|object $editorial;
	public stdClass $queryResult;

	public function setQueryResult(stdClass $query): void {$this->queryResult = $query; }

	public function __invoke(){
		$this->_table = new Sportswear($this->_setDb());	
		$this->_table->setRoute($this->_route);
		$this->_table->setConstructorArgs([$this->_route]);

		$queries = $this->getRequest()->getQueryParams();
		$slug = isset($queries['slug']) ? $queries['slug'] : '';		

		$this->_path = dirname(__FILE__);
		
		$this->editorial = $this->_table->read((int)$this->queryResult->id);
		$this->meta_description = $this->editorial->meta_description;
		$this->meta_title = $this->editorial->meta_title ?? 'Kutvek'; 
		if(array_key_exists('page', $queries)) $this->setCurrentPage((int)$queries['page']);
		$this->_items = $this->editorial->items;
		$this->paginate();
		$slices = $this->getSlices();	
		$cards =$this->_table->cards($slices);
		
		$page = $this->getCurrentPage();
		$pages = $this->getNumberOfPages();		
		$this->_view = 'pagination';
		$pagination = $this->partial(compact('pages', 'page'));		
		$this->_view = 'index';
		$this->_layout = 'index';		
		$this->_content = $this->partial(compact('cards', 'pagination', 'queries'));
		$this->getResponse()->getBody()->write($this->_print());
		return $this->getResponse();
	}

	public function listOfProducts(): array {	
		$category = $this->editorial->category_id;
		$this->_table->setStore((int)$this->editorial->d_store ?? 1);
		$this->_items = $this->_table->listOfProducts($category);
		// -2- paginer 
		$this->paginate();
		// -3- les cartes 
		$cards =  $this->_table->cards(array_column($this->_slices, 'id'));
		return $cards;
	}
}	