<?php
declare(strict_types=1);
namespace App\Section\Domain;

use Core\Model\Entity;

class ContentEntity extends Entity
{	
	public function getBehavior(){
		return ucfirst($this->behavior_type);
	}	
}