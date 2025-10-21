<?php
declare(strict_types=1);
namespace App\Catalog;
use App\AppAction;

use Domain\Table\Section;
use Exception;
use Library\HTML\{TraitSanitize};
use stdClass;

final class Filter extends AppAction {
	use TraitSanitize;
	protected $section;
	private Section $_table;
	protected array $filters = [];

	public function __invoke() {
		try{
			$queries = $this->getRequest()->getQueryParams();		
			$slug = isset($queries['slug']) ? $queries['slug'] : 'false';
			$models = isset($queries['model']) ? explode(',', $queries['model']) : [];
			$colors = isset($queries['color']) ? explode(',', $queries['color']) : [];
			$designs = isset($queries['design']) ? explode(',', $queries['design']) : [];
			$vehicles = isset($queries['vehicle']) ? explode(',', $queries['vehicle']) : [];
			$this->_table = new Section($this->_setDb());
			$this->_table->setRoute($this->_route);	
			$this->_table->hasFilters();		
			$this->_table->setFilters('model', $models)
			->setFilters('color', $colors)
			->setFilters('design', $designs)
			->setFilters('vehicle', $vehicles);
			$this->section = $this->_table->section($slug);
			
			$obj = new stdClass();
			$obj->models = $models;
			$obj->designs = $designs;
			$obj->colors = $colors;
			$obj->slug = $slug;
			
			$this->_path = dirname(__FILE__);
			$this->_view = 'cards';
			$cards = $this->partial();
			$obj->cards = $cards;
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');  
			$this->_response->getBody()->write(json_encode($obj)); 
			return $this->_response;    	
		}
		catch(Exception $e){
			$obj = new stdClass();
			$obj->msg = $e->getMessage();
			$this->_response = $this->_response->withStatus(400);
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');  
			$this->_response->getBody()->write(json_encode($obj));
			return $this->_response; 
		}
				
    }	
}