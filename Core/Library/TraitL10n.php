<?php
declare(strict_types=1);
namespace Core\Library;

use stdClass;
use Core\Routing\RouteInterface;

trait TraitL10n {
	public stdClass $l10n;
	public $countryId = 62;
	public $currencyId = 1;
	public $currencyCode = 'EUR';
	public $countryCode = 'FR';

	public function getL10nId(): int {
		return ($this instanceof RouteInterface) ? $this->l10n->id : $this->_route->getL10nId();		
	}

	public function getL10nCode(): ?string {
		return ($this instanceof RouteInterface) ? $this->l10n->code : $this->_route->getL10nCode();		
	}

	public function getL10n():stdClass {
		return ($this instanceof RouteInterface) ? $this->l10n : $this->_route->getL10n();
	}

	public function getI18nId(): int {
		return ($this instanceof RouteInterface) ? $this->l10n->i18n_id : $this->_route->getI18nId();		
	}

	public function getI18n(): string {
		return ($this instanceof RouteInterface) ? $this->l10n->i18n : $this->_route->getI18n();		
	}


	public function setL10n(stdClass $l10n) {		
		if($this instanceof RouteInterface)
			$this->l10n = $l10n;
		else $this->_route->l10n = $l10n;		
	}	

	public function getCountryCode(): string {
		return ($this instanceof RouteInterface) ? $this->countryCode : $this->_route->getCountryCode();
	}

	public function getCurrencyId(): int {
		return ($this instanceof RouteInterface) ? $this->currencyId : $this->_route->getCurrencyId();
	}

	public function setCurrencyId(int $id){
		if($this instanceof RouteInterface)
			$this->currencyId = $id;
		else
			$this->_route->setCurrencyId($id);
	}

	public function setCountryCode(string $code){
		if($this instanceof RouteInterface)
			$this->countryCode = $code;
		else
			$this->_route->setCountryCode($code);
	}

	public function getCurrencyCode(): string {
		return ($this instanceof RouteInterface) ? $this->currencyCode : $this->_route->getCurrencyCode();		
	}

	public function setCurrencyCode(string $code){
		if($this instanceof RouteInterface)
			$this->currencyCode = $code;
		else
			$this->_route->setCurrencyCode($code);
	}	

	public function getLang():string{
		return match($this->getL10nId()){
			1 => 'fr',
			2 => 'fr-CA',
			3 => 'en',
			4 => 'en-US',
			5 => 'en-CA',
			default => 'en-CA',
		};
	}

	public function prefix(array $url) {
		return implode('/', array_filter($url, 'strlen'));
	}

	public function stripPrefix(){
		return ($this instanceof RouteInterface) ? $this->l10n->code : $this->_route->getL10nCode();
	}
}