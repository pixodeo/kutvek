<?php
declare(strict_types=1);
namespace App\Product;

use Core\Routing\{RouteInterface, RouterInterface, TraitRequest};
use Core\Library\{TraitModel,TraitResponse,TraitL10n};
use Core\Responder;
use Domain\Table\Catalog;
use Library\HTML\{TraitSanitize, Form};
use Library\TraitProduct;
use Psr\Http\Message\ResponseInterface;

abstract class Department implements Strategy {
	use TraitModel, TraitResponse, TraitL10n, TraitRequest, TraitSanitize, TraitProduct;
	protected false|Catalog $_table;	
	protected $_viewFile = 'test';	
	public Form $form;

	public function __construct(protected RouteInterface $_route){
		$this->_table = new Catalog($this->_setDb());
     	$this->_table->setRoute($_route);
	}

	public function __invoke(int $id){ 		
      	$this->product = $this->_table->product($id);
      	return $this->product;
   	}
	
	public function setRoute(RouteInterface $route): void {$this->_route = $route;}	

	public function vehicles():array{
		return [];
	}

	public function suitableFor(): string	{
		return '';
	}

	public function colours():string {
		return '';
	}

	public function departments(int $category_id): array{
		return $this->_table->departments($category_id);
	}

	public function setRouter(RouterInterface $router){ $this->_router = $router;}
	
	public function setResponse(ResponseInterface $response){
        if($this->_responder && $this->_responder instanceof Responder) $this->_responder->setResponse($response);
        else $this->_response = $response;
    }

		
}