<?php
declare(strict_types=1);
namespace Domain\Decorator;

interface Component
{  

    public function getId():int;
    public function hasContent():bool;    
}