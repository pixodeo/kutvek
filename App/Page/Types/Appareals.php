<?php
declare(strict_types=1);
namespace App\Product\Types;

use App\Product\Department;

class Appareals extends Department {
    protected $_viewFile = 'appareal';
    public function getName(){return __CLASS__;}
}