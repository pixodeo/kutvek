<?php
declare(strict_types=1);
namespace App\Section\Filters;

use Core\Component;
use Exception;

class Model extends Component {

	public function __invoke() {
		try {
			$table = $this->_tables['Section'];
			$models = $table->models();		
			
			$this->_path = dirname(__FILE__,2);
			$this->_view = 'Filters.models';
			return $this->partial(compact('models'));
		} catch(Exception $e){
			throw $e;
		}		
	}
}