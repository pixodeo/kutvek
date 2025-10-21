<?php
declare(strict_types=1);
namespace App\SaddleCover;
use App\AppAction;

use Domain\Table\SaddleCover;
use Exception;
use Library\TraitPagination;

final class Filter extends AppAction {
	use TraitPagination;
	protected SaddleCover $_table;	
	protected $category;

	public function __invoke(){		
		$l10n = new \stdClass();
		$l10n->id = $this->getL10nId();
		$l10n->code = $this->getL10n();
		$queries = $this->getRequest()->getQueryParams();
		$slug = isset($queries['slug']) ? $queries['slug'] : '';

		
		try{
			$this->_table = new SaddleCover($this->_setDb());
			$this->_table->setL10n($l10n);
			$this->_table->setCountryCode($this->_country);
			$this->_table->setCurrencyCode($this->_currency);
			$this->_table->setCurrencyId($this->_currencyId);	
			$this->_table->setRequest($this->getRequest());	
			
			$this->category = $this->_table->store($slug);
			$this->_table->setStore((int)$this->category->department_store);
			$cards = $this->listOfProducts();
			if(array_key_exists('page', $queries)) $this->setCurrentPage((int)$queries['page']);	
			foreach($queries as $k => $values)
	        {
	            switch($k){
	                case 'universes':
	                	// filtre des marques inutiles	                    
	                    break;
	                case 'page':
	                	$this->setCurrentPage((int)$queries['page']);
	                	break;
	                /*case 'brands':
	                    $brands = explode(',', $values);                   
	                    $plh = $this->namedPlaceHolder($brands, $k);
	                    $params = array_merge($params, $plh->values);
	                    $sql .= " AND v.brand IN({$plh->place_holder})";  
	                    break;*/
	            }                              
	        } 		
			$page = $this->getCurrentPage();
			$pages = $this->getNumberOfPages();	

			$this->_path = dirname(__FILE__);		
			$this->_view = 'pagination';
			$pagination = $this->partial(compact('pages', 'page'));
			$this->_view = 'cards';
			$body = $this->partial(compact('cards', 'pagination'));
			$this->_response->getBody()->write($body);
			return $this->_response;
		}
		catch(Exception $e){
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');
			$this->_response = $this->_response->withStatus('400');
			$this->_response->getBody()->write($e->getMessage());
			return $this->_response;
		}	
		
		//$this->_content = $this->partial(compact('queries', ));
		//$this->_path = false;
		//$this->_layout = 'saddle-covers';
		//return $this->_print();
		
	}

	public function listOfProducts(): array {	
		
	
		$this->_items = $this->_table->listOfProductsWithFilters((int)$this->category->category_id);
		// -2- paginer 
		$this->paginate();
		// -3- les cartes 
		$cards =  $this->_table->cards($this->_slices);
		return $cards;
	}

	public function brands(){
		$brands = [];		
		return $brands;

	}

	public function vehicles(){
		$vehicles = [];		
		return $vehicles;
	}

	public function colours(){
		$colours = [];		
		return $colours;
	}
}