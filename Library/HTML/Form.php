<?php
namespace Library\HTML;

class Form {
    public function input(string $name, array $opts = []): string{
        return $this->text($name, $opts);
    }
    public function text(string $name, array $opts = []): string
    {            
        $label = isset($opts['label']) ? $opts['label'] : false ;
        $required = false;
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        unset($opts['id']);
        $direction = $opts['direction'] ?? '';
        $attributes = ['type' => ' type="text"']; 
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            $required = in_array('required', $opts['attributes']);
            unset($opts['attributes']);
        } 

        $o = ['attributes' => $attributes , 'value' => null, 'class' => [], 'access' => []];  
        $params = array_merge($o, $opts);

        $input = '<input name="' . $name .'" id="' .$id . '" ';
        if(isset($params['placeholder'])) $input .= ' placeholder="' . $params['placeholder'] . '"';
        $input .= implode(' ', $params['attributes']);
        if($params['value'] !== null) $input .= ' value="' . $params['value'] . '" ';
        $input .= implode(' ', $params['access']);
        $input .= ' />';

        $wrapper = '<div class="field-wrapper ' . $direction;
        if($required) {
            $wrapper .= ' required ';
        }
        $wrapper .= implode(' ', $params['class']);
        $wrapper .= '">';
        if($label) $wrapper .= "<label for=\"{$for}\">{$label}</label>";
        $wrapper .= $input;
        $wrapper .= '</div>';
        return $wrapper;
    }

    public function password($name, array $opts = []){
        $attributes = array('type' =>'type="password"');
        if(array_key_exists('attributes', $opts))
            $attributes = array_merge($attributes, $opts['attributes']);      
        $opts['attributes'] = $attributes;       
        return $this->input($name, $opts);
    }

    public function longText(string $name,  array $opts = []):string {
        $label = isset($opts['label']) ? $opts['label'] : false ;
        $required = false;
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        unset($opts['id']);
        $direction = $opts['direction'] ?? '';
        $attributes = []; 
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            $required = in_array('required', $opts['attributes']);
            unset($opts['attributes']);
        } 

        $o = ['attributes' => $attributes , 'value' => null, 'class' => [], 'access' => []];  
        $params = array_merge($o, $opts);

        $input = '<textarea name="' . $name .'" id="' .$id . '" ';
        if(isset($params['placeholder']))
            $input .= ' placeholder="' . $params['placeholder'] . '"';

        $input .= implode(' ', $params['attributes']);
        $input .= ' />';
        if($params['value'] !== null)
            $input .=  $params['value'];
        $input .= '</textarea>'; 

        $wrapper = '<div class="field-wrapper ' . $direction;
        if($required) {
            $wrapper .= ' required';
        }
        $wrapper .= implode(' ', $params['class']);
        $wrapper .= '">';
        if($label) $wrapper .= "<label for=\"{$for}\">{$label}</label>";
        $wrapper .= $input;
        $wrapper .= '</div>';
        return $wrapper;       
    }

    public function textarea(string $name,  array $opts = []): string {
        $required = false;
        $wrap = [];      
        $label = isset($opts['label']) ? $opts['label'] : false ;
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        unset($opts['id']);
        $direction = $opts['direction'] ?? '';
        if(isset($opts['wrapper'])) {
            $wrap = $opts['wrapper'];
            unset($opts['wrapper']);
        }
        
        if(array_key_exists('attributes', $opts)){
            $attributes = $opts['attributes'];
            $required = in_array('required', $opts['attributes']);
            unset($opts['attributes']);
        } else {
            $attributes = [];
        }

        $o = ['attributes' => $attributes , 'value' => null];  
        $params = array_merge($o, $opts);

        /**
         * <div class="field-wrapper textarea required">       
            <textarea name="item[comment]" data-i18n="custom-graphics-comment" id="custom-graphics-comment" class="field-input textarea" required></textarea>
            <label class="required" for="custom-graphics-comment">Ton commentaire</label>
            </div>
         *
         * @var        string
         */

        $input = '<textarea name="' . $name .'" id="' .$id . '" ';
        if(isset($params['placeholder']))
            $input .= ' placeholder="' . $params['placeholder'] . '"';

        $input .= implode(' ', $params['attributes']);
        $input .= ' />';
        if($params['value'] !== null)
            $input .=  $params['value'];
        $input .= '</textarea>';        
        if($label){
            $label = "<label for=\"{$for}\">{$label}</label>";            
        }
        $div = "<div ";       
        $div .= "class=\"field-wrapper textarea {$direction}";
        if($required) $div .= ' required';
        $div .= '">';
        $div .= "{$input}{$label}</div>";
        return $div; 
    }

    public function trix(string $name, array $opts): string
    {         
           
        $label = isset($opts['label']) ? $opts['label'] : false;
        $id = $opts['id'] ?? $name;               
        unset($opts['id']);
        $direction = $opts['direction'] ?? '';
        
        $value = array_key_exists('value', $opts) ? $opts['value'] : '';
        if(array_key_exists('input', $opts)){
            $trix = "<textarea name=\"{$name}\" id=\"{$id}\" class=\"hide\"";
        } else{
            $trix = "<input name=\"{$name}\" id=\"{$id}\" type=\"hidden\" ";
            
            $trix .= "value=\"$value\" ";
        }
        
       
        $attributes = [];

        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        } 

        $o = ['attributes' => $attributes];  
        $params = array_merge($o, $opts);    

        $trix .= implode(' ', $params['attributes']);
        if(array_key_exists('input', $opts))
            $trix .= ">{$value}</textarea>";         
        else $trix .= " />"; 
        $trix .= "<div class=\"trix-container\">";
        $trix .= "<trix-editor input=\"{$id}\"></trix-editor>";
        $trix .= "</div>";
        if($label)
        {
            $label = "<label for=\"{$id}\">{$label}</label>";
            return $this->_trixLabel($label, $trix);
        }
        return $trix;
    }    
    
    public function radio(string $name, array $opts = [], bool $wrapper = true){         
        $label = isset($opts['label']) ? $opts['label'] : false ;   
         
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        $direction = $opts['direction'] ?? '';
        unset($opts['id']);

        // $data : objet issu de la bdd, si on veut mettre des attributs data-* sur la div wrapper
        $data = array_key_exists('data', $opts) ? $opts['data'] : (object)[];
        unset($opts['data']);

         /**
         * Attributs sur la div qui entoure l'input / label
         *
         * @var        <type>
         */
        $wrapper_dataset = $opts['wrapper_dataset'] ?? false;
        unset($opts['wrapper_dataset']);

        // data-* propres à l'input 
        $dataset = $opts['dataset'] ?? false;
        unset($opts['dataset']);

        $attributes = ['type' => ' type="radio"'];        
        
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        } 
        /*if(array_key_exists('data', $opts)){           
            foreach($opts['data'] as $data => $val):
                $attributes[] = 'data-'.$data .'="' . $val .'"';
            endforeach;
            //unset($opts['data']);
        } */  


        $o = ['attributes' => $attributes , 'value' => null, 'class' => [], 'access' => []];  
        $params = array_merge($o, $opts);

        $input = '<input name="' . $name .'" id="' .$id . '" ';
        $input .= implode(' ', $params['attributes']);
        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';
        $input .= implode(' ', $params['access']);
        if($dataset):                                               
                foreach($dataset as  $o):
                    $d = str_replace('_', '-', $o);
                    $input .= " data-{$d}=\"{$data->$o}\" "; 
                endforeach;   
            endif; 
        $input .= ' />';
        if($wrapper):
            $wrapper = '<div class="field-wrapper radio '. implode(' ', $params['class']) . $direction . '"';
            if($wrapper_dataset):                                               
                foreach($wrapper_dataset as  $o):
                    $d = str_replace('_', '-', $o);
                    $wrapper .= " data-{$d}=\"{$data->$o}\" "; 
                endforeach;   
            endif;    
            $wrapper .= '>';
            if($label) $wrapper .=   $this->_label($label, $for);
            $wrapper .= $input;
            $wrapper .= '</div>';            
        else:        
        $wrapper .= $input;
        $wrapper .= $this->_label($label, $for); 
            
        endif;
        return $wrapper;
    }

    public function radios(string $name, array $opts = [] ): string{
        $label = isset($opts['label']) ? $opts['label'] : false ;
        $required = '';
        
        $radios = $opts['radios'];
        unset($opts['radios']);
        if(array_key_exists('attributes', $opts)){
            $attributes = $opts['attributes'];
            $required = in_array('required', $opts['attributes']) ? 'required' : '';
            unset($opts['attributes']);
        } 

        
        $params = implode(' ', $attributes);
        $inputs = [];

        foreach($radios as $radio):
            $dataset = [];
            $checked = $radio->checked ? 'checked' : '';
            //$form = isset($radio->form) ? "form=\"{$radio->form}\"" : '';
            if(isset($radio->dataset)):
                foreach ($radio->dataset as $k => $data) {
                   $dataset[] = "data-{$k}=\"{$data}\"";
                }
            endif;
            $classes = isset($radio->class) ? "class=\"$radio->class\"" : '';
            $input = "<input type=\"radio\" name=\"$radio->name\" id=\"$radio->id\"  value=\"$radio->value\" $classes $checked  $params ";
            $input .= implode(' ', $dataset);
            $input .= '/>';
            $inputs[] = $input;
            $inputs[] = "<label for=\"$radio->id\">$radio->label</label>";           
        endforeach;
        $inputs = implode('', $inputs);
        $css = $opts['class'] ?? [];
        $css = implode(' ', $css);
        $dataset = [];
        foreach ($opts['dataset'] as $k => $data) {
            $dataset[] = "data-{$k}=\"{$data}\"";
        }
        $dataset = implode(' ', $dataset);
        $fieldset = <<<TEXT
        <div class="fieldset-wrapper {$css}">        
        <fieldset class="fieldset-radio" {$params} {$dataset}>
            <legend>{$name}</legend>
            $inputs
        </fieldset>
        </div>
        TEXT;
        return $fieldset;
    }

    public function checkbox(string $name, array $opts = []){         
        $label = isset($opts['label']) ? $opts['label'] : false ;        
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        $direction = $opts['direction'] ?? '';
        unset($opts['id']);
        $attributes = ['type' => ' type="checkbox"'];        
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        }
        $o = ['attributes' => $attributes , 'value' => null, 'class' => [], 'access' => []];  
        $params = array_merge($o, $opts);
        $input = '<input name="' . $name .'" id="' .$id . '" ';
        $input .= implode(' ', $params['attributes']);
        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';
        $input .= implode(' ', $params['access']);

        if(array_key_exists('checked', $params) && $params['checked'] > 0) 
            $input .= ' checked';
        
        $input .= ' />';

        $wrapper = '<div class="field-wrapper checkbox ' . $direction . ' ';
        $wrapper .= implode(' ', $params['class']);
        $wrapper .= '">';
        if($label) $wrapper .= "<label for=\"{$for}\">{$label}</label>";
        $wrapper .= $input;
        $wrapper .= '</div>';
        return $wrapper;
    }

    public function qty(string $name, array $opts = []): string {
        $classes = ['field-input', 'select', 'item-qty'];
        $required = false;
        if(array_key_exists('attributes', $opts)){
            $required = in_array('required', $opts['attributes']);
        }
        $uri = array_key_exists('data-uri', $opts) ? " data-uri=\"{$opts['data-uri']}\"" : '';
        unset($opts['data-uri']);
        
        $css = implode(' ', array_merge($classes, $opts['class'] ?? []));       
        $opt_css = implode(' ',  $opts['opt_class'] ?? []);
        $for = $opts['id'] ?? $name;      
        unset($opts['keys']);
        unset($opts['dataset']);
        unset($opts['opt_class']);
        unset($opts['class']);
        $selected = (int)$opts['selected'];
        $params = array_merge(['attributes' => [], 'values' => [],'valueTypeOf' => 'INT'], $opts);
        $options = [];
        $select = "<select name=\"$name\" class=\"$css\" ";
        $select .= "id=\"{$for}\" ";        
        $select  .= implode(' ', $params['attributes']);
        $select .= $uri;
        $select .= '>';

        // les options
        // si on a des attributs spécifiques pour les options       
        if(isset($params['placeholder']))
            $options[] = "<option value data-placeholder >{$params['placeholder']}</option>";
                   
        for($i = 1; $i <= 10; $i++){
            $is_selected = $selected === $i ? 'selected' : '';
            $options[] = "<option {$is_selected} value=\"{$i}\">{$i}</option>";
            
        }
        $select .= implode(' ', $options);
        $select .= '</select>';
        return $select;

        
    }

    public function select(string $name, array $opts): string
    {       
        $required = false;
        if(array_key_exists('attributes', $opts)){
            $required = in_array('required', $opts['attributes']);
        }
        $uri = array_key_exists('data-uri', $opts) ? " data-uri=\"{$opts['data-uri']}\"" : '';
        unset($opts['data-uri']);
        $label = isset($opts['label']) ? $opts['label']: false ;

        $css = $opts['class'] ?? [];
        $wrapper = isset($opts['wrapper']) ? $opts['wrapper'] : true;
        $opt_css = $opts['opt_class'] ?? [];
        if(!empty($opt_css)): $opt_css = implode(' ', $opt_css);
            
        else:
            $opt_css = null;
        endif;     

        $for = $opts['id'] ?? $name;
        
        $keys = $opts['keys'] ?? false;
        $dataset = $opts['dataset'] ?? false;
        unset($opts['keys']);
        unset($opts['dataset']);
        unset($opts['opt_class']);
        $params = array_merge(['attributes' => [], 'values' => [],'valueTypeOf' => 'INT'], $opts);
        $options = [];
        $select = "<select name=\"$name\" ";
        $select .= "id=\"{$for}\" ";        
        $select  .= implode(' ', $params['attributes']);
        $select .= $uri;
        $select .= '>';

        // les options
        // si on a des attributs spécifiques pour les options       
        if(isset($params['placeholder']))
            $options[] = "<option value data-placeholder >{$params['placeholder']}</option>";
        // La data
        if($keys){
            $value = array_pop($keys);
            $text = array_pop($keys);
            foreach($params['values'] as $data){
                $widget = $opt_css !== null ? "<option class=\"{$opt_css}\" value=\"{$data->$value}\"": "<option value=\"{$data->$value}\"";                  
                if($dataset):                                               
                    foreach($dataset as  $o):
                        $dataname = str_replace('_', '-', $o);
                        $widget .= " data-{$dataname}=\"{$data->$o}\" "; 
                    endforeach;   
                endif;                             
                if(isset($params['selected']) && $data->$value == $params['selected'])
                    $widget .= ' selected'; 
                if(isset($params['disabled']) && $data->$value == $params['disabled'])
                    $widget .= 'disabled';               

                $widget .= ">{$data->$text}</option>";
                $options[] = $widget;
            }
        } 
       
        else
        {
            foreach($params['values'] as $k => $value) {
                
                // Si on a un simple tableau numérique
                if(!is_array($value) && !is_object($value))
                {
                    $widget = $opt_css !== null ? "<option class=\"{$opt_css}\" value=\"{$value}\"" : "<option value=\"{$value}\"";
                    if(isset($params['selected']) && $value == $params['selected'])
                        $widget .= " selected >{$value}</option>";
                    else
                        $widget .= " >{$value}</option>";                   
                    $options[] = $widget;
                }
                // Sinon
                else {
                    if(!is_object($value))
                        $value = (object)$value;
                    if(isset($value->{'value'}))
                    {
                        $widget = $opt_css !== null ? "<option class=\"{$opt_css}\" value=\"{$value->value}\"" : "<option value=\"{$value->value}\"";
                                            
                        if($dataset):                                               
                            foreach($dataset as  $o):
                                $widget .= " data-{$o}=\"{$value->$o}\" "; 
                            endforeach;   
                        endif;            
                        
                        if(isset($params['selected']) && $value->value == $params['selected'])
                            $widget .= " selected >{$value->text}</option>";
                        else
                            $widget .= " >{$value->text}</option>";
                        $options[] = $widget;
                    } else
                    {
                        $options[] = $opt_css !== null ? "<option class=\"{$opt_css}\" value=\"{$value->text}\">{$value->text}</option>" : "<option value=\"{$value->text}\">{$value->text}</option>";
                    }
                }
            }
        }            
        
        $select .= implode(' ', $options);
        $select .= '</select>';
        if($label)
        {
            $label = "<label for=\"{$for}\">{$label}</label>";
        }else $label = '';
        if(!$wrapper) return $label . $select;
        $div = "<div ";       
        $div .= "class=\"field-wrapper select ";
        if($required) $div .= ' required ';
        $div .= implode(' ', $css);
        $div .= '">';
        $div .= "{$label}{$select}<i class=\"material-symbols-rounded dropdown\">&#xe5c5;</i></div>";
        return $div;     
    }

    protected function _options($data){

    }
    
    public function datetime(string $name, array $opts): string
    {
        $required = false;
        $label = isset($opts['label']) ? $opts['label'] : false;
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        unset($opts['id']);
        $direction = $opts['direction'] ?? '';
        unset($opts['direction']);
        $attributes = ['type' => ' type="datetime-local"'];

        
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            $required = in_array('required', $opts['attributes']);
            unset($opts['attributes']);
        } 

        $o = ['attributes' => $attributes , 'value' => null];  
        $params = array_merge($o, $opts);

        $input = '<input name="' . $name .'" id="' .$id . '" ';
        if(isset($params['placeholder']))
            $input .= ' placeholder="' . $params['placeholder'] . '"';

        $input .= implode(' ', $params['attributes']);

        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';

        $input .= ' />';
        if($label)
        {

            $label = "<label for=\"{$for}\">{$label}</label>";
            return $this->_withLabel($label, $input, $direction, $required);
        }
        return $input;
    }

    public function switch(string $name, array $opts = []): string
    {
        $label = isset($opts['label']) ? $opts['label'] : false ;        
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        unset($opts['id']);
        $direction = $opts['direction'] ?? '';
        unset($opts['direction']);
        $attributes = ['type' => ' type="checkbox"'];        
        
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        } 

        $o = ['attributes' => $attributes, 'order' => 1, 'value' => null, 'class' => [], 'access' => []];  
        $params = array_merge($o, $opts);

        $input = '<input name="' . $name .'" id="' .$id . '" ';
        if(isset($params['placeholder']))
            $input .= ' placeholder="' . $params['placeholder'] . '"';

        $input .= implode(' ', $params['attributes']);

        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';

        $input .= implode(' ', $params['access']);

        if(array_key_exists('checked', $params) && $params['checked'] > 0) 
            $input .= ' checked';

        $input .= ' />';

        $wrapper = '<div class="field-wrapper-switch ';

        $wrapper .= implode(' ', $params['class']);
        $wrapper .= '">';
        $wrapper .= $params['order'] === 2 ? '<span class="switch-label right">' :'<span class="switch-label">';
        $wrapper .= $label;
        $wrapper .= '</span>';

        $wrapper .= $input;
        
        $wrapper .= "<label for=\"{$for}\"><i></i></label>";
        
       
        $wrapper .= '</div>';
        return $wrapper;
    }

    public function number(string $name, array $opts = []): string {
        $required = false;
        // <input type="number" placeholder="1.0" step="0.01" min="0" max="10" />
        $label = isset($opts['label']) ? $opts['label'] : false ;
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        $direction = $opts['direction'] ?? '';
        unset($opts['id']);

        $attributes = ['type' => ' type="number"']; 

        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            $required = in_array('required', $opts['attributes']);
            unset($opts['attributes']);
        } 

        $o = ['attributes' => $attributes , 'value' => null, 'class' => [], 'access' => []];  
        $params = array_merge($o, $opts);
        
        $input = '<input name="' . $name .'" id="' .$id . '" ';
        if(isset($params['placeholder'])) $input .= ' placeholder="' . $params['placeholder'] . '"';
        $input .= implode(' ', $params['attributes']);
        if($params['value'] !== null) $input .= ' value="' . $params['value'] . '" ';
        $input .= implode(' ', $params['access']);
        $input .= ' />';

        
        $wrapper = '<div class="field-wrapper ' . $direction;
        if($required) {
            $wrapper .= ' required ';
        }
        $wrapper .= implode(' ', $params['class']);
        $wrapper .= '">';
        if($label) $wrapper .= "<label for=\"{$for}\">{$label}</label>";
        $wrapper .= $input;
        $wrapper .= '</div>';
        return $wrapper;
    }

    private function _is_assoc($var)
    {
        return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
    }
    private function _withLabel($label, $field, $direction, $required = false)
    {
        $div = "<div ";       
        $div .= "class=\"field-wrapper {$direction}";
        if($required) $div .= ' required';
        $div .= '">';
        $div .= "{$label}{$field}</div>";
        return $div; 

    }

    private function _label($label, $for){
        if(is_object($label)):
        $attrs = implode('',$label->attributes??[]);
        return <<<TEXT
        <label for="{$for}" {$attrs}>{$label->text}</label>
        TEXT;
        endif;
        return <<<TEXT
        <label for="{$for}" >{$label}</label>
        TEXT;
    
    }
    private function _trixLabel($label, $field)
    {
        $div = "<div ";

        if(!empty($wrap))
        {
            $div .= implode(' ', $wrap['attributes']);
            $div .= ">{$label}{$field}</div>";
            return $div;    
        }
        $div .= "class=\"field-wrapper column\">{$label}{$field}</div>";
        return $div; 

    }
    /**
 * Build an option. 
 * Pareil que checkOption mais utilisé dans
 * Action/Product/Show
 *
 * @param      <type>  $option  The option
 *
 * @return     <type>  The option.
 */
