<?php
declare(strict_types=1);

namespace Domain\Entity;
use Core\Domain\Entity;
use NumberFormatter;

class ShippingFees extends Entity {

	public $chrono_13;
	public $chrono_classic;
	public NumberFormatter $formatter;
	public $locale = 'fr';
	public $currency_code = 'EUR';

	
	public function __construct()
	{
		if($this->suppl_corse > 0) {
			$this->chrono_13 = $this->chrono_13 + 25.60;
		}
		if($this->area == 4 && $this->classic == 1) {
			$this->chrono_classic = $this->chrono_classic + $this->shipping_surcharge_c4;
		}
		$this->formatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
	}
	
	public function c_classic():float{
		if($this->country_vat > 0 && (int)$this->country_id === 62):
			return (float)($this->chrono_classic * 1.20);
		else:
			return (float)$this->chrono_classic;
		endif;	
	}
	public function c_13():float{
		if($this->country_vat > 0 && (int)$this->country_id === 62):
			return (float)($this->chrono_13 * 1.20);
		else:
			return (float)$this->chrono_13;
		endif;
	}
	public function c_express():float{
		//a_c.vat = 1 AND a_c.id != 62 AND bc.rebate IS NOT NULL AND bc.rebate > 0 THEN 0
		//const chronoExpress = vat > 0 && shipping_country == 62 ? parseFloat(shipping_cost.chrono_express * 1.20) : parseFloat(shipping_cost.chrono_express);
		if($this->country_vat > 0 && (int)$this->country_id === 62):
			return (float)($this->chrono_express * 1.20);
		else:
			return (float)$this->chrono_express;
		endif;
	}

	public function format(float $price, int $digits = 2):string{
		$this->formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $digits);
        return $this->formatter->formatCurrency((float)$price, $this->currency_code);
	}
}