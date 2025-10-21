<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use NumberFormatter;

class Finish extends Entity {
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';	
	public $currency_code = 'EUR';
	public float $price;

	public function __construct()
	{
		$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		$this->price_format = $this->_setPrice($this->price);
	}

	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}

	protected function _label() {
		return $this->price > 0 ?  "{$this->name} + {$this->price_format}" : "{$this->name}";		
	}

	protected function _setPrice(float $price = 0.00, int $digits = 2)
	{		
		$this->_numFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $digits);
		return $this->_numFormatter->formatCurrency($price, $this->currency_code); // outputs €12.345,12
	}
}