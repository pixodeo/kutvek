<?php
declare(strict_types=1);
namespace App\Page;
use App\AppAction;
use App\Product\Read AS ProductRead;
use App\Section\Read as SectionRead;
use Core\Request\UrlQueryResult;
use Domain\Table\Page;
use Library\HTML\{Form, TraitSanitize};
use stdClass;


final class Read extends AppAction implements UrlQueryResult {
	use TraitSanitize;

	public stdClass $queryResult;
	
	private Page $_pageTable;

	public $app;

	public function setQueryResult(stdClass $query): void { }	

	public function __invoke() {

		$this->_pageTable = new Page($this->_setDb());		
		$this->_pageTable->setRoute($this->_route);		
        $slug = ltrim($this->getRequest()->getUri()->getPath(), '/');
        $slug = $this->trimUrl($slug);       
        $page = $this->_pageTable->readBySlug($slug);

        if(isset($_GET['page_test'])):
            die(print_r($page));
        endif;
        if(!$page):
            $this->_middleware = new SectionRead($this->_router);
            $this->handle($this->getRequest());
            return $this->_response;
        endif;
        $xhr = $this->getRequest()->getHeaderLine('x-requested-with');              
        if($page){           
            $this->_layout =  $xhr === 'XMLHttpRequest' ? 'minimalist' : $page->layout ?? 'page';
            $this->_view =  $xhr === 'XMLHttpRequest' ? 'pages.minimalist' :  $page->template ?? 'pages.show';
            $this->meta_title = $page->meta_title;
            $this->meta_description = $page->meta_description ?? $page->short_description;   
            $page->content = $this->specialchars_decode($page->content);
            $slugs = $this->_pageTable->slugsById($page->page);
            foreach($slugs as $s){
                $s->uri = $this->matchPrefixUri($s->l10n, $s->slug);
            }
            $this->_content = $this->partial(compact('page', 'slugs',  'slug'));
            $this->_response->getBody()->write($this->_print(compact('slugs')));
			return $this->_response;           
        } else {
        	// handle product 
        	$this->_middleware = new ProductRead($this->_router);            
            // Temporaire
            $this->_middleware->setApp($this->app);
        	$this->handle($this->getRequest());
            return $this->_response;
        }        
	}

    public function setApp($app){
    	$this->app = $app;
    } 	
}