public function buildOption($option){
        // si on a un input radio
        // 
        if($option->input_type == 'checkbox'):
        $option = <<<EOT
<div class="product-option option"  data-opt="{$option->id}">
<a href="#{$option->modal}" class="click" data-ctrl="utils.popin"></a>
<input form="addToCart" type="checkbox" data-type="opt" name="{$option->input_name}" id="{$option->label_id}" value="{$option->value}" class="onchange" data-ctrl="option.add" data-modal="{$option->modal}" data-name="{$option->name}" data-pricef="{$option->pricef}" data-text="{$option->name} +{$option->pricef}" />
<label  {$this->_hasSibling($option->sibling)} for="{$option->label_id}" class="no-zero"><img class="picto" src="/img/pictos/options/{$option->id}.png" />
<span>{$option->name}</span>
</label>
<span class="price">{$option->pricef}</span>
</div>
EOT;
        
        // faire fonctionner la déselection
        elseif($option->input_type == 'radio'):
         $option = <<<EOT
<div class="product-option option radio"  data-opt="{$option->id}">
<a href="#{$option->modal}" class="click" data-ctrl="utils.popin"></a>
<input form="addToCart" type="radio" name="{$option->input_name}" id="{$option->label_id}" value="{$option->value}" class="oninput no-zero" data-ctrl="option.add" data-modal="{$option->modal}" data-name="{$option->name}" data-pricef="{$option->pricef}" data-text="{$option->name} +{$option->pricef}" data-type="opt"/>
<label  {$this->_hasSibling($option->sibling)} for="{$option->label_id}" class="no-zero">
<img class="picto" src="/img/pictos/options/{$option->id}.png" />
<span>{$option->name}</span>
</label>
<label for="{$option->label_id}-0" class="zero cancel"><span class="icon material-symbols-rounded">cancel</span></label>
<input form="addToCart" type="radio" name="{$option->input_name}" id="{$option->label_id}-0" value="0" class="onchange zero"  data-ctrl="option.add" data-modal="{$option->modal}"  data-type="opt" />
<span class="price">{$option->pricef}</span>
</div>
EOT;
        else:
        $option = <<<EOT
<div class="product-option option"  data-opt="{$option->id}"><label  {$this->_hasSibling($option->sibling)} ><img class="picto" src="/img/pictos/options/{$option->id}.png" /><span>{$option->name}</span><a href="#" class="click" data-ctrl="utils.popin" data-modal="{$option->modal}" data-type="$option->input_type"></a></label><span class="price">{$option->pricef}</span></div>
EOT;

        endif;
        return $option;
}
public function buildOptionSeatCover($option){
$option = <<<EOT
<div class="product-option option">
<input form="addToCart" type="checkbox" name="item[price][seat_cover]" id="{$option->label_id}" value="{$option->value}" class="onchange standalone" data-id="{$option->id}" data-ctrl="item.seat" data-name="{$option->name}" data-pricef="{$option->pricef}" data-text="{$option->name} +{$option->pricef}" />
<label for="{$option->label_id}" class="onchange zero" data-ctrl="option.unset"><span class="icon material-symbols-rounded">close</span></label>
<label  {$this->_hasSibling($option->sibling)} for="{$option->label_id}" class="no-zero"><img class="picto" src="/img/pictos/options/seat-cover.png" />
<span>{$option->name}</span>
</label>
<span class="price">{$option->pricef}</span>
</div>
EOT;
return $option;
}
public function buildOptionRimSticker($option){
$option = <<<EOT
<div class="product-option option">
<input form="addToCart" type="checkbox" name="item[price][rim_sticker]" id="{$option->label_id}" value="{$option->value}" class="onchange standalone" data-id="{$option->id}" data-ctrl="item.rim" data-name="{$option->name}" data-pricef="{$option->pricef}" data-text="{$option->name} +{$option->pricef}" />
<label for="{$option->label_id}" class="onchange zero" data-ctrl="option.unset"><span class="icon material-symbols-rounded">close</span></label>
<label  {$this->_hasSibling($option->sibling)} for="{$option->label_id}" class="no-zero"><img class="picto" src="/img/pictos/options/rim-sticker.png" />
<span>{$option->name}</span>
</label>
<span class="price">{$option->pricef}</span>
</div>
EOT;
return $option;
}
public function buildOptionDoorSticker($option){
$option = <<<EOT
<div class="product-option option">
<input form="addToCart" type="checkbox" name="item[price][door_stickers]" id="{$option->label_id}" value="{$option->value}" class="onchange standalone" data-id="{$option->id}" data-ctrl="item.doorStickers" data-name="{$option->name}" data-pricef="{$option->pricef}" data-text="{$option->name} +{$option->pricef}" />
<label for="{$option->label_id}" class="onchange zero" data-ctrl="option.unset"><span class="icon material-symbols-rounded">close</span></label>
<label  {$this->_hasSibling($option->sibling)} for="{$option->label_id}" class="no-zero label-primary"><img class="picto" src="{$option->picto}" />
<span>{$option->name}</span>
</label>
<span class="price">{$option->pricef}</span>
</div>
EOT;
return $option;
}

private function _hasSibling($sibling = null)
    {
        return ( $sibling !== null ? "data-sibling=\"{$sibling}\"": '' );
    }
}