<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use NumberFormatter;

class SaddleCover extends Entity {
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';
	public $slug;
	public $opts = false;
	public $vehicle_fullname = null;
	public $saddle_type_name = null;

	public function __construct()
	{
		$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		$this->price = $this->price !== null ? json_decode($this->price) : null;
		$bytes = random_bytes(5);
		$this->prices = json_decode($this->prices);

		$this->slug = $this->item_slug ?? bin2hex($bytes);
		$this->section = $this->l10n_id === 1 ? 'housses-de-selle' : 'seat-covers';
		$opts = array_filter([$this->foam, $this->install]);
		if(count($opts) > 0) $this->opts = true;
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
		if($this->vehicle_fullname === null):
			$this->vehicle_fullname = $this->family_name . ' ' . $this->brand_name;
		endif;
		$replace = [$this->saddle_type_name, $this->design_name, $this->color_name, $this->vehicle_fullname ?? $this->family_name ?? null];
		$designation =  str_replace($search, $replace, $this->df_full_designation ?? '');
		return ucwords($designation);
	}

	protected function _setPrice(float $price = 0.00, int $digits = 2)
	{		
		$this->_numFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $digits);
		return $this->_numFormatter->formatCurrency($price, $this->currency_code); // outputs €12.345,12
	}

	protected function _min(){
		if($this->prices !== null){
			// On ajoute la tva ou pas
			$price = $this->_withVAT((float)$this->prices->min);
			$price = $this->_setPrice($price);
			return <<<TEXT
				<span class="min-price">$price</span>
				TEXT;
		}
	}
	protected function _max(){
		if($this->prices !== null){
			if($this->prices->unique < 1):
				$price = $this->_withVAT((float)$this->prices->max);
				$price = $this->_setPrice($price);
				return <<<TEXT
				<span class="max-price">$price</span>
				TEXT;
			else:
				return '';
			endif;
		}
	}	

	protected function _hat(){
		if($this->short_desc !== null) return <<<EOT
		<h2 class="short-desc">{$this->short_desc}</h2>
		EOT;
		return '';
	}

	protected function _suitableFor(){
		if(count($this->suitable) > 0) {
			$li = [];
			foreach($this->suitable as $suitable){
				$li[] = '<li>' . $suitable->fullname . '</li>';
			}
			$li = implode('', $li);
			return <<<EOT
			<ul class="suitable-for">{$li}</ul>
			EOT;
		}
		return '';
	}
	protected function _withVAT(float $price):float{
		if($this->country_vat > 0 && $this->currency_id !== 1) return $price *  (1 + ($this->tax_rate / 100));
		return $price;
	}


}