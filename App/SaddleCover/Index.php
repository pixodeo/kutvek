<?php
declare(strict_types=1);
namespace App\SaddleCover;
use App\AppAction;
use Core\Request\UrlQueryResult;
use Domain\Table\SaddleCover;
use Library\HTML\TraitSanitize;
use Library\TraitPagination;
use stdClass;

final class Index extends AppAction implements UrlQueryResult {
	use TraitPagination, TraitSanitize;

	protected SaddleCover $_table;
	public false|object $editorial;
	private $_filters = [];
	public stdClass $queryResult;
	public function setQueryResult(stdClass $query): void {$this->queryResult = $query; }

	public function __invoke(){
		$this->_table = new SaddleCover($this->_setDb());
		$this->_table->setRoute($this->_route);		
		$this->_table->setRequest($this->getRequest());	

		$queries = $this->getRequest()->getQueryParams();
		foreach($queries as $k => $values)
	    {
            switch($k){
                case 'universes':
                	$this->_filters['universes'] = explode(',', $values);                   
                    break;
                case 'colors':
                	$this->_filters['colors'] = explode(',', $values);  
                	break;
                case 'brands':
                   	$this->_filters['brands'] = explode(',', $values); 
                    break;
                case 'vehicles':
                   	$this->_filters['vehicles'] = explode(',', $values);
                    break;
            }                              
	    } 
		$slug = isset($queries['slug']) ? $queries['slug'] : '';	
		$this->_path = dirname(__FILE__);		
		$this->editorial = $this->_table->editorial($slug);
		$this->meta_description = $this->editorial->meta_description;
		$this->meta_title = $this->editorial->meta_title ?? 'Kutvek'; 
		if(array_key_exists('page', $queries)) $this->setCurrentPage((int)$queries['page']);
		$cards = $this->listOfProducts();
		$page = $this->getCurrentPage();
		$pages = $this->getNumberOfPages();	
		unset($queries['page']);
		$this->_view = 'pagination';
		$uri = $this->url('saddleCover.index', ['queries'=>$queries]);
		$pagination = $this->partial(compact('pages', 'page', 'uri'));	
		
		$universes = $this->_table->query("SELECT
			v.universe AS 'family_id',
			v.fam_name AS 'family_name'
			FROM `item_vehicles` i_v
			JOIN saddle_covers s_c ON s_c.id = i_v.item
			JOIN item_stores i_s ON (i_s.item = s_c.id AND i_s.status = 1)
			JOIN vue_vehicle_2 v ON v.id = i_v.vehicle AND v.l10n = :l10n
			GROUP BY v.universe
			ORDER BY v.position
			",
			['l10n' => $this->getL10nId()]);

		$brands = $this->_table->query("SELECT
			v.brand AS 'brand_id',
			v.brand_name
			FROM `item_vehicles` i_v
			JOIN saddle_covers s_c ON s_c.id = i_v.item
			JOIN vue_vehicle_2 v ON v.id = i_v.vehicle AND v.l10n = :l10n
			GROUP BY v.brand
			ORDER BY v.brand_name
			",
			['l10n' => $this->getL10nId()]);

		$colors = $this->_table->query("SELECT
			c.color AS 'color_id',
			CASE 
				WHEN c.color = 127 AND c.l10n IN (1,2) THEN 'Bleu' 
				WHEN c.color = 127 AND c.l10n IN (3,4,5,6) THEN 'Blue' 
				ELSE c.designation 
			END  AS 'color_name'
			FROM item_colors i_c
			JOIN saddle_covers s_c ON s_c.id = i_c.item
			JOIN color_l10ns c ON (c.color = i_c.color AND c.l10n = :l10n)
			GROUP BY c.color
			ORDER BY c.designation
			",
			['l10n' => $this->getL10nId()]);
		$this->_view = 'index';
		
		$this->_layout = 'index';		
		$this->_content = $this->partial(compact('cards', 'pagination', 'queries', 'universes', 'brands', 'colors'));

		$this->getResponse()->getBody()->write($this->_print());
		return $this->getResponse();
	}

	public function listOfProducts(): array {	
		$category = $this->editorial->category_id;
		$this->_table->setStore((int)$this->editorial->d_store ?? 1);	

		// -1- items	
		if(count($this->_filters) > 0):
			$this->_items = $this->_table->listOfProductsWithFilters((int)$category);
		else:
			$this->_items = $this->_table->listOfProducts($category);
		endif;

		// -2- paginer 
		$this->paginate();

		// -3- les cartes 
		$cards =  $this->_table->cards($this->_slices);
		return $cards;
	}

	public function getFilters():array{return $this->_filters;}
}	