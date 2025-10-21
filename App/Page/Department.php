<?php
declare(strict_types=1);
namespace App\Product;

use Core\Routing\{RouteInterface,TraitUri, TraitRequest};
use Core\Library\{TraitModel,TraitResponse,TraitL10n};
use Domain\Table\Catalog;

abstract class Department implements Strategy {
	use TraitModel, TraitUri, TraitResponse, TraitL10n, TraitRequest;
	protected false|Catalog $_table;
	//protected false|Catalog $_table = false;	
	protected $_viewFile = 'test';
	public $product;

	public function __construct(protected RouteInterface $_route){
		$this->_table = new Catalog($this->_setDb());
     	$this->_table->setRoute($_route);
	}

	public function __invoke(int $id){ 
		
      	$this->product = $this->_table->product($id);
      	return $this->product;
   	}
	
	public function setRoute(RouteInterface $route): void {$this->_route = $route;} 	

	public function getName(){return __CLASS__;}

	public function getView(): string {
		return $this->_viewFile;
	}

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

		
}