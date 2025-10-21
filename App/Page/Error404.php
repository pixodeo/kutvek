<?php
declare(strict_types=1);
namespace App\Page;
use App\AppAction;
use Core\Request\UrlQueryResult;
use Domain\Table\Page;
use stdClass;

final class Error404 extends AppAction implements UrlQueryResult {
	public stdClass $queryResult;	
	private Page $_pageTable;
	public $app;

	public function setQueryResult(stdClass $query): void { }	

	public function __invoke() {

		$this->_pageTable = new Page($this->_setDb());		
		$this->_pageTable->setRoute($this->_route);
		$this->_response = $this->_response->withStatus(404); 
        $slugs = [];
        $url = '';
        $this->_layout = 'error-404';
        $this->_view = 'pages.404';
        $page = $this->_pageTable->notFound();
        $this->_content = $this->partial(compact('page', 'slugs', 'url'));        
        $this->_response->getBody()->write($this->_print());
        return $this->_response; 
	}

    public function setApp($app){
    	$this->app = $app;
    } 	
}