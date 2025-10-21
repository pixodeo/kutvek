<?php
declare(strict_types=1);
namespace App\Category;
use App\AppAction;
use Domain\Table\Category;
use Exception;
use Library\HTML\{Form, TraitString, TraitPagination};
use stdClass;

class Read extends AppAction {
	use TraitString, TraitPagination;
	private Category $_table;
	public string $cards;

	public function __invoke(int $category_id){
		try {
			$this->_table = new Category($this->_setDb());		
			$this->_table->setRoute($this->_route);
			$this->_items = $this->_table->itemsInCategory($category_id);
			$this->paginate();
			$slices = $this->getSlices();	
			$cards = $this->_table->cards($slices);
			$this->_path = dirname(__FILE__);
			$this->_view = 'cards';
			$this->cards = $this->partial(compact('cards'));

		}
		catch(Exception $e){}
	}
}