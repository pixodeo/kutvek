<?php
declare(strict_types=1);
namespace App\Product\Types\Decorators;

use App\Product\Types\Decorators\Decorator;
use Domain\Entity\Option AS OptionEntity;
use Core\Component;
use Core\Routing\{RouteInterface};
use Domain\Table\Option; 

final class Options extends Decorator  {

	private array $_colors = [];
	private array $_options = [];
	private array $_activatedOptions = [];
	private array $_tabs = [];
	private array $_tabsLinks = [];
	private array $_miniPlatesStickers = [];
	private array $_wheelHubStickers = [];
	private array $_optionnalStickers = [];

	public function __construct(protected RouteInterface $_route, protected Component $component){
		$this->_tables['options'] = new Option($this->_setDb());
		$this->_tables['options']->setRoute($_route);
	} 

	/** Récupère les détails des options présents sur un produit */
	public function options(int $fluo = 0)
	{
		$this->_colors = $this->_tables['options']->plateColors();
		$attributes = $this->productAttributes();  
		$inputs = [];
		$options = $attributes->opts;  
		switch ($options) {
			case '1':
			$ids = array(9);
			break;
			case '2':
			$ids = array(8);
			break;
			case '3':
			$ids = array(9, 11);
			break;
			default:
			$ids = array();
			break;
		}

		if ((int)$attributes->switch > 0) $ids[] = 23;
		if ((int)$attributes->seat_cover > 0) $ids[] = $attributes->seat_cover;
		if ((int)$attributes->rim_sticker > 0) $ids[] = 71;
		if ((int)$attributes->door_stickers > 0) $ids[] = $attributes->door_stickers;
		if ((int)$attributes->hubs_stickers > 0 || (int)$attributes->mini_plates > 0):
		 	$vehicle = $this->vehicleInfo();
		 	$this->_optionnalStickers = $this->_tables['options']->optionnalStickers($vehicle->family->id);;
		endif;

		$this->_options = $this->_tables['options']->options($ids);
		$data = print_r($this->_options,true);

		foreach($this->_options as $option):
			$method = $option->input_name === 'sponsor' ? '_plate_sponsor' :  "_{$option->input_name}";
			if(method_exists($this, $method)):
				$inputs[] = $this->{$method}($option);
				$this->_activatedOptions[] = $option->label_id;	
			endif;        	
		endforeach;
		$print = print_r($this->_activatedOptions,true);		
		$inputs = implode('',$inputs);		
		return <<<TEXT
		<div class="bloc-header" data-i18n="options">Options</div>
		<div class="options">{$inputs}</div>
		TEXT;        
	}

	private function _plate_sponsor(OptionEntity $option){
		$params = [
			'label' => (object)['attributes' => ['class="click"', 'data-tab="'. $option->label_id .'-tab"',    'data-ctrl="option.popin"','data-modal="opt-modal"'],'text'=>$option->label],
			'id' => $option->label_id,    		
			'attributes' => ['class="radio-opt cost"','form="addToCart"'],
			'value' => $option->price
		];
		$inputs = [$this->_form()->radio('item[item_price][opts]', $params, false)];
		$params = [
			'label' => '<i class="material-symbols-rounded">&#xe5cd;</i>',    		
			'data' => $option,
			'dataset' => ['label_id'],
			'id' => $option->label_id . '-' . $option->option_id,
			'attributes' => ['class="cost onchange"', 'hidden', 'data-ctrl="option.update"','form="addToCart"'],
			'value' => 0
		];
		$inputs[] = $this->_form()->radio('item[item_price][opts]', $params, false);
		$inputs = implode('', $inputs);
		$this->_tabs[] = match($option->label_id)
		{
			'plate' => <<<TEXT
				<div class="tab_content" id="plate-tab">
				<div class="grid">
					<div class="col col-m-6">{$this->_inputName()}</div>								
					<div class="col col-m-6">{$this->_inputNumber()}</div>						
				</div>
				<div class="grid">								
					<div class="col col-m-6">{$this->_inputNameTypo()}</div>
					<div class="col col-m-6">{$this->_inputNumberTypo()}</div>						
				</div>
				<div class="grid">
					<div class="col col-m-6">{$this->_inputNumberColors()}</div>
					<div class="col col-m-6">{$this->_inputPlateColors()}</div>						
				</div>	
				<hr>
				<div class="grid">
					<div class="col col-m-6 col-logo">{$this->_inputRaceLogo()}</div>
				</div>										
			</div>
			TEXT,
			'plate-sponsor' => <<<TEXT
				<div class="tab_content" id="plate-sponsor-tab">{$this->_sponsors()}</div>
			TEXT,
			'sponsor' => <<<TEXT
				<div class="tab_content" id="sponsor-tab">{$this->_sponsors()}</div>
			TEXT,
			'switch-color' => <<<TEXT
				<div class="tab_content" id="switch-color-tab">switch couleur !</div>
			TEXT,
			default => ''
 		};
 		$this->_tabsLinks[] = match($option->label_id){
 			'plate' => '<li class="col-4"><a href="#plate-tab">Nom + numéros</a></li>',
 			'plate-sponsor' => 	'<li class="col-4"><a href="#plate-sponsor-tab">Sponsors</a></li>',
 			'sponsor' => '<li class="col-4"><a href="#sponsor-tab">Sponsors</a></li>', 			
 			default => ''
 		};
		return <<<TEXT
		<div class="option radio">
		{$inputs}
		</div>
		TEXT;
	}

