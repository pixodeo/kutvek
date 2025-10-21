<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use Core\Routing\RouteInterface;
use Domain\Decorator\{Component,Basics, Decorator, Graphics};
use NumberFormatter;

class Card extends Entity implements Component {
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';
	public $slug;
	public $opts = false;
	private false|Decorator $_decorator = false;

	public function __construct(RouteInterface $route)
	{

		$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		$this->_route = $route;		
		$bytes = random_bytes(5);
		$this->slug = bin2hex($bytes);
		$this->section = 'sportswear';
	}

	public function hasContent(): bool { return $this->hasContent !== null;}

	public function getId(): int { return (int)$this->id;}

	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}		

	public function _cost(){
		$this->price = $this->price !== null ? json_decode($this->price) : null;
		if($this->price !== null){
			// On ajoute la tva ou pas
			$price = $this->_withVAT((float)$this->price->price);
			$price = $this->_setPrice($price);
			return <<<TEXT
			<span class="min-price">$price</span>
			TEXT;
		}
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

	/*protected function _hat(){
		if($this->short_desc !== null) return <<<EOT
		<h2 class="short-desc">{$this->short_desc}</h2>
		EOT;
		return '';
	}*/


	protected function _url(){
		if($this->item_slug !== null) return implode('/', array_filter([FQDN, $this->_prefix, $this->item_slug]));		

		// sinon on fait appel à un decorateur 
		return $this->getDecorator()->slug();
	}
	
}