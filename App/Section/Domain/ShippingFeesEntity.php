<?php
declare(strict_types=1);
namespace App\Checkout\Domain;

use Core\Model\Entity;

class ShippingFeesEntity extends Entity
{
	public $chrono_13;
	public $chrono_classic;
	
	public function __construct()
	{
		if($this->suppl_corse > 0) {
			$this->chrono_13 = $this->chrono_13 + 25.60;
		}
		if($this->area == 4 && $this->classic == 1) {
			$this->chrono_classic = $this->chrono_classic + $this->shipping_surcharge_c4;
		}
	}
}