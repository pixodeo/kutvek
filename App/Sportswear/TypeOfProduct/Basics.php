<?php
declare(strict_types=1);
namespace App\Sportswear\TypeOfProduct;

final class Basics extends Strategy {
    protected $_viewFile = 'basic';
    public function getName(){return __CLASS__;}
}