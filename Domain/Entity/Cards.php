<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use NumberFormatter;

class Cards extends Entity {
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';
	public $slug;
	public $opts = false;
	public $price = null;
	public $cover = null;
	public $min = 0;
	public $max = 0;

	public function __construct()
	{
		//$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		//$this->price = $this->price !== null ? json_decode($this->price) : null;
		$bytes = random_bytes(5);
		$this->slug = bin2hex($bytes);
		//$this->section = 'sportswear';	
	}

	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}

	protected function _designation(){	
		$search = ['{{family_name}}', '{{brand_name}}', '{{design_name}}', '{{color_name}}' , '{{vehicle_fullname}}', '{{type}}'];
		$replace = [$this->family_name, $this->brand_name, $this->design_name, $this->color_name, $this->vehicle_fullname ?? $this->model_name ?? null, null];
		return str_replace($search, $replace, $this->df_full_designation);
		unset($this->df_designation);
	}

	public function _cost(){
		if($this->price !== null){
			// On ajoute la tva ou pas
			$price = $this->_withVAT((float)$this->price->price);
			$price = $this->_setPrice($price);
			return <<<TEXT
			<span class="min-price">$price</span>
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
}