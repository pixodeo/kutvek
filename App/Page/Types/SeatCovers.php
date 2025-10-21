<?php
declare(strict_types=1);
namespace App\Product\Types;

use App\Product\Department;
use App\Product\Domain\Table\SeatCover;
use Core\Routing\RouteInterface;

class SeatCovers extends Department  {
	protected $_viewFile = 'seat-cover';

	public function __construct(protected RouteInterface $_route)
   	{
      $this->_table = new SeatCover($this->_setDb());
      $this->_table->setRoute($_route);
   	}

	public function getName(){return __CLASS__;}

	public function vehicles(): array
   {      
      return $this->_table->itemVehicles($this->product->id);
   }

   public function colours():string{
      $this->_path = dirname(__FILE__,2);
      $this->_view = 'Seat-Covers.colours';
      $colours = $this->_table->itemColors($this->product->id);
      return $this->partial(compact('colours'));

   }
   /**
    * Convient pour kit déco , housse de selle
    * En fonction du behavior 
    */
   public function suitableFor():string {

      $this->_path = dirname(__FILE__,2);
      $this->_view = 'Seat-Covers.suitable-for';
      $vehicles = $this->vehicles();
      $brands = $this->_table->brands($this->product->category_info->family);
      return $this->partial(compact('vehicles', 'brands'));
        
   }
}