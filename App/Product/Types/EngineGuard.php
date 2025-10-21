<?php
declare(strict_types=1);
namespace App\Product\Types;
use Core\Routing\{RouteInterface};
use Library\HTML\Form;

class EngineGuard extends Type {	

	public function __construct(protected RouteInterface $_route){
		$this->_path = dirname(__FILE__,2);
		$this->_view = 'engine-guard';
		$this->form = new Form;
	}

	public function __invoke(){ 
		$this->_table = $this->getDomain('Product');
   		$this->_table->setEntity(null);	
		$info = $this->_table->info((int)$this->product->id);
	   	if($info):
	   		foreach($info as $property => $value):
	            $this->product->{$property} = $value;
	        endforeach;
	        $this->product->family_id = 5;
	        $this->product->attributes = json_decode($this->product->attributes);
	   	endif;     
	   	$reinsurance_items = $this->_table->reinsuranceOnCart();
	   	$this->_content = $this->partial(compact('reinsurance_items'));
	   	$this->_path = false;
        $this->_layout = 'graphic-kits';  
		$this->_response->getBody()->write($this->_print());
		return $this->_response;
	}

	public function widgetMillesims()
    {
        $sql = "SELECT CONCAT_WS(' ', v.brand_name, v.name) AS 'name',
   		CASE 
	   		WHEN v_m.finish = '-' AND v_m.begin < YEAR(NOW()) THEN CONCAT_WS('-', v_m.begin, YEAR(NOW())) 
	   		WHEN v_m.finish = '-' AND v_m.begin >= YEAR(NOW()) THEN v_m.begin 
	   		ELSE CONCAT_WS('-',  v_m.begin, v_m.finish) 
   		END AS 'year',
   		v_m.id AS 'year_id',
   		CONCAT_WS('.', v_m.id, p.design, p.color) AS 'sku'
   		FROM item_vehicles i_v
   		LEFT JOIN vehicle_millesims v_m ON v_m.id = i_v.year
   		LEFT JOIN vue_vehicle_2 v ON (v.id = (CASE WHEN i_v.year IS NOT NULL THEN v_m.vehicle ELSE i_v.vehicle END) AND v.l10n = :l10n_id)
   		LEFT JOIN products_old p ON p.id = :id
   		WHERE i_v.item = p.id
   		ORDER BY v.name DESC, v_m.begin DESC;";
   		$this->_table->setEntity('ItemVehicle');
   		$siblings = $this->_table->query($sql, ['id'=>$this->product->id, 'l10n_id'=>$this->getL10nId()]);
   		$this->_table->setEntity(null);
   		foreach ($siblings as $year) {            
            $this->years[$year->year_id] = $year;
        }

        $input = $this->form->select(
            'item[millesim]',
            array(
                'label' => 'Véhicule',
   				'id' => 'vehicle-version',
   				'i18n' => 'choose-vehicle',
                'attributes' => array(
                    'class="field-input select onchange"', 
                    'form="addToCart"', 
                    'data-target="kit-type"', 
                    'data-ctrl="product.sku"',
                    'required'),
                'values' => $this->years,
                'keys' => ['designation', 'year_id'],
                'dataset' => ['sku', 'year']
            )
        );
        return $input;
    }

    public function engineGuardVersion(): string{
        if((int)$this->product->attributes->skid_plates_versions > 0) {
            $versions = ['Version 1', 'Version 2'];
            $input = $this->form->select(
            'item[skid_plates_version]',
            array(
                'label' => 'Version du sabot',
                'id'    => 'skid-plate-version',
                'placeholder' => 'Choisir',
                'attributes' => array('class="field-input select"', 'form="addToCart"', 'required'),
                'values' => $versions                           
            )
            );
            return <<<EOT
            <div class="skid-plates-version">
                <p data-i18n="skid-plates-version">Merci de sélectionner la version de votre sabot moteur. Plus d'info sur : <a href="https://www.axp-racing.com/fr/moto/sabot-adventure/" class="link" target="_blank">le site AXP Racing</a></p>
                <br>
                {$input}
            </div>
            EOT;
        }
        return 'eee';
    }    
}