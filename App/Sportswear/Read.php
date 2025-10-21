<?php
declare(strict_types=1);
namespace App\Sportswear;
use App\AppAction;
use Domain\Table\Catalog;
use Library\HTML\TraitSanitize;
use App\Sportswear\TypeOfProduct\TraitLaunchContext;

final class Read extends AppAction {
	use TraitSanitize, TraitLaunchContext;

	protected Catalog $_table;
	public false|object $product;


	public function __invoke(int $id){	
		try {		
			$this->_table = new Catalog($this->_setDb());
			$this->_table->setRoute($this->_route);	

			
			/*echo  '<p> Article </p>';
			echo '<pre>';
			print_r($this->product);
			echo '</pre>';
			die();*/
			
			$this->_launchContext($id);
			$this->product = $this->_context->product($id);

			$this->_path = dirname(__FILE__);
			$this->_view = $this->_context->getView();
			$this->meta_description = 'desc';
			$this->meta_title =  'Kutvek'; 
			$reinsurance_items = $this->_table->reinsuranceOnCart();
					
			$this->_content = $this->partial(compact('reinsurance_items'));
			$this->_path = false;
			$this->_layout = 'basics';
			$this->_response->getBody()->write($this->_print());
			return $this->_response;
		} catch(\Exception $e){
			die($e->getMessage());
		}	
		
	}
		
}