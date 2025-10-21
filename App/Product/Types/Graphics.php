<?php
declare(strict_types=1);
namespace App\Product\Types;
use Domain\Table\Graphic;
use App\Product\Types\Decorators\{Options, OptionSeatCover};
use Library\HTML\{Form};
use stdClass;

final class Graphics extends Type {   
    public ?object $_defaultKitType = null;
    private Graphic $_graphicsTable;

    public function __invoke(){ 
        $this->_table = $this->getDomain('Product'); 
        $this->form = new Form;    
        $this->_graphicsTable = new Graphic($this->_setDb(), $this->product);
        $this->_graphicsTable->setRoute($this->_route);
        $this->product = $this->_graphicsTable->info((int)$this->product->id);
        $this->setDomain($this->_graphicsTable, 'Graphics');
        $this->_path = dirname(__FILE__,2);
        $this->_view = 'graphic-kit';        
        $this->product->attributes = json_decode($this->product->attributes);
        $reinsurance_items = $this->_table->reinsuranceOnCart();
        $this->meta_title = $this->product->meta_title;
        $this->meta_description = $this->product->meta_description;
        $component = new Options($this->_route,$this);
        // Si option housse de selle existe 
        if((int)$this->product->attributes->seat_cover > 0):
            $component = new OptionSeatCover($this->_route, $component);
        endif;
        $component->setRequest($this->getRequest());        
        $this->_content = $this->partial(compact('reinsurance_items', 'component'));
        $this->_path = false;
        $this->_layout = 'graphic-kits';        
        $this->_response->getBody()->write($this->_print());
        return $this->_response;
    }

   /**
     * Retourne tous les véhicules frères par marque / design.
     * Rajouter le prix du véhicule 
     *
     * @param      <type>  $parent  The parent
     * @param      <type>  $brand   The brand
     */
    public function widgetVehicles()
    {
        // Ajouter le véhicule actuel
        $vehicles = [
            $this->product->vehicle_id => (object)[
                'value'     => $this->product->vehicle_id,
                'text'      => $this->product->vehicle_designation,                
                'uri'       => $this->url('vehicle.graphicYears', ['fqdn'=>1, 'queries' => ['id' => $this->product->vehicle_id]]),
                'design_id' => $this->product->design_id,
                'color_id'  => $this->product->color_id,
                'item'      => $this->product->id
            ]
        ];
        
        $siblings = $this->_graphicsTable->vehicles($this->product->vehicle_id,$this->product->id);
        $sku = "{$this->product->design_id}.{$this->product->color_id}";
         foreach ($siblings as $sibling) {
            $vehicles[$sibling->id] = (object)array(
                'value'     => $sibling->id,
                'text'      => $sibling->name,                
                'uri'       => $this->url('vehicle.graphicYears', ['fqdn'=>1,  'queries' => ['id' => $sibling->id]]),
                'item'      => $sibling->item
            );
        }
        $attributes = [
            'class="onchange field-input select"' , 
            'data-ctrl="graphics.years"',
            'data-sku="' . $sku . '"',
            'data-target="millesim"',
            'form="addToCart"',            
            'required'            
        ];
        //return print_r($vehicles,true);
        $input = $this->form->select(
            'vehicle[version]',
            [
                'label' => 'Modèle / version',
                'id' => 'vehicle-version',
                'i18n' => 'model-version',
                'attributes' => $attributes,
                'values' => $vehicles,
                'selected' => $this->product->vehicle_id,
                'dataset' => [ 'uri', 'item']
            ]
        );

        return <<<TEXT
        <div class="bloc-header" data-i18n="vehicle">Ton véhicule</div>
        {$input}
        TEXT;
    }

    public function widgetMillesims()
    {        
        foreach ($this->product->years as $year) {
            $year->uri = $this->url('vehicle.yearKitTypes', ['fqdn'=>1,  'queries' => ['id' => $year->year_id]]);
            $this->years[$year->year_id] = $year;
        }
        $input = $this->form->select(
            'item[millesim]',
            array(
                'label' => 'Année',
                'id'    => 'millesim',
                'attributes' => array(
                    'class="field-input select onchange"', 
                    'form="addToCart"', 
                    'data-target="kit-type"', 
                    'data-ctrl="graphics.year"',
                    'required'),
                'values' => $this->years,
                'keys' => ['year', 'year_id'],
                'dataset' => ['sku', 'year', 'uri']
            )
        );
        return $input;
    }

    public function widgetTypes()
    {        
        $first = $this->product->years[0]->year_id;
        $types = array_filter($this->product->years, fn($n) => $n->year_id === $first); 
        $this->_defaultKitType = count($types) > 0 ? $types[0] : null;    
        $input = $this->form->select(
            'item[item_price][product]',
            array(
                'label' => 'Type de kit',                
                'id' => 'kit-type',
                'values' => $types, 
                'keys' => ['label', 'price'],              
                'attributes' => array('class="field-input select onchange cost qty-depend"', 'data-ctrl="graphics.price"', 'form="addToCart"', 'required'),

                'dataset'=> ['id', 'designation']
            )
        );
        return $input;
    }

