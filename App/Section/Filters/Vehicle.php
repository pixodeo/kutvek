<?php
declare(strict_types=1);
namespace App\Section\Filters;

use Core\Component;
use Core\Routing\RouteInterface;
use Exception;

class Vehicle extends Component {
	public function __construct(protected RouteInterface $_route, protected ?int $vehicle_id){

    }
	public function __invoke() {
		try {
			// si on a déjà un vehicule  on doit simplement filter par défaut sur celui-ci
			if($this->vehicle_id !== null){
				return <<<TEXT
				<input name="vehicle" type="checkbox" class="hide" checked value="{$this->vehicle_id}" />
				TEXT;
			}
			$table = $this->_tables['Section'];
			$vehicles = [];			
			$this->_path = dirname(__FILE__,2);
			$this->_view = 'Filters.vehicles';
			return $this->partial(compact('vehicles'));
		} catch(Exception $e){
			throw $e;
		}		
	}


}