	private function _switch_color(OptionEntity $option){
		$params = [
			'label' => (object)['attributes' => ['class="click"', 'data-tab="'. $option->label_id .'-tab"',    'data-ctrl="option.popin"','data-modal="opt-modal"'],'text'=>$option->label],
			'id' => $option->label_id,
			'attributes' => ['class="radio-opt cost"','form="addToCart"'],
			'value' => $option->price
		];
		$inputs = [$this->_form()->radio('item[item_price][switch_color]', $params, false)];
		$params = [
			'label' => '<i class="material-symbols-rounded">&#xe5cd;</i>',    		
			'data' => $option,
			'dataset' => ['label_id'],
			'id' => $option->label_id . '-' . $option->option_id,
			'attributes' => ['class="cost onchange"', 'hidden','data-ctrl="option.update"','form="addToCart"'],
			'value' => 0
		];
		$inputs[] = $this->_form()->radio('item[item_price][switch_color]', $params, false);
		$inputs = implode('', $inputs);
		$this->_tabsLinks[] = '<li class="col-4"><a href="#switch-color-tab">Switch couleur</a></li>';
		$this->_tabs[] = <<<TEXT
			<div class="tab_content" id="switch-color-tab">switch couleur !</div>
		TEXT;
		return <<<TEXT
		<div class="option radio">
		{$inputs}
		</div>
		TEXT;		
	}

	private function _rim_sticker(OptionEntity $option) {
		$params = [
			'label' => $option->label,
			'id' => $option->label_id,
			'attributes' => ['class="cost qty-depend onchange"', 'data-ctrl="option.rimStickers"','form="addToCart"','data-name="' . $option->name . '"', 'data-id="'. $option->option_id . '"'],
			'value' => $option->price
		];

		$inputs = [$this->_form()->radio('item[item_price][rim_sticker]', $params, false)];
		$params = [
			'label' => '<i class="material-symbols-rounded">&#xe5cd;</i>',    		
			'data' => $option,
			'dataset' => ['label_id'],
			'id' => $option->label_id . '-' . $option->option_id,
			'attributes' => ['class="cost qty-depend onchange"','hidden', 'data-ctrl="option.rimStickers"','form="addToCart"'],
			'value' => 0
		];
		$inputs[] = $this->_form()->radio('item[item_price][rim_sticker]', $params, false);
		$inputs[] = <<<INPUT
			<input type="hidden" name="item[item_custom][options][rim_stickers][id]" id="rim-sticker-id" value="" form="addToCart" />
			<input type="hidden" name="item[item_custom][options][rim_stickers][name]" id="rim-sticker-name" value="" form="addToCart" />
		INPUT;
		$inputs = implode('', $inputs);
		return <<<TEXT
		<div class="option checkbox">
		{$inputs}
		</div>
		TEXT;
	}	

