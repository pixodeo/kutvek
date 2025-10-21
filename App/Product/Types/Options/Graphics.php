<?php
declare(strict_types=1);
namespace App\Product\Types\Options;
use Domain\Table\Graphic;
use App\Product\Types\Decorators\{Options, OptionSeatCover};
use App\Product\Types\Type;
use Library\HTML\Form;
use stdClass;

/**
 * Pattern Strategy
 * This class is the concrete Strategy
 */
final class Graphics extends Type {  

    public function __invoke(){ 
        $this->_table = $this->getDomain('Product'); 
        $this->form = new Form;    
        $table = new Graphic($this->_setDb(), $this->product);
        $table->setRoute($this->_route);
        $table->info((int)$this->product->id);
        $this->setDomain($table, 'Graphics');
        $this->_path = dirname(__FILE__,3);
        $this->_view = 'bloc-options';
        
        $this->product->attributes = json_decode($this->product->attributes);       
        $component = new Options($this->_route,$this);
        // Si option housse de selle existe 
        if((int)$this->product->attributes->seat_cover > 0):
            $component = new OptionSeatCover($this->_route, $component);
        endif;
        $component->setRequest($this->getRequest());        
        $view = $this->partial(compact('component'));         
        $this->_response->getBody()->write($view);
        return $this->_response;
    }   

    public function vehicleInfo(): stdClass {
        if(is_string($this->product->vehicle)) $this->product->vehicle = json_decode($this->product->vehicle);
        return $this->product->vehicle;
    }

    public function productAttributes(): stdClass {
        return $this->product->attributes;        
    }

    public function seatCover(){
        return '';
    }
}