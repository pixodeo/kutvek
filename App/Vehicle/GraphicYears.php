<?php
declare(strict_types=1);
namespace App\Vehicle;


use Core\Action;
use Domain\Table\Graphic;
use Library\HTML\Form;

final class GraphicYears extends Action {
	private Graphic $_table;
	public Form $form;
	private array $_years = [];

	public function __invoke(int $id)
	{
		$this->_table = new Graphic($this->_setDb(), null);
		$this->_table->setRoute($this->_route);
		$queries = $this->getRequest()->getQueryParams();
		$sku = $queries['sku'];
		$data = $this->_table->graphicYears($id, $sku);
		foreach ($data as $year) {
			$year->uri = $this->url('vehicle.yearKitTypes', ['fqdn'=>1,  'queries' => ['id' => $year->year_id]]);
            $this->_years[$year->year_id] = $year;
        }
		$this->form = new Form;
        $select = $this->form->select(
            'item[millesim]',
            array(
                'label' => 'Année',
                'id'    => 'millesim',
                'attributes' => array('class="field-input select onchange"', 'form="addToCart"', 'data-target="kit-type"', 'data-ctrl="graphics.year"', 'required'),
                'values' => $this->_years,
                'keys' => ['year', 'year_id'],
                'dataset' => ['sku', 'year', 'uri']
            )
        );
        $first = $data[0]->year_id;
        $types = array_filter($data, fn($n) => $n->year_id === $first); 
        //$this->_defaultKitType = count($types) > 0 ? $types[0] : null;    
        $type = $this->form->select(
            'item[item_price][product]',
            array(
                'label' => 'Type de kit 2',                
                'id' => 'kit-type',
                'values' => $types, 
                'keys' => ['label', 'price'],              
                'attributes' => array('class="field-input select onchange cost qty-depend"', 'data-ctrl="graphics.price"', 'form="addToCart"', 'required'),

                'dataset'=> ['id', 'designation']
            )
        );       
        $typesp = print_r($types, true);
        $pre = <<<PRE
        '<pre>
        	{$typesp}
        </pre>'
        PRE;
        $this->_response->getBody()->write($select.$type);
		return $this->_response;
	}
}