    public function widgetFinish()
    {        
        if($this->product->attributes->finish > 0):
            $this->finishes = $this->_table->finishes($this->product->family_id);           
            $opts = [
            'label' => 'Finition',
            'id' => 'item-price-finish',            
            'values' => $this->finishes,
            'keys' => ['label', 'price'],
            'attributes' => [               
                'class="field-input select cost qty-depend onchange"',
                'data-ctrl="graphics.finish"',
                'form="addToCart"',
                'required',                
                'data-i18n="finish"'
            ],
            'dataset' => ['id', 'name'],
            'class' => []
            ];
            $finish = $this->form->select('item[item_price][finish]',$opts);
            $premium = $this->premium();
            $premiumInfo = $this->premiumInfo();
            $finishInfo = $this->finishInfo();
            return <<<EOT
            <div class="bloc-header" data-i18n="finish">Impression & finition</div>
            <div class="finish-desc" data-i18n="choose-finish-desc">
            <p>Choisis la finition qui protégera et sublimera ton kit de déco.</p><p>Si tu le souhaites, un effet premium comme le chrome et l'holographique est possible.</p>
            <p>Les couleurs peuvent varier en fonction de la luminosité avec un effet premium.</p>
            <p>Nous déconseillons une finition en mat pour les impressions chromées ou métalliques.</p><p><i class="material-symbols-rounded warning">&#xe88e;</i><a href="#effects-finish" class="click link" data-ctrl="app.modal" data-modal="effects-finish">Clique ici pour voir des exemples d'images.</a><p></div>
            {$premium}
            {$premiumInfo}
            {$finish}
            {$finishInfo}
            EOT;
        endif;
        return '';
    }

    public function premium()
    {
        if($this->product->attributes->finish > 0):
            $this->premiums = $this->_table->premiums($this->product->family_id);
            $opts = [
            'label' => 'Effet premium',
            'id' => 'item-price-premium',            
            'values' => $this->premiums,
            'keys' => ['label', 'price'],
            'attributes' => [               
                'class="field-input select cost qty-depend onchange"',
                'data-ctrl="graphics.premium"',
                'form="addToCart"',
                'required',                
                'data-i18n="premium"'
            ],
            'dataset' => ['id', 'name'],
            'class' => []
            ];
            $input = $this->form->select('item[item_price][premium]',$opts);
            return $input;            
        endif;
        return '';
    }  

     /**
     * Ajoute 2 inputs hidden pour envoyer les infos du premium choisi (id, name)
     */
    public function premiumInfo(){
        if($this->product->attributes->finish > 0):
            $id = count($this->premiums) > 0 ? $this->premiums[0]->id : 0;
            $name = count($this->premiums) > 0 ? $this->premiums[0]->name : '';
            return <<<EOT
            <input type="hidden" name="item[item_custom][options][premium][id]" id="premium-id" value="{$id}" form="addToCart"/>
            <input type="hidden" name="item[item_custom][options][premium][name]" id="premium-name" value="{$name}" form="addToCart"/>
            EOT;
        endif;
        return '';
    } 
    /**
     * Ajoute 2 inputs hidden pour envoyer les infos de la finition choisie (id, name)
     */
    public function finishInfo(){
        if($this->product->attributes->finish > 0):
            $id = count($this->finishes) > 0 ? $this->finishes[0]->id : 0;
            $name = count($this->finishes) > 0 ? $this->finishes[0]->name : '';
            return <<<EOT
            <input type="hidden" name="item[item_custom][options][finish][id]" id="finish-id" value="{$id}" form="addToCart"/>
            <input type="hidden" name="item[item_custom][options][finish][name]" id="finish-name" value="{$name}" form="addToCart"/>
            EOT;
        endif;
        return '';
    }

    public function itemTypeInfo(){
        $id = $this->_defaultKitType ? $this->_defaultKitType->id : null;
        $designation = $this->_defaultKitType ? $this->_defaultKitType->designation : null;
        return <<<INPUT
        <input type="hidden" id="type-id" name="item[item_type][id]" value="{$id}" />
        <input type="hidden" id="type-name" name="item[item_type][designation]" value="{$designation}" />
        INPUT;
    } 

    public function sku(){      
        $ids = count($this->years) > 0 ? array_keys($this->years) : [];
        $id = $ids[0] ?? null;
        $parts = [$id, $this->product->design_id,$this->product->color_id];
        $sku = implode('.',$parts);
        return <<<INPUT
        <input type="hidden" id="item-sku" name="item[sku]" value="{$sku}" />
        INPUT;
    }
    

    public function widgetVehicle(){
        return 'Pas de composant véhicule je renvoie de la merde';
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