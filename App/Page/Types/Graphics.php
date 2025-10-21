<?php
declare(strict_types=1);
namespace App\Product\Types;

use App\Product\Department;
use App\Product\Domain\Table\Graphic;
use Core\Routing\RouteInterface;

class Graphics extends Department {
	protected $_viewFile = 'graphic-kit';
	

   public function __construct(protected RouteInterface $_route)
   {
      $this->_table = new Graphic($this->_setDb());
      $this->_table->setRoute($_route);
   }

	public function getName(){return __CLASS__;}
	
   public function __invoke(int $id){        
      $this->product = $this->_table->product($id);
      return $this->product;
   }

   public function vehicles(): array
   {      
      return $this->_table->vehicles($this->product->vehicle->id);
   }
}