	private function _door_stickers(OptionEntity $option) {		
		$params = [
			'label' => $option->label,
			'id' => $option->label_id,
			'attributes' => ['class="cost qty-depend onchange"', 'data-ctrl="option.doorStickers"','form="addToCart"', 'data-name="' . $option->name . '"', 'data-id="'. $option->option_id . '"'],			
			'value' => $option->price
		];

		$inputs = [$this->_form()->radio('item[item_price][door_stickers]', $params, false)];
		$params = [
			'label' => '<i class="material-symbols-rounded">&#xe5cd;</i>',    		
			'data' => $option,			
			'id' => $option->label_id . '-' . $option->option_id,			
			'attributes' => ['class="cost qty-depend onchange"','hidden', 'data-ctrl="option.doorStickers"','form="addToCart"'],
			'value' => 0
		];
		$inputs[] = $this->_form()->radio('item[item_price][door_stickers]', $params, false);
		$inputs[] = <<<INPUT
			<input type="hidden" name="item[item_custom][options][door_stickers][id]" id="door-sticker-id" value="" form="addToCart" />
			<input type="hidden" name="item[item_custom][options][door_stickers][name]" id="door-sticker-name" value="" form="addToCart" />
		INPUT;
		$inputs = implode('', $inputs);
		return <<<TEXT
		<div class="option checkbox">
		{$inputs}
		</div>
		TEXT;
	}

	private function _inputNumberColors(){
		$default = '';
		$inputs = [];
		$colors = array_filter($this->_colors,fn($c)=> $c->is_for_number > 0);
		foreach($colors AS $color):
			if($color->is_number_default > 0): 
				$default = <<<TEXT
				<div class="input-color grid">
				<span class="label col col-m-5">Couleur du nom et numéro</span>					
				<div class="col col-m-4 click" data-picker="number-colors" data-ctrl="option.picker">
					<input type="color" id="number-color" name="item[item_custom][options][plate][number_color]" class="col-6 col-m-3 click"  data-picker="number-colors"data-picker="number-colors" data-ctrl="option.picker"  value="{$color->hexa_color}" form="addToCart" />
					<label class="col-6 col-m-1" data-color="{$color->text_color}" for="number-color">{$color->designation}</label>
					<i class="material-symbols-rounded">&#xe5c5;</i>	
				</div>
				
				</div>
				TEXT;			
			endif;
			$input = <<<TEXT
				<div class="color">				
				<input type="color" id="pn-{$color->id}" class="click"  data-ctrl="option.pickColor"  data-input="number-color" value="{$color->hexa_color}" />
				<label data-color="{$color->text_color}" for="pn-{$color->id}">{$color->designation}</label>
				</div>
				TEXT;
			$inputs[] = $input;
		endforeach;
		$i = implode('',$inputs);
		$modal = <<<TEXT
				<div class="picker" id="number-colors">
				<a href="#number-colors" class="close click" data-ctrl="utils.picker"><i class="material-symbols-rounded">&#xe5cd;</i></a>
				<p><span>Sélectionnez la couleur du nom et du numéro</span></p>
				<div  class="picker-colors">							
				{$i}
				</div>
				</div>
				TEXT;
		return $default . $modal;
	}

	private function _inputPlateColors(){
		$default = '';
		$inputs = [];
		$colors = array_filter($this->_colors,fn($c)=> $c->is_for_plate > 0);
		foreach($colors AS $color):
			if($color->is_plate_default > 0): 
				$default = <<<TEXT
				<div class="input-color grid">
					<span class="label col col-m-5">Couleur des plaques</span>				
					<div class="col col-m-4 click" data-picker="plate-colors" data-ctrl="option.picker">
						<input type="color" id="plate-color" name="item[item_custom][options][plate][plate_color]" form="addToCart"class="col-6 col-m-3 click" data-picker="plate-colors" data-ctrl="option.picker"  value="{$color->hexa_color}" />
						<label class="col-6 col-m-1" data-color="{$color->text_color}" for="plate-color">{$color->designation}</label>
						<i class="material-symbols-rounded">&#xe5c5;</i>	
					</div>				
				</div>
				TEXT;			
			endif;
			$input = <<<TEXT
				<div class="color">				
				<input type="color" id="pn-{$color->id}" class="click"  data-ctrl="option.pickColor"  data-input="plate-color" value="{$color->hexa_color}" />
				<label data-color="{$color->text_color}" for="pn-{$color->id}">{$color->designation}</label>
				</div>
				TEXT;
			$inputs[] = $input;
		endforeach;
		$i = implode('',$inputs);
		$modal = <<<TEXT
				<div class="picker" id="plate-colors">
				<a href="#plate-colors" class="close click" data-ctrl="utils.picker"><i class="material-symbols-rounded">&#xe5cd;</i></a>
				<p><span>Sélectionnez la couleur du fond de plaques</span></p>
				<div  class="picker-colors">							
				{$i}
				</div>
				</div>
				TEXT;
		return $default . $modal;
	}	

