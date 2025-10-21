<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;


class SaddleCoverExport extends Entity {
	public $full_designation;
	public $description;
	public $item_slug;
	public $item;
	public $l10n;	
	public $vehicle_fullname = null;
	public $saddle_type_name = null;

	public function __construct()
	{
		
	}

	public function __get($key) {		
		$method = "_{$key}";
		if(!method_exists($this, $method)) return '';
		$this->{$key} = $this->{$method}();		
		return $this->{$key};
	}

	public function content(){		
		if($this->full_designation === null) :
		$search = ['{{type}}', '{{design_name}}', '{{color_name}}', '{{vehicle_fullname}}'];
			if($this->vehicle_fullname === null):
				$v = implode(' ', [$this->family_name,$this->brand_name]);			
			endif;
			$replace = [$this->saddle_type_name, $this->design_name, $this->color_name, $this->vehicle_fullname ?? $v ?? null];
			$designation =  str_replace($search, $replace, $this->df_full_designation ?? '');
			$this->full_designation = ucwords($designation);
		endif;
		if($this->item_slug === null) $this->item_slug = $this->slugify($this->full_designation);
		unset($this->vehicle_fullname);
		unset($this->tax_rate);
		unset($this->brands);
		unset($this->brand_name);
		unset($this->family_name);
		unset($this->design_id);
		unset($this->design_name);
		unset($this->color_id);
		unset($this->color_name);
		unset($this->saddle_type_name);
		unset($this->saddle_type_id);
		unset($this->df_full_designation);

	}
	protected function slugify(?string $slug = null):?string {
		if($slug === null) return null;
		$translit = \Transliterator::createFromRules("::Latin-ASCII; ::Lower; [^[:L:][:N:]]+ > '-';");
        $slug = trim($translit->transliterate($slug), '-');  
        return $slug;
	}
}