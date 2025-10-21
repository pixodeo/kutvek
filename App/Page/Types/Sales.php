<?php
declare(strict_types=1);
namespace App\Product\Types;

use App\Product\Department;
use App\Product\Domain\Table\Sale;
use Core\Routing\RouteInterface;

class Sales extends Department{   

   protected $_viewFile = 'sale';

   public function __construct(protected RouteInterface $_route)
   {
      $this->_table = new Sale($this->_setDb());
      $this->_table->setRoute($_route);
   }
   public function getName(){return __CLASS__;}

   public function __invoke(int $id){        
      $this->product = $this->_table->product($id);

      return $this->product;
   }

   /**
    * Convient pour kit déco , housse de selle
    * En fonction du behavior 
    */
   public function suitableFor():string{
      $this->_path = dirname(__FILE__,2);
      $this->_view = 'suitable-for';

      return match($this->product->behavior_id){
            1,5,11 => $this->partial(),
            default => ''
         };    
   }
}