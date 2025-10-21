<?php
declare(strict_types=1);
namespace Domain\Entity\Checkout;
use Core\Domain\Entity;

class VoucherError extends Entity {
	public $name = 'balance';
	public $min_purchase;
	public $designation;
	public $description;
	public $currency_code = 'EUR';
	public $_locale =  'fr';
	public $fallback;

	
	public function __construct(){
		$search = ['{{min_purchase}}','{{fallback}}'];
		$min = $this->_setPrice((float)$this->min_purchase ?? 0.00);
		$replace = [$min, $this->fallback];
		$this->description = str_replace($search, $replace, $this->description ?? '');
	}

	protected function _setPrice($price = 0, $digits = 2)
    {
        $a = new \NumberFormatter($this->_locale, \NumberFormatter::CURRENCY);
        $a->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $digits);
        return $a->formatCurrency($price, $this->currency_code); // outputs €12.345,12
    }
}