	private function _inputName(){
		return <<<TEXT
			<div class="input grid">
			<label class="col col-m-5" for="name">Nom</label>
			<input class="col col-m-4" id="name" type="text" name="item[item_custom][options][plate][name]" form="addToCart" />
			</div>
		TEXT;	
	}

	public function widgetWheelHubsStickers(){
	 	$attributes = $this->productAttributes();
	 	
        if ((int)$attributes->hubs_stickers < 1) return '';      
        $stickers = array_filter($this->_optionnalStickers, fn ($opt) => $opt->opt_type === 'hubs_stickers' );
        $input = '<div class="bloc-header"></div>';
       
        $input .= $this->_form()->select(
            'item[item_price][hubs_stickers]', [
                'id' => 'hubs-stickers',
                'label' => 'Stickers de moyeux',                                
                'attributes' => ['class="onchange field-input select cost"', 'form="addToCart"', 'data-ctrl="option.hubStickers"'],
                'values' => $stickers, 
                'keys' => ['text', 'price'],
                'dataset' => ['data-name' => 'name', 'data-id' => 'id']
                ]
            );
        $inputs = [$input];
        $inputs[] = $this->_wheelHubsInfo();

        return implode('', $inputs);
    }
    private function _wheelHubsInfo(){
    	$attributes = $this->productAttributes();
    	if((int)$attributes->hubs_stickers < 1) return ''; 
    	 $stickers = array_filter($this->_optionnalStickers, fn ($opt) => $opt->opt_type === 'hubs_stickers' );
        $id = $stickers[0]->id;
        $name = $stickers[0]->name;
        return <<<EOT
        <input type="hidden" name="item[item_custom][options][hubs_stickers][id]" id="hubs-stickers-id" value="{$id}" form="addToCart" />
        <input type="hidden" name="item[item_custom][options][hubs_stickers][name]" id="hubs-stickers-name" value="{$name}" form="addToCart" />
        EOT;
    }

    public function widgetMiniPlates(){
        $attributes = $this->productAttributes();
        $vehicle = $this->vehicleInfo();
        if ((int)$attributes->mini_plates < 1) return '';       
        $stickers = array_filter($this->_optionnalStickers, fn ($opt) => $opt->opt_type === 'mini_plates' );
        $input = '<div class="bloc-header"></div>';       
        $input .= $this->_form()->select(
            'item[item_price][mini_plates]', [
                'id' => 'mini-plates',
                'label' => 'Mini plaques',                
                'attributes' => ['class="field-input select onchange cost"', 'form="addToCart"', 'data-ctrl="option.miniPlates"'],
                'values' => $stickers,
                'keys' => ['text', 'price'],
                'dataset' => ['data-name' => 'name', 'data-id' => 'id']
                ]
            );
       	$inputs = [$input];
       	$inputs[] = $this->_miniPlatesInfo();
        return implode('', $inputs);        
    }

    private function _miniPlatesInfo(){
    		$attributes = $this->productAttributes();
    		if((int)$attributes->mini_plates < 1) return '';
    		$stickers = array_filter($this->_optionnalStickers, fn ($opt) => $opt->opt_type === 'mini_plates' ); 
            $id = $stickers[0]->id;
            $name = $stickers[0]->name;
            return <<<EOT
            <input type="hidden" name="item[item_custom][options][mini_plates][id]" id="mini-plates-id" value="{$id}"  form="addToCart" />
            <input type="hidden" name="item[item_custom][options][mini_plates][name]" id="mini-plates-name" value="{$name}"  form="addToCart" />
            EOT;
    }    

