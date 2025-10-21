<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use NumberFormatter;

class Appareal extends Entity {
	public $id;
	
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';
	public function __construct()
	{
		$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		$this->price = $this->price !== null ? json_decode($this->price) : null;		
		$this->section = 'sportswear';	
	}

	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}

	protected function _designation(){		
		return $this->full_designation;		
	}

	public function _cost(){
		if($this->price !== null){
			// On ajoute la tva ou pas
			$price = $this->_withVAT((float)$this->price->price);
			$price = $this->_setPrice($price);
			return <<<TEXT
			<span class="price">$price</span>
			TEXT;
		}
	}

	protected function _setPrice(float $price = 0.00, int $digits = 2)
	{		
		$this->_numFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $digits);
		return $this->_numFormatter->formatCurrency($price, $this->currency_code); // outputs €12.345,12
	}
	protected function _withVAT(float $price):float{
		if($this->country_vat > 0 && $this->currency_id !== 1) return $price *  (1 + ($this->tax_rate / 100));
		return $price;
	}

	protected function _hat(){
		if($this->short_desc !== null) return <<<EOT
		<div class="short-desc">{$this->short_desc}</div>
		EOT;
		return '';
	}
}