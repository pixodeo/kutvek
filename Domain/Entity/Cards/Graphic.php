<?php
declare(strict_types=1);
namespace Domain\Entity\Cards;

use Core\Domain\Entity;
use Library\HTML\TraitString;

class Graphic extends Entity {
	use TraitString;

    public function __get($key)
    {
    	$_method = '_'.$key;
    	if(!property_exists($this, $key) && method_exists($this, $_method)){

    		$this->$key = $this->$_method();
    	} else {
    		return null;
    	}
    	return $this->$key;       
    }

    private function _title(){
    	
    	//Kit déco {{vehicle_fullname}} {{design_name}} {{color_name}}  {{type}}
    	$search = ['{{vehicle_fullname}}', '{{design_name}}', '{{color_name}}', '{{type}}'];
    	$replace = [$this->vehicle_fullname, $this->design_name, $this->color_name, null];
    	return str_replace($search, $replace, $this->full_designation??$this->designation??'');
    }

    private function _url(){
    	// kit-deco-{{family}}-{{brand}}-{{vehicle}}-{{design}}-{{color}}
    	$search = ['{{family}}', '{{brand}}', '{{vehicle}}', '{{design}}', '{{color}}'];
    	$replace = [$this->family_slug, $this->brand_slug, $this->vehicle_slug, $this->design_slug,$this->color_slug];
    	$this->item_slug = $this->slugify(str_replace($search, $replace, $this->item_slug??''));
    	$url = $this->item_slug.'-'.$this->id;
    	return implode('/', array_filter([FQDN, $this->_prefix, $url]));
    }

    
}