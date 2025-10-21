<?php
declare(strict_types=1);
namespace App\Product\Types;

final class Basics extends Type {   

   public function __invoke() {      
      $this->_path = dirname(__FILE__,2);
      $this->_view = 'basic';
      $this->_content = $this->partial();
      $this->_path = false;
      $this->_layout = 'basics';
      $this->_response->getBody()->write($this->_print());
      return $this->_response;
   }
}