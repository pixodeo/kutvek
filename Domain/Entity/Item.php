<?php
declare(strict_types=1);
namespace Domain\Entity;

use Core\Domain\Entity;

#[\AllowDynamicProperties]
class Item extends Entity {
	
	public mixed $item_price = '{}';
	public mixed $switch;
	public mixed $sponsor;
	public mixed $race;
	
	public function __construct()
	{
		
		$this->item_price = json_decode($this->item_price);
		if($this->race !== null) $this->race = json_decode($this->race);
		if($this->sponsor !== null) $this->sponsor = json_decode($this->sponsor);
		if($this->switch !== null) $this->switch = json_decode($this->switch);
	}	

	public function options(){
		if(!property_exists($this, 'options')):
			$sku = $this->sku ? explode('.', $this->sku) : [];
			$design = count($sku) === 3 ? $sku[1] : 0;
			$options = [];
			if((int)$design === 439) 	$options[] = '<img class="option" src="/img/pictos/options/custom.png" alt="" />';
			if($this->race !== null)	$options[] = '<img class="option" src="/img/pictos/options/race.png" alt="" />';
			if($this->switch !== null)  $options[] = '<img class="option" src="/img/pictos/options/switch.png" alt="" />';
			if($this->sponsor !== null) $options[] = '<img class="option" src="/img/pictos/options/logo.png" alt="" />';
			/*if($this->premium !== null) $options[] = '<img class="option" src="/img/pictos/options/premium.png" alt="' . $this->premium->name .'"  title="' . $this->premium->name .'" />';	
			if(count($options) > 0) $this->suggest = 3;
			if($this->item_category === 9) $this->suggest = 16;*/
		$this->options =  implode('', $options);
		endif;
		return $this->options;
	}

	public function setData(array $data) {
		$this->_data = $data;
	}


	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}

	protected function _designation(){		
		if($this->name !== null) return $this->name;
		return '';
		/*$search = ['{{type}}', '{{design_name}}', '{{color_name}}', '{{vehicle_fullname}}'];
		$replace = [null, $this->design_name, $this->color_name, $this->vehicle_fullname];
		return str_replace($search, $replace, $this->df_full_designation ?? '');*/
	}


	
}