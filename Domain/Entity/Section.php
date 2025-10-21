<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;
use Library\HTML\TraitSanitize;
use NumberFormatter;

class Section extends Entity {
	
	use TraitSanitize;
	private NumberFormatter $_numFormatter; 
	public $locale = 'fr';
	public function __construct()
	{
		$this->_numFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
		
		$this->bla = [];
	}

	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}	

	protected function _further(){		
		if($this->further_info !== null):
			$info = $this->specialchars_decode($this->further_info);
			return <<<TEXT
			<div class="vehicle-info">{$info}</div>
			TEXT;
		endif;		
	}	

	protected function _hat(){
		if($this->short_desc !== null) return <<<EOT
		<div class="short-desc">{$this->short_desc}</div>
		EOT;
		return '';
	}

	protected function _attributes(){

		return json_decode($this->attr??'[]', true);
	}
}