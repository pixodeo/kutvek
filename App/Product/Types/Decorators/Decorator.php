<?php
declare(strict_types=1);
namespace App\Product\Types\Decorators;
use Core\Component;
use Core\Routing\{RouteInterface};
use stdClass;

class Decorator  extends Component {    

    protected $default_price = 0;
       
    public function __construct(protected RouteInterface $_route, protected Component $component){} 

        
    public function getDefaultPrice(){
        return $this->component->getDefaultPrice();
    }

    public function widgetVehicle(){
        return $this->component->widgetVehicle();
    }

    public function options(){
        return $this->component->options();
    }

    public function popinOptions(){
        return $this->component->popinOptions();
    }

    public function finish(): string {
        return '';
    }

    public function comment(): string {
        return $this->component->comment();
    }

    public function _form(){
        return $this->component->form;
    }

    public function vehicleInfo(): stdClass {        
        return $this->component->vehicleInfo();
    } 

    public function productAttributes(): stdClass {
        return $this->component->productAttributes();
        
    }  

    public function colourVariants(){
        return $this->component->colourVariants();
    }

    public function gallery(){
        return $this->component->gallery();
    }

    public function _description(){
        return $this->component->description;
    }

    public function _designation(){
        return $this->component->designation;
    }

    public function _short_desc(){
        return $this->component->short_desc;
    }
}