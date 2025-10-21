<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use NumberFormatter;

class Option extends Entity {
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';
	public $slug;
	public $opts = false;
	public float|null $price = null;
	public ?string $picto = null;
	public ?string $opt_type = null;
	public function __construct()
	{
		$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		if($this->price === null) $this->price = 0.00;

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

	private function _label(){
    	if($this->price > 0):
    		$f = $this->_setPrice($this->price,0);
			return <<<TEXT
			<img src="/img/pictos/options/{$this->picto}" alt="" />
			<span>{$this->name}</span>
			<span class="cost">{$f}</span>
			TEXT;
    	endif;
    	return  $this->name;
    }


    private function _text(){
    	if($this->price > 0):
    		$f = $this->_setPrice($this->price,2);
			return <<<TEXT
			{$this->name} +{$f}		
			TEXT;
    	endif;
    	return  $this->name;
    }   
}