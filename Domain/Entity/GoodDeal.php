<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use NumberFormatter;
use Library\HTML\TraitSanitize;

class GoodDeal extends Entity {
	use TraitSanitize;

	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';
	public $slug;

	public function __construct()
	{
		$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		$this->price = $this->price !== null ? json_decode($this->price) : null;
		$bytes = random_bytes(5);

		$this->slug = bin2hex($bytes);
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

	protected function _old(){
		if($this->price !== null){
			return $this->_setPrice((float)$this->price->old);
		}
	}
	protected function _new(){
		if($this->price !== null){
			return $this->_setPrice((float)$this->price->new);
		}
	}

	protected function _hat(){
		if($this->short_desc !== null):
			$this->short_desc = $this->specialchars_decode($this->short_desc);
		return <<<EOT
		<h2 class="short-desc">{$this->short_desc}</h2>
		EOT;
		endif;
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


}