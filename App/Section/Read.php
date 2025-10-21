<?php
declare(strict_types=1);
namespace App\Section;
use App\AppAction;
use App\Product\Read as ProductRead;
use App\Section\Filters\{Color, Design, Model};
use Core\Component;
use Core\Request\UrlQueryResult;
use Domain\Table\Section;
use Exception;
use Library\HTML\{TraitSanitize, TraitPagination};
use stdClass;

/**
 * Section du site type catégorie
 */
final class Read extends AppAction implements UrlQueryResult {
	use TraitSanitize, TraitPagination;
	protected $section;
	private Section $_table;
	protected array $filters = [];
	protected string $cards;
	public stdClass $queryResult;
	public function setQueryResult(stdClass $query): void {$this->queryResult = $query; }

	public function __invoke() {
		try {
			$this->_table = new Section($this->_setDb());		
			$this->_table->setRoute($this->_route);		
			$queries = $this->getRequest()->getQueryParams();			
        	$slug = $this->_route->slug;
        	$section = $this->_table->readBySlug($slug);
			if(!$section):
				$this->_middleware = new ProductRead($this->_router);				
				return $this->handle($this->getRequest());           
			endif;
			$this->section = $section;
			// si on est sur du xhr/fetch js on envoie à filter			
			$withFilters = (int)$this->getRequest()->getHeaderLine('x-filtering');			
			if($withFilters > 0):
				$middleware = new Filter($this->_router);
				$queryResult = (object)[
					'department_store' 	=> $this->section->department_store,
					'categories'		=> $this->section->categories,
					'product_types'		=> $this->section->product_types
				];				
				$middleware->setQueryResult($queryResult);
				$this->_middleware = $middleware;
				return $this->handle($this->getRequest());
			endif;			
			$this->meta_title = $this->section->meta_title;
            $this->meta_description = $this->section->meta_description ?? $this->section->short_desc;   
			if(array_key_exists('page', $queries)) $this->setCurrentPage((int)$queries['page']);			
			//$this->l10ns = $this->section->l10ns;			
			$this->_items = $this->section->items;			
			$this->paginate();
			$slices = $this->getSlices();	
			$cards =$this->_table->cards($slices);
			$this->_view = 'widgets.pagination';
			$pagination = $this->partial();
			$this->_filters();
			$this->_view = 'widgets.filters';
			$filters = $this->partial();
			$this->_path = dirname(__FILE__);
			$this->_view = 'cards';
			$this->cards = $this->partial(compact('cards'));
			$this->meta_title = $this->section->meta_title;
			$this->meta_description = $this->section->meta_description;
			$this->_view = 'read';
			$this->_content = $this->partial(compact('pagination', 'cards', 'filters'));
			$this->_path = false;
			$this->_layout = 'section';			
			$this->_response->getBody()->write($this->_print());
			return $this->_response;
			
		}
		catch(Exception $e){
			$this->_response = $this->_response->withStatus(400);
			$this->_response->getBody()->write('Not Found !' . $e->getMessage());
			return $this->_response;	
		}		 	
	}

	private function _filters(){
		$filters = array_keys(array_filter(json_decode($this->section->filters,true)));
		$this->section->filters = $filters;
		foreach($filters as $filter){
			$f = match($filter){
			'model' => new Model($this->_route),
			'design' => new Design($this->_route),
			'color' => new Color($this->_route),
			//'vehicle' => new Vehicle($this->_route, $this->section->vehicle),
			default => ''
			};
			if($f instanceof Component) :
				$f->setRouter($this->_router);
				$f->setRequest($this->getRequest());
				$f->setDomain($this->_table, 'Section');
				$this->filters[] = $f;
			endif;
		}		
	}	
}