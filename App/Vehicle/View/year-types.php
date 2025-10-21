<?php 
$opts = [
	'id' => 'type-id',
	'label'=>'Type de kit', 
	'placeholder' => 'Choisir',         
	'attributes' => [               
		'class="field-input select cost qty-depend"',
		'data-ctrl="graphics.type"',
		'form="addToCart"',
		'required',                
		'data-i18n="kit_type"'
	],
	'values' => $types,
	'keys'=> ['name', 'price'],
	'dataset' => ['id', 'year_id'],
	'class' => ['column']
];
$select = $this->form->select('item[item_price][product]',[
	'id' => 'type-id',
	'label'=>'Type de kit', 
	'placeholder' => 'Choisir',         
	'attributes' => [               
		'class="field-input select cost qty-depend"',
		'data-ctrl="graphics.type"',
		'form="addToCart"',
		'required',                
		'data-i18n="kit_type"'
	],
	'values' => $types,
			//'opt_class' => ['hide'],
	'keys'=> ['name', 'price'],
	'dataset' => ['id', 'year_id'],
	'class' => ['column']
]);
return $select;
?>