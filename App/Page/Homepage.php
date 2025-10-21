<?php
declare(strict_types=1);
namespace App\Page;
use App\AppAction;
use Domain\Table\Page;
use Library\HTML\{Form, TraitSanitize};

final class Homepage extends AppAction  {
    use TraitSanitize;	
	private Page $_pageTable;
    public $app;	

	public function __invoke() {
        $isPro = false;
        $session_token = $this->getRequest()->getCookieParams()['session_token'] ?? false;
        if($session_token) {
            $cookie = $this->getCookie($session_token);
            $isPro = $cookie->type === 'pro' ? $cookie : false;            
        } 
		$this->_pageTable = new Page($this->_setDb());		
		$this->_pageTable->setRoute($this->_route);   
        $this->_layout = 'homepage';
        $this->_view = 'pages.homepage';
        $this->_content = $this->partial();  
        $this->_response->getBody()->write($this->_print());
		return $this->_response;              
	}

    public function setApp($app){
    	$this->app = $app;
    } 	
}