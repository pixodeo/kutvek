<?php
declare(strict_types=1);
namespace App\Checkout\PSP;

use App\Checkout\PSP\PayPal\Sandbox;
use App\Checkout\PSP\PayPal\Live;

trait TraitServiceProvider {
	private function _psp(string $psp): false|ServiceProvider {
		$psp = match ($psp) {
			'paypal' => new Sandbox($this->_route),
			'paylater' => new PayLater,
			'giftcard' => new GiftCard,
			default =>  false
		};
		if($psp):
			$psp->setRequest($this->getRequest());
		endif;
		return $psp;
	}	
}