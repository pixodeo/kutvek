<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use Core\Routing\RouteInterface;
use Domain\Decorator\{Component,Basics, Decorator, Graphics};
use NumberFormatter;

class Product extends Entity implements Component {
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';
	public $slug;
	public $opts = false;
	private false|Decorator $_decorator = false;

	public function __construct(RouteInterface $route)
	{
		$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		$this->_route = $route;	
	}

	public function hasContent(): bool { return $this->hasContent !== null;}

	public function getId(): int { return (int)$this->id;}

	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}		

	private function _cost(){
		$this->price = $this->price !== null ? json_decode($this->price) : null;
		if($this->price !== null){
			// On ajoute la tva ou pas
			$price = $this->_withVAT((float)$this->price->price);
			$price = $this->_setPrice($price);
			return <<<TEXT
			<span>$price</span>
			TEXT;
		}
	}

	private function _amount(){
		$this->price = $this->price !== null ? (is_object($this->price) ? $this->price :  json_decode($this->price)) : null;
		if($this->price === null) return 0.00;
		return $this->_withVAT((float)$this->price->price);

	}


	public function getDecorator(): Decorator {
		if (!$this->_decorator):
			$this->_decorator = match($this->behavior_type){
			'Graphics' => new Graphics($this, $this->_route),
			default => new Basics($this, $this->_route)
			};
		endif;
		return $this->_decorator;
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

	private function _title(){
		if($this->full_designation !== null) return $this->full_designation;

		// sinon on fait appel à un decorateur 
		return $this->getDecorator()->title();
	}

	protected function _hat(){
		if($this->l10n_short_desc !== null) return <<<EOT
		<h2 class="short-desc">{$this->l10n_short_desc}</h2>
		EOT;
		return '';
	}

	private function _meta_title(){
		if($this->l10n_meta_title !== null) return $this->l10n_meta_title;
	}

	private function _meta_description(){
		if($this->l10n_meta_description !== null) return $this->l10n_meta_description;
	}

	private function _description(){
		if($this->l10n_description !== null) return $this->l10n_description;
	}

	private function _features(){
		if($this->l10n_features !== null) return $this->l10n_features;
	}

	private function _composition_care(){
		if($this->l10n_composition_care !== null) return $this->l10n_composition_care;
	}


	protected function _url(){
		if($this->item_slug !== null) implode('/', array_filter([FQDN, $this->_prefix, $this->item_slug]));
		return $this->item_slug.'-'.$this->id;
		// sinon on fait appel à un decorateur 
		return $this->getDecorator()->slug();
	}
	
}