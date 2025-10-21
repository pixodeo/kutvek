<?php
declare(strict_types=1);
namespace App\Sportswear\TypeOfProduct;
use Core\Routing\RouteInterface;
use Domain\Table\Appareal;
use Library\HTML\Form;

final class Appareals extends Strategy {

    protected $_viewFile = 'appareal';

    public function __construct(protected RouteInterface $_route){
        $this->_table = new Appareal($this->_setDb());
        $this->_table->setRoute($this->_route);  
        $this->form = new Form;      
    }

    public function getName(){return __CLASS__;}

    public function getSizes(): string {
        if($this->product->has_sizes > 0):
            foreach ($this->product->stock as $opt) {
                $inputs[] = (object)[
                    'label' => $opt->size_designation, 
                    'id'=>'opt-'.$opt->size_id, 
                    'value'=> $opt->size_id, 
                    'name'=>'item_size[]',
                    'class' => 'onchange',                    
                    'checked' => false,
                    'dataset' => ['name'=>$opt->size_designation, 'ctrl' => 'appareal.size']
                ];
            }
            return $this->form->radios('Taille', [                        
                'radios' => $inputs,
                'attributes' => ['form="addToCart"', 'required'],                        
                'class' => ['sizes'],
                'dataset' => ['i18n'=>'clothing-size']
            ]);
                 
        endif;
        return '';
    }
}