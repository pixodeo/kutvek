<?php
declare(strict_types=1);
namespace Core;

use Core\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Core\Model\Table;

abstract class Behavior
{ 
	use \Core\Library\TraitCore, \Core\Library\TraitCookie, \Core\Library\TraitModel, \Core\Library\TraitUtils, \Core\Library\TraitResponse;
    
    protected $_payload;
   
    protected false|object $_customer = false;   
    protected array $_tables = [];   
    protected string $_breadcrumb = '';	

	public function setRouter(RouterInterface $router):void { $this->_router = $router;}

    public function getRouter(): RouterInterface { return $this->_router;}

    public function setRequest(ServerRequestInterface $request): ServerRequestInterface{
        $this->_request = $request;
        return $this->_request;
    }

    public function getRequest(): ServerRequestInterface {return $this->_request;}

    public function setResponse(ResponseInterface $response) {$this->_response = $response;}

    public function getResponse(): ResponseInterface {return $this->_response;}

    public function setCustomer(){
        $session_token = $this->getRequest()->getCookieParams()['session_token'] ?? false;
        if($session_token) $this->_customer = $this->getCookie($session_token);
    }
    
    public function isCustomerPro(): bool {return $this->_customer ? $this->_customer->type === 'pro' : false;}

    

    public function __set(string $name, mixed $value):void {
        $this->{$name} = $value;
    }	

    public function getRebate(): ?string{
        if($this->_customer && property_exists($this->_customer, 'rebate') && $this->_customer->rebate > 0){
            $span = '<span class="pro-rebate">';
            $span .= '<span data-i18n="pro-rebate">Remise Pro</span>';
            $span .= '<span class="percent">';
            $span .= '- ' . $this->_customer->rebate . '%';
            $span .= '</span>';
            $span .= '</span>';
            return $span;
        }
        return null;
    }    

    public function fetch($type){
        $str='';
        foreach ($this->$type as $v) {
            $str.= "$v\n";
        }
       return $str;
    }

    public function setBreadcrumb(string $breadcrumb):void {$this->_breadcrumb = $breadcrumb;}
    protected function getBreadcrumb():string {return $this->_breadcrumb;}  

    abstract public function getTable(string $key): Table; 
}