	private function _inputNumber(){
		return <<<TEXT
			<div class="input grid">
			<label class="col col-m-5" for="number">Numéro</label>
			<input class="col col-m-4" id="number" type="text" name="item[item_custom][options][plate][number]" form="addToCart" />
			</div>
		TEXT;
	}

	private function _inputNameTypo(){
		$dir =  WEBROOT. DS . 'img' . DS . 'typo' .DS;
        $web_path = '/img' . DS . 'typo' .DS;
        $typos = glob($dir . "name*.png", GLOB_BRACE);
        $print = print_r($typos, true);        
		$inputs = [];
		// 1ère typo 
		$first_typo = $typos[0];
		$name = basename($first_typo);
		$uri = DOMAIN . $web_path . $name;
		$default = <<<TEXT
				<div class="input-typo grid click" data-picker="name-typos" data-ctrl="option.typoPicker">
					<span class="label col col-m-5">Typo du nom</span>				
					<input type="hidden" id="name-typo" name="item[item_custom][options][plate][name_typo]" form="addToCart"  value="{$uri}" />
					<div class="col col-m-4 click"   data-picker="name-typos" data-ctrl="option.typoPicker">
						<img   src="{$uri}" alt="" />
						<i class="material-symbols-rounded">&#xe5c5;</i>	
					</div>
				</div>
				TEXT;
		foreach($typos AS $typo):
			$name = basename($typo);
			$uri = DOMAIN . $web_path . $name;		
			$input = <<<TEXT
				<div class="col col-m-6 typo click" data-ctrl="option.setTypo" data-input="name-typo">		
				<img src="{$uri}" alt="" />
				</div>
				TEXT;
			$inputs[] = $input;
		endforeach;
		$i = implode('',$inputs);
		$modal = <<<TEXT
				<div class="picker" id="name-typos">
				<a href="#name-typos" class="close click" data-ctrl="utils.picker"><i class="material-symbols-rounded">&#xe5cd;</i></a>
				<p><span>Sélectionnez la typo du nom</span></p>
				<div  class="picker-typos grid">							
				{$i}
				</div>
				</div>
				TEXT;
		return $default . $modal;
	}

	private function _inputNumberTypo(){
		$dir =  WEBROOT. DS . 'img' . DS . 'typo' .DS;
        $web_path = '/img' . DS . 'typo' .DS;
        $typos = glob($dir . "num*.png", GLOB_BRACE);
        $print = print_r($typos, true);	
		$inputs = [];
		// 1ère typo 
		$first_typo = $typos[0];
		$name = basename($first_typo);
		$uri = DOMAIN . $web_path . $name;
		$default = <<<TEXT
			<div class="input-typo grid click" data-picker="number-typos" data-ctrl="option.typoPicker">
				<span class="label col col-m-5">Typo du numéro</span>				
				<input type="hidden" id="number-typo" name="item[item_custom][options][plate][number_typo]" form="addToCart" value="{$uri}" />
				<div class="col col-m-4 click"   data-picker="number-typos" data-ctrl="option.typoPicker">
					<img   src="{$uri}" alt="" />
					<i class="material-symbols-rounded">&#xe5c5;</i>	
				</div>
			</div>
			TEXT;
		foreach($typos AS $typo):
			$name = basename($typo);
			$uri = DOMAIN . $web_path . $name;		
			$input = <<<TEXT
				<div class="col col-m-6 typo click" data-ctrl="option.setTypo" data-input="number-typo">		
				<img src="{$uri}" alt="" />
				</div>
				TEXT;
			$inputs[] = $input;
		endforeach;
		$i = implode('',$inputs);
		$modal = <<<TEXT
				<div class="picker" id="number-typos">
				<a href="#number-typos" class="close click" data-ctrl="utils.picker"><i class="material-symbols-rounded">&#xe5cd;</i></a>
				<p><span>Sélectionnez la typo du numéro</span></p>
				<div  class="picker-typos grid">							
				{$i}
				</div>
				</div>
				TEXT;
		return $default . $modal;
	}

