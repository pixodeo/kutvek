<?php
declare(strict_types=1);
namespace Domain\Entity;

use Core\Domain\Entity;
use NumberFormatter;

class ItemVehicle extends Entity {
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';	
	public $currency_code = 'EUR';
	
	public function __construct()
	{
		/*$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		if($this->price_template !== null):
            $price = json_decode($this->price_template);
            $this->price = $price->cost;
        else:
            $price = json_decode($this->price);
            $this->price = $price->cost;            
        endif;*/
	}

	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}	

	protected function _setPrice(float $price = 0.00, int $digits = 2)
	{		
		$this->_numFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $digits);
		return $this->_numFormatter->formatCurrency($price, $this->currency_code); // outputs €12.345,12
	}

	private function _designation(){
		return <<<EOT
			{$this->name } {$this->year}
		EOT;
	}	
}