<?php
declare(strict_types=1);
namespace App\Product\Types;
use Core\Routing\{RouteInterface};

class AccessorySticker extends Type {
   
   public function __construct(protected RouteInterface $_route){
      $this->_path = dirname(__FILE__,2);
      $this->_view = 'accessory-sticker';
   }
   
   public function __invoke(){      
      $this->_response->getBody()->write($this->partial());
      return $this->_response;
   }   
}