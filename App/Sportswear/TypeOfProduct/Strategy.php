<?php
declare(strict_types=1);
namespace App\Sportswear\TypeOfProduct;

use Core\Routing\{RouteInterface, TraitRequest};
use Core\Library\{TraitModel,TraitResponse,TraitL10n};
use Domain\Table\Catalog;
use Library\HTML\Form;

abstract class Strategy {
	use TraitModel,TraitResponse, TraitL10n, TraitRequest;
	protected false|Catalog $_table;	
	protected $_viewFile = 'read';
	public $product;
	protected Form $form;

	public function __construct(protected RouteInterface $_route){
		$this->_table = new Catalog($this->_setDb());
     	$this->_table->setRoute($this->_route);
     	$this->form = new Form;
     	
	}

	public function __invoke(int $id){ 
		
      	$this->product = $this->_table->read($id);
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

	

	public function getSizes():string {
		return '';
	}

		
}