<?php
declare(strict_types=1);
namespace App\Product\Types;

use App\Product\Department;
class Basics extends Department {

   protected $_viewFile = 'basic';    

   public function getName(){return __CLASS__;}
}