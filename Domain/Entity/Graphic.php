<?php
declare(strict_types=1);
namespace Domain\Entity;

use Core\Domain\Entity;

class Graphic extends Entity {
	public int $id;	
	public int $opts = 0;
	public ?int $parent;
	public array $files = [];
	public $vehicle;
	public float $fluo_cost = 0.00;
    public int $fluo_support = 0;
    public string $_locale = 'fr';
    
	public function __construct()
	{
		$this->vehicle = json_decode($this->vehicle);
		$this->attributes = json_decode($this->attributes);
		$opts = array_filter([$this->attributes->opts, $this->attributes->door_stickers, $this->attributes->switch, $this->attributes->hubs_stickers,$this->attributes->mini_plates, $this->attributes->plastics,$this->attributes->rim_sticker]);
		$this->opts = count($opts);

	}

	public function thumbnail(){
		if(!property_exists($this, 'thumbnail')){
			if($this->cover === null): $this->thumbnail = '/img/blank';
			else:
				$dot = strrpos($this->cover, '.');
        		$filename = substr($this->cover, 0, $dot);
        		$ext = substr($this->cover, $dot);
        		$this->thumbnail =  "{$filename}_w96{$ext}";        		
        	endif;
        	return $this->thumbnail;
		}
	}

	public function thumbnail_2x(){
		if(!property_exists($this, 'thumbnail_2x')){
			if($this->cover === null): $this->thumbnail_2x = '/img/blank';
			else:
				$dot = strrpos($this->cover, '.');
        		$filename = substr($this->cover, 0, $dot);
        		$ext = substr($this->cover, $dot);        		
        		$this->thumbnail_2x =  "{$filename}_w96d2x{$ext}";
        	endif;
        	return $this->thumbnail_2x;
		}
	}

	
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



    private function _designation(){
    	if($this->l10n_designation !== null)return $this->l10n_designation;
    	//Kit déco {{vehicle_fullname}} {{design_name}} {{color_name}}  {{type}}
    	$search = ['{{vehicle_fullname}}', '{{design_name}}', '{{color_name}}', '{{type}}'];
    	$replace = [$this->vehicle_fullname, $this->design_name, $this->color_name, null];
    	return str_replace($search, $replace, $this->df_full_designation??'');
    }

    private function _short_desc() {
    	return $this->l10n_short_desc;
    }

    private function _features(){
    	return $this->l10n_features;
    }

    private function _description(){
    	return $this->l10n_description;
    }

    private function _best_rendering(){
    	return '';
    }
}