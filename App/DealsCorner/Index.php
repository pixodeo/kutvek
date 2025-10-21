<?php
declare(strict_types=1);
namespace App\DealsCorner;
use App\AppAction;

use Domain\Table\GoodDeal;
use Library\TraitPagination;

final class Index extends AppAction {
	use TraitPagination;

	protected GoodDeal $_table;
	public false|object $editorial;
	private $_filters = [];

	public function __invoke(){
		$this->_table = new GoodDeal($this->_setDb());	
		$this->_table->setRoute($this->_route);		
		$this->_table->setRequest($this->getRequest());	

		$queries = $this->getRequest()->getQueryParams();
		foreach($queries as $k => $values)
	    {
            switch($k){
            	case 'behaviors':
                	$this->_filters['behaviors'] = explode(',', $values);  
                    break;
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
		$this->_table->setStore((int)$this->editorial->department_store);
		$this->meta_description = $this->editorial->meta_description;
		$this->meta_title = $this->editorial->meta_title ?? 'Kutvek'; 

		if(array_key_exists('page', $queries)) $this->setCurrentPage((int)$queries['page']);

		$cards = $this->listOfProducts($this->editorial->category_id);
		$page = $this->getCurrentPage();
		$pages = $this->getNumberOfPages();		

		unset($queries['page']);
		$this->_view = 'pagination';

		$uri = $this->url('dealsCorner.index', ['queries'=>$queries]);
		$pagination = $this->partial(compact('pages', 'page', 'uri'));

		$categories = $this->_table->query("SELECT
			f.behavior_id, f.behavior_name
			FROM deals_corner_filters f
			WHERE l10n_id = :l10n_id
			GROUP BY behavior_id
			ORDER BY behavior_name",
			['l10n_id' => $this->getL10nId()]);
		$universes = $this->_table->query("SELECT
			f.family_id, f.family_name
			FROM deals_corner_filters f
			WHERE l10n_id = :l10n_id
			GROUP BY family_id
			ORDER BY family_name",
			['l10n_id' => $this->getL10nId()]);
		$brands = $this->_table->query("SELECT
			f.brand_id, f.brand_name
			FROM deals_corner_filters f
			WHERE l10n_id = :l10n_id
			GROUP BY brand_id
			ORDER BY brand_name",
			['l10n_id' => $this->getL10nId()]);

		$this->_view = 'index';		
		$this->_content = $this->partial(compact('cards', 'pagination', 'queries', 'categories' ,'universes', 'brands'));
		$this->_path = false;
		$this->_layout = 'deals-corner';

		$this->getResponse()->getBody()->write($this->_print());
		return $this->getResponse();
	}

	public function listOfProducts(int $id): array {		
		if(count($this->_filters) > 0):
			$this->_items = $this->_table->listOfProductsWithFilters((int)$id);
		else:
			$this->_items = $this->_table->listOfProducts($id);
		endif;

		// -2- paginer 
		$this->paginate();

		// -3- les cartes 
		$cards =  $this->_table->cards($this->_slices);
		return $cards;
	}

	public function getFilters():array{return $this->_filters;}
}	