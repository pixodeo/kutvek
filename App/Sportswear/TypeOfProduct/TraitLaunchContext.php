<?php
declare(strict_types=1);
namespace App\Sportswear\TypeOfProduct;
use App\Sportswear\TypeOfProduct\{Appareals,Basics};
use Core\Routing\TraitRequest;
use stdClass;

trait TraitLaunchContext {
	use TraitRequest;

	private Context $_context;

	private function _launchContext(int $id): void {
		$query = $this->_table->query("SELECT b._type FROM items i JOIN behaviors b ON b.id = i.behavior WHERE i.id = :id;", ['id'=>$id],true);
		$behavior = $query ? $query->_type : false;       
		$strategy = match($behavior){
			//'SeatCovers' => new SeatCovers($this->_route),
			//'Graphics' => new Graphics($this->_route),
			//'Sales'	=> new Sales($this->_route),
			//'AccessoryStickers' => new AccessoryStickers($this->_route),
			'Appareals' => new Appareals($this->_route),
			default => new Basics($this->_route)
		};
		$strategy->setRequest($this->getRequest());
		$this->_context = new Context($strategy);
	}

	public function getContext(): Context{
		return $this->_context;
	}
}