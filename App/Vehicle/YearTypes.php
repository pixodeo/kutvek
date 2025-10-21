<?php
declare(strict_types=1);
namespace App\Vehicle;

use App\AppAction;
use Domain\Table\Vehicle;
use Library\HTML\Form;

final class YearTypes extends AppAction {
	private Vehicle $_table;

	public function __invoke(int $year_id)
	{
		$this->_table = new Vehicle($this->_setDb());
		$this->_table->setRoute($this->_route);
		$this->form = new Form;
		$year_types = $this->_table->yearTypes($year_id);
		//$this->_path = dirname(__FILE__);
		//$this->_view = 'year-types';
		$opts = [
			'id' => 'type-id',
			'label'=>'Type de kit', 
			'placeholder' => 'Choisir',         
			'attributes' => [               
				'class="field-input select cost qty-depend onchange"',
				'data-ctrl="graphics.type"',
				'form="addToCart"',
				'required',                
				'data-i18n="kit_type"'
			],
			'values' => $year_types,
			'keys'=> ['label', 'price'],
			'dataset' => ['type_id', 'year_id'],
			'class' => ['column']
		];
		$select = $this->form->select('item[item_price][product]',$opts);
		$this->_response->getBody()->write($select);
		return $this->_response;
	}
}