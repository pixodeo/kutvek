<?php
//declare(strict_types=1);
namespace App\Section\Domain;
use Core\Model\Table;

class SectionTable extends Table {

    public function getParams(): false|object
    {
        return false;
    }
}