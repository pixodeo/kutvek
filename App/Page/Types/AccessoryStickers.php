<?php
declare(strict_types=1);
namespace App\Product\Types;

use App\Product\Department;
use App\Product\Domain\Table\AccessorySticker;
use Core\Routing\RouteInterface;

class AccessoryStickers extends Department {
   protected $_viewFile = 'accessory-sticker';

   public function __construct(protected RouteInterface $_route)
   {
      $this->_table = new AccessorySticker($this->_setDb());
      $this->_table->setRoute($_route);
   }
   /**
    * Le produit
    *
    * @param      int   $id     The identifier
    */
   public function __invoke(int $id){      
      return $this->_table->product($id);
   }
   
   public function getName(){return __CLASS__;}
}