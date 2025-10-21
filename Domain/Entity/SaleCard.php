<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use NumberFormatter;

class SaleCard extends Entity {
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';
	public $slug;

	public function __construct()
	{
		$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		$this->price = $this->price !== null ? json_decode($this->price) : null;
		$bytes = random_bytes(5);
		$this->prices = json_decode($this->prices);

		$this->slug = $this->item_slug ? $this->item_slug : bin2hex($bytes);
		$this->section = $this->l10n_id === 1 ? 'coin-des-bonnes-affaires' : 'deals-corner';
	}

	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}

	protected function _designation(){		
		if($this->full_designation !== null) return $this->full_designation;
		$search = ['{{type}}', '{{design_name}}', '{{color_name}}', '{{vehicle_fullname}}'];
		$replace = [null, $this->design_name, $this->color_name, $this->vehicle_fullname];
		return str_replace($search, $replace, $this->df_full_designation ?? '');
	}

	protected function _setPrice(float $price = 0.00, int $digits = 2)
	{		
		$this->_numFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $digits);
		return $this->_numFormatter->formatCurrency($price, $this->currency_code); // outputs €12.345,12
	}

	protected function _new(){
		if($this->price !== null){
			// On ajoute la tva ou pas
			$price = $this->_withVAT((float)$this->price->new);
			$price = $this->_setPrice($price);
			return <<<TEXT
				<span class="new">$price</span>
				TEXT;
		}
	}
	protected function _old(){
		if($this->price !== null){
			
				$price = $this->_withVAT((float)$this->price->old);
				$price = $this->_setPrice($price);
				return <<<TEXT
				<span class="old">$price</span>
				TEXT;
		
				
			
		}
	}

	protected function _withVAT(float $price):float{
		if($this->country_vat > 0 && $this->currency_id !== 1) return $price *  (1 + ($this->tax_rate / 100));
		return $price;
	}
}