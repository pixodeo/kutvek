<?php
declare(strict_types=1);
namespace App\Section\Filters;

use Core\Component;
use Exception;

class Color extends Component{

	public function __invoke(){
		try {
			$table = $this->_tables['Section'];
			$colors = $table->colors();	
			$this->_path = dirname(__FILE__,2);
			$this->_view = 'Filters.colours';
			return $this->partial(compact('colors'));
		} catch(Exception $e){
			throw $e;
		}
	}
}