<?php
declare(strict_types=1);
namespace App\Vehicle;

use App\AppAction;
use Domain\Table\Graphic;

use Library\HTML\Form;

final class YearKitTypes extends AppAction {
	private Graphic $_table;

	public function __invoke(int $id)
	{
		$this->_table = new Graphic($this->_setDb(), null);
		$this->_table->setRoute($this->_route);
		$this->form = new Form;
		$year_types = $this->_table->yearKitTypes($id);
		$p = print_r($year_types,true);
		$txt = <<<EOT
		<pre>
			{$p}
		</pre>
		EOT;	

        $input = $this->form->select(
            'item[item_price][product]',
            array(
                'label' => 'Type de kit',                
                'id' => 'kit-type',
                'values' => $year_types, 
                'keys' => ['label', 'price'],              
                'attributes' => array('class="field-input select onchange cost qty-depend"', 'data-ctrl="graphics.price"', 'form="addToCart"', 'required'),
                'dataset'=> ['id', 'designation']
            )
        );
        
		$this->_response->getBody()->write($input);
		return $this->_response;
	}
}