	private function _inputRaceLogo(){
		$dir =  WEBROOT. DS . 'img' . DS . 'federations-logos' .DS;
        $web_path = '/img' . DS . 'federations-logos' .DS;
        $logos = glob($dir . "*", GLOB_BRACE);
        $print = print_r($logos, true);	

		
		$l10n = $this->getL10nCode();
		$uri = DOMAIN . "/img/no-logo.svg";
		$input = <<<TEXT
				<div class="col col-m-6 logo click" data-ctrl="option.setLogo" data-input="logo-federation">		
				<img src="{$uri}" alt="" />
				</div>
				TEXT;
		$inputs = [$input];
		$default = <<<TEXT
				<div class="input-logo grid click" data-picker="logos-federations" data-ctrl="option.picker">
					<span class="label col col-m-5">Logo fédération</span>				
					<input type="hidden" id="logo-federation" name="item[item_custom][options][plate][logo]" form="addToCart" value="{$uri}" />
					<div class="col col-m-4 click"   data-picker="logos-federations" data-ctrl="option.picker">
						<img   src="{$uri}" alt="" />
						<i class="material-symbols-rounded">&#xe5c5;</i>	
					</div>
				</div>
				TEXT;
		foreach($logos AS $logo):
			$name = basename($logo);
			$uri = DOMAIN . $web_path . $name;		
			$input = <<<TEXT
				<div class="col col-m-6 logo click" data-ctrl="option.setLogo" data-input="logo-federation">		
				<img src="{$uri}" alt="" />
				</div>
				TEXT;
			$inputs[] = $input;
		endforeach;
		$i = implode('',$inputs);
		$modal = <<<TEXT
				<div class="picker" id="logos-federations">
					<a href="#logos-federations" class="close click" data-ctrl="utils.picker"><i class="material-symbols-rounded">&#xe5cd;</i></a>
					<p><span>Choisissez votre logo</span></p>
					<div  class="picker-typos grid">{$i}</div>
				</div>
				TEXT;
		return $default . $modal;
	}

	private function _switchColor(){}

	private function _sponsors(){
		// On affiche les sponsors que si option cochée
		$vehicle = $this->vehicleInfo();
		$template = DOMAIN . '/img/sponsors/'.$vehicle->sponsors->template;
		$inputs = [];
		for ($i=1; $i <= $vehicle->sponsors->quota; $i++):
			$input = <<<TEXT
				<div class="sponsor col-m-4">
					<span class="place">{$i}</span>
					<input type="hidden" name="item[item_custom][options][sponsor][{$i}][place]" value="{$i}" form="addToCart"/>	
					<input class="field-input text" type="text" name="item[item_custom][options][sponsor][{$i}][text]" data-i18n="sponsor-placeholder" placeholder="Nom du sponsor" form="addToCart"/>
					<input class="file onchange sponsor-file" type="file" id="spo-{$i}" data-place="{$i}" data-ctrl="option.uploadSponsor" name="item[item_custom][options][sponsor][{$i}][file]" form="addToCart"/>
					<label for="spo-{$i}">
						<i class="icon material-symbols-rounded">&#xe9fc;</i>
					</label>
					<span class="filename"></span>
				</div>
			TEXT;
			$inputs[] = $input;
		endfor;
		$print = print_r($vehicle, true);
		$inputs =implode('',$inputs);
		return <<<TEXT
			<div class="sponsor-template">
				<img src="{$template}" alt="" />
			</div>
			<div class="grid sponsors">
				{$inputs}
			</div>			
		TEXT;
	}

	public function popinOptions(){	
		$links = implode('', $this->_tabsLinks);
		$tabs = implode('', $this->_tabs);
		return <<<TEXT
		<aside class="modal" id="opt-modal">
			<div class="popup fullscreen">
				<header class="close">
					<p class="title" data-i18n="effect-finish-desc"></p>
					<a href="#opt-modal" class="click" data-modal="opt-modal" data-ctrl="modal.popin"><span class="icon material-symbols-rounded close-popup">close</span></a>
				</header>
				<div>
					<ul class="grid tabs " id="item-info">{$links}</ul>
					<div class="tabs_content">{$tabs}</div>
				</div>
			</div>
		</aside>
		TEXT;
	}


	public function seatCover(){
		return $this->component->seatCover();
	}
}