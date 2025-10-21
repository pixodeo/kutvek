<?php
declare(strict_types=1);
namespace App\SaddleCover;
use App\AppAction;

use Domain\Table\SaddleCover;
use Library\HTML\TraitSanitize;
use App\Component\Product\Widget\CrossSelling;

final class Read extends AppAction {
	use TraitSanitize;

	protected SaddleCover $_table;
	public false|object $product;

	public function __invoke(int $id){
		$queries = $this->getRequest()->getQueryParams();
		$this->_table = new SaddleCover($this->_setDb());
		$this->_table->setRoute($this->_route);		
		$this->product = $this->_table->read($id);
		$this->_path = dirname(__FILE__);
		$this->_view = 'read';
		$this->meta_description = '';
		$this->meta_title =  $this->product->meta_title; 
		$reinsurance_items = $this->_table->reinsuranceOnCart();
		$x_sell = new CrossSelling;
        $x_sell->setI18n($this->getI18n()); 
		$this->_content = $this->partial(compact('queries', 'reinsurance_items', 'x_sell'));
		$this->_path = false;
		$this->_layout = 'saddle-covers';
		$this->_response->getBody()->write($this->_print());		
		return $this->_response;
	}

	public function brands(){
		$brands = array_column($this->product->suitable, 'brand_name', 'brand_id');
		return $brands;
	}

	public function vehicles(){
		$vehicles = [];
		foreach ($this->product->suitable as  $value) {
			if(array_key_exists($value->id, $vehicles)) continue;
			$vehicles[$value->id] = (object)[
				'id' => $value->id,
				'name' => $value->name,
				'brand_id' => $value->brand_id
			];
		}
		return $vehicles;
	}

	public function foam(): string{
		$inputs = [];
		if($this->product->foam > 0):
			foreach ($this->product->optFoam as $opt) {
				$inputs[] = (object)[
                	'label' => $opt->label, 
                	'id'=>'opt-'.$opt->id, 
                	'value'=> $opt->price, 
                	'name'=>'item[price][foam]',
                	'class' => 'onchange cost qty-depend',
                	'form' => 'addToCart',
                	'checked' => $opt->price > 0 ? false : true,
                	'dataset' => ['name'=>$opt->name, 'id'=>$opt->id, 'ctrl' => 'saddles.foam']
                ];
			}
			return $this->form->radios('Mousse confort', [                        
                'radios' => $inputs,
                'attributes' => ['form="addToCart"'],      
                'dataset' => ['i18n'=>'comfort-foam'],                  
                'class' => ['column']
            ]);	                
		endif;
		return '';
	}
}