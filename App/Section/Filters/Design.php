<?php
declare(strict_types=1);
namespace App\Section\Filters;

use Core\Component;
use Exception;

class Design extends Component{

	public function __invoke(){
		try {
			$table = $this->_tables['Section'];
			$designs = $table->designs();
			$this->_path = dirname(__FILE__,2);
			$this->_view = 'Filters.designs';
			return $this->partial(compact('designs'));
		} catch(Exception $e){
			throw $e;
		}
	}
}