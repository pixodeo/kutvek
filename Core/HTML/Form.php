<?php

namespace Core\HTML;


/**
 * Class Form
 * Permet de générer un formulaire rapidement et simplement
 */

class Form{

    /**
     * @var array Données utilisées par le formulaire
     */
    private $data;
    private $fields =[];
    private $rules = [];
    private $filters = [];
    protected $input_class;
    private $colors = array(
        array('text'=> '','value' => '', 'style' => 'background-color:white; color: black; border-color:#1d1d1b;'),
        array('text'=> 'blanc','value' => 'blanc', 'style' => 'background-color:white; color: black; border-color:#1d1d1b;'),
        array('text'=> 'bleu husqvarna','value' => 'bleu-husqvarna', 'style' => 'background-color:#0f2f59; color: white;'),
        array('text'=> 'bleu ktm','value' => 'bleu-ktm', 'style' => 'background-color:#0f2f59; color: white;'),
        array('text'=> 'bleu polaris','value' => 'bleu-polaris', 'style' => 'background-color:#0069b2; color: white;'),
        array('text'=> 'bleu sherco','value' => 'bleu-sherco', 'style' => 'background-color:#312783; color: white;'),
        array('text'=> 'bleu yamaha','value' => 'bleu-yamaha', 'style' => 'background-color:#094496; color: white;'),
        array('text'=> 'bleu clair','value' => 'bleu-clair', 'style' => 'background-color:#009ee2; color: white;'),
        array('text'=> 'carbone','value' => 'carbon', 'style' => '
        background-color: rgb(32, 32, 32);
      background-image: linear-gradient(45deg, rgba(48,47,46) 25%, transparent 25%, transparent 75%, rgba(48,47,46) 75%, rgba(48,47,46)), linear-gradient(45deg, rgba(48,47,46) 25%, transparent 25%, transparent 75%, rgba(48,47,46) 75%, rgba(48,47,46)), linear-gradient(to bottom, rgb(8, 8, 8), rgb(32, 32, 32));
      background-size: 10px 10px, 10px 10px, 10px 5px;
      background-position: 0px 0px, 5px 5px, 0px 0px;
        color:white;
        '),
        array('text'=>'cuivre polaris','value' => 'cuivre-polaris', 'style' => 'background-color:#925e41; color: white;'),
        array('text'=>'antracite','value' => 'antracite', 'style' => 'background-color:#808082; color: white;'),
        array('text'=>'gris clair','value' => 'gris-clair', 'style' => 'background-color:#bcbcc1; color: white;'),
        array('text'=>'gris foncé','value' => 'gris-fonce', 'style' => 'background-color:#525253; color: white;'),
        array('text'=>'gris antracite','value' => 'gris-antracite', 'style' => 'background-color:#808082; color: white;'),
        array('text'=>'jaune can am','value' => 'jaune-can-am', 'style' => 'background-color:#fecc00; color: black;'),
        array('text'=>'jaune husqvarna','value' => 'jaune-husqvarna', 'style' => 'background-color:#ffed00; color: black;'),
        array('text'=>'jaune néon','value' => 'jaune-neon', 'style' => 'background-color:#ebf511; color: black;'),
        array('text'=>'jaune suzuki','value' => 'jaune-suzuki', 'style' => 'background-color:#feed00; color: black;'),
        array('text'=>'kaki','value' => 'kaki', 'style' => 'background-color:#3b4b27; color: white;'),
        array('text'=>'ktm crux','value' => 'ktm-crux', 'style' => 'background-color:#6ec5d7; color: white;'),
        array('text'=>'ktm control','value' => 'ktm-control', 'style' => 'background-color:#009787; color: white;'),
        array('text'=>'ktm kontrol','value' => 'ktm-kontrol', 'style' => 'background-color:#009787; color: white;'),
        array('text'=>'ktm skyline','value' => 'ktm-skyline', 'style' => 'background-color:#4570a4; color: white;'),
        array('text'=>'noir','value' => 'noir', 'style' => 'background-color:#1d1d1b; color: white;'),
        array('text'=>'or','value' => 'or', 'style' => 'background-color:#d6ac53; color: white;'),
        array('text'=>'orange can am','value' => 'orange-can-am', 'style' => 'background-color:#ee8800; color: black;'),
        array('text'=>'orange ktm','value' => 'orange-ktm', 'style' => 'background-color:#fd5600; color: white;'),
        array('text'=>'orange','value' => 'orange', 'style' => 'background-color:#ec6b06; color: white;'),
        array('text'=>'rose','value' => 'rose', 'style' => 'background-color:#e5007e; color: white;'),
        array('text'=>'rouge foncé','value' => 'rouge-fonce', 'style' => 'background-color:#9d151a; color: white;'),
        array('text'=>'rouge','value' => 'rouge', 'style' => 'background-color:#ff0000; color: white;'),
        array('text'=>'rouge honda','value' => 'rouge-honda', 'style' => 'background-color:#e30613; color: white;'),
        array('text'=>'sable','value' => 'sable', 'style' => 'background-color:#d8ad7b; color: black;'),
        array('text'=>'vert cf moto','value' => 'vert-cf-moto', 'style' => 'background-color:#0d97a0; color: white;'),
        array('text'=>'vert kawasaki','value' => 'vert-kawasaki', 'style' => 'background-color:#39c71f; color: white;'),
        array('text'=>'vert néon','value' => 'vert-neon', 'style' => 'background-color:#c7e50f; color: black;'),
        array('text'=>'vert utilitaire 02','value' => 'vert-utilitaire-02', 'style' => 'background-color:#005c2d; color: white;'),
        array('text'=>'vert utilitaire','value' => 'vert-utilitaire', 'style' => 'background-color:#5e6849; color: white;'),
        array('text'=>'vert','value' => 'vert', 'style' => 'background-color:#8bc53f; color: black;'),
        array('text'=>'violet','value' => 'violet', 'style' => 'background-color:#9e1881; color: white;')
    
    
    );

    /**
     * @var string Tag utilisé pour entourer les champs
     */
    public $surround = 'p';

    /**
     * @param $data Données utilisées par le formulaire
     */

    /**
    * Validator trait
    *
    */
    use \Core\Library\TraitValidator, \Core\Library\TraitUtils;

    public function __construct($data = null){        
        if($data !== null) {
            // Si $data est un []
            if(is_array($data)) {
                $this->data = $data;
            } else {
                $this->data[] = $data;
            }
        }        
    }

    public function _toPost($fields, $is_array = true) {       
        $properties = [];
        $data = [];
        $lines = 0;
        if($is_array) {
            foreach ($fields as $key => $line) {
                if(is_array($line)) {
                    $lines = count($line);
                    $properties[] = $key;                 
                }else {
                    $data[$key] = $line;
                }                
            }
            for ($i=0; $i < $lines; $i++) {                
                $data[$i] = (object) [];                       
            }            
            foreach ($data as $k => &$value) {
                if(is_int($k)) {
                    foreach ($properties as $property) {
                        $value->$property = $fields[$property][$k];
                    }
                }               
            }
           return $data;
        }
    }

    public function setData($data) {
          $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    /**
     *  Si le nom est un tableau 
     */

    protected function setName($name){
        $s = (string)$name;
        if($s[-1] === ']') {
          return true;
        }
        return false;
    }

    protected function getName($name) {
        $s = (string)$name;
        if($s[-1] === ']') {
            $pos = strpos($name, '[');
            // retourne ce qu'il y a entre les crochets, l'index num du tableau data
            $index = substr($name, $pos + 1, -1); 
            // retourne l'index associatif, le nom de la propriété
            $name = substr($name, 0, strpos($name, '['));
            return array('name' => $name, 'index'=>$index);
        } else { 
            return array('name'=>$name);}
    }

    /**
    * @param $name string Index de la valeur à récupérer
    * @return string
    * marche aussi pour la session
    * exemple : description[0] 
    */
    public function getValue($name){
        if($this->setName($name)){
            $pos = strpos($name, '[');
            // retourne ce qu'il y a entre les crochets, l'index num du tableau data
            $index = substr($name, $pos + 1, -1); 
            // retourne l'index associatif, le nom de la propriété
            $name = substr($name, 0, strpos($name, '['));
            //die($name);
        }else{
            $index = $name;
        }
        // 
        if(is_object($this->data)){
            return $this->data->$index;
        }
        if(isset($this->data[$index])){
            if(is_object($this->data[$index])) {
                if(property_exists($this->data[$index], $name)) {
                    return  $this->data[$index]->$name;
                }
             } else {
                 return $this->data[$index];
             }
           
        }
        if(isset($_SESSION['fields'][$index])){
            return $_SESSION['fields'][$index];
        }
        return null;
    }

    /**
     * input type file
     * @param  string $name  [description]
     * @param  string $label [description]
     * @param  array  $opts  [description]
     * @return [type]        [description]
     */
    public function file(string $name, string $label, ?array $options = []): string {
        if(isset($options['class'])) {
            $wrap_class = array_key_exists('wrap',$options['class'] ) ? ' class="field field-file '. $options['class']['wrap']. '" ' : ' class=" field field-file" ';           
            $field_class = array_key_exists('field',$options['class'] ) ? ' class="input-file '. $options['class']['field'] . '" ' : ' class=" input-file" '; 
        }
        $return = '<div ' . $wrap_class . '>';
        $return .= '<label class="input-file-trigger" for="'.$name.'"   tabindex="0">' . $label . '</label>';
        $return .= '<input ' . $field_class . ' id="'.$name.'" name="'.$name.'" type="file" />';        
        $return .= '</div>';
        $return .= '<p class="' . $options['class']['wrap']. '"></p>';
        return $return;         
    }

    protected function  getErrors($field){
        
        $name = $this->getName($field);
        if(array_key_exists('index', $name)){
            $index = $name['index'];
            $key = $name['name'];
            if(isset($this->errors[$key][$index])){
                $msg = $this->errors[$key][$index];
                return "<div class=\"errors\">{$msg}</div>";
            }
        } else {
           if(isset($this->errors[$field])){
                $msg = $this->errors[$field];
                return "<div class=\"errors\">{$msg}</div>";
            } 
        }    
        return false;
    }

    public function validate($rules){        
       foreach ($rules as $field => $v) {
            // Si un champ est trouvé dans $rules
            // !! $this data est un tableau d'objets (des lignes d'input) indexé numériquement
            // verifier si la cle est numerique 
            foreach ($this->data as $key => $inputs) {
                if(is_numeric ($key)){
                    if(property_exists($inputs, $field)){                    
                        foreach ($v as $rule => $msg) {
                            if(!$this->$rule($field, $inputs->$field)){
                                $this->success = false;
                                $inputs->error = array($field=>$msg);
                                if (!array_key_exists($field[$key], $this->errors)) $this->errors[$field][$key] = $msg;
                            }
                        }
                    }   
                }
                else {
                    // Si la clé n'est pas numérique on n'a pas affaire à un tableau d'objet
                    if (array_key_exists($field, $this->data)){
                        foreach ($v as $rule => $msg) {
                            if(!$this->$rule($field, $this->data[$field])){
                                $this->success = false;
                                if (!array_key_exists($field, $this->errors)) $this->errors[$field] = $msg;
                            }
                        }
                    }
                }
                       
            }
        }      
       return $this->errors;
    }

    /**
    * @param $html string Code HTML à entourer
    * @return string
    */

    protected function surround($html){
        if($this->input_class !=null) {
            $return = '<p class="'.$this->input_class.'">'.$html.'</p>';
            return $return;
        }
        return "<p class=\"field-surround\">{$html}</p>";
    }

    protected function textareaSurround($html){
        return "<div class=\"field field-textarea\">{$html}</div>";
    }

    protected function selectSurround($html){
        return "<div class=\"field select\">{$html}</div>";
    }

    private function setRules($field, $options){
        if(array_key_exists('rules', $options)){
            $this->rules[$field] = $options['rules'];
        }
    }   

    public function hidden($name, $value = null, $options = []): string{
        return '<input type="hidden" name="' . $name . '" value="' . $value . '">';
    }

    public function input3(string $name, array $opts): string
    {  
        $wrap = [];      
        $label = isset($opts['label']) ? $opts['label'] : false ;
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        unset($opts['id']);

        $attributes = ['type' => ' type="text"'];

        if(isset($opts['wrapper'])) {
            $wrap = $opts['wrapper'];
            unset($opts['wrapper']);
        }
        
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
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
            return $this->_withLabel($label, $input, $wrap);
        }
        return $input;
    }


    public function finish($options, $form = null, string $prefix = '')
    {
        if($form !== null) $form = 'form="'.$form.'"';
        else $form = '';

        $input = $this->select('price[finish]',
        array(
            'label' => 'Finition',
            'id'    => $prefix. 'kit-finish',
            'placeholder' => 'Choisir',
            'attributes' => array('class="onchange field-input select"' , 'data-ctrl="option.setFinish"',  $form, 'required'),
            'values' => $options,
            'opt_attributes' => array('data-name' => 'opt_name', 'data-id' => 'id')
        ));
        return $input;
    }

    public function text(string $name, array $opts): string
    {  
        $wrap = [];      
        $label = isset($opts['label']) ? $opts['label'] : false ;
        $for = $opts['id'] ?? $name;
       
        unset($opts['id']);

        $attributes = [];

        if(isset($opts['wrapper'])) {
            $wrap = $opts['wrapper'];
            unset($opts['wrapper']);
        }
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        } 

       

        $o = ['attributes' => $attributes , 'value' => null];  
        $params = array_merge($o, $opts);

        $input = '<textarea name="' . $name .'" id="' . $for . '" ';
        if(isset($params['placeholder']))
            $input .= ' placeholder="' . $params['placeholder'] . '" ';

        $input .= implode(' ', $params['attributes']);
        $input .= ' >';


        if($params['value'] != null) $input .= $params['value'];
        $input .= '</textarea>';       
       

        if($label)
        {

            $label = "<label for=\"{$for}\">{$label}</label>";
            return $this->_withLabel($label, $input, $wrap);
        }
        return $input;
    }


    public function text2(string $name, array $opts): string
    {         
        $wrap = [];      
        $label = isset($opts['label']) ? $opts['label'] : false;
        $id = $opts['id'] ?? $name;               
        unset($opts['id']);
        if(isset($opts['wrapper'])) {
            $wrap = $opts['wrapper'];
            unset($opts['wrapper']);
        }

        $textarea = "<textarea name=\"{$name}\" id=\"{$id}\" ";
        $value = array_key_exists('value', $opts) ? $opts['value'] : '';

        

        $attributes = [];

        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        } 

         

        $o = ['attributes' => $attributes];  
        $params = array_merge($o, $opts);

        
        

        $textarea .= implode(' ', $params['attributes']);
        


        $textarea .= ">";                
        $textarea .= $value;
        $textarea .= "</textarea>";
       

        if($label)
        {

            $label = "<label for=\"{$id}\">{$label}</label>";
            return $this->_withLabel($label, $textarea, $wrap);
        }
        return $textarea;
    }

    public function trix(string $name, array $opts): string
    {         
        $wrap = [];      
        $label = isset($opts['label']) ? $opts['label'] : false;
        $id = $opts['id'] ?? $name;               
        unset($opts['id']);
        if(isset($opts['wrapper'])) {
            $wrap = $opts['wrapper'];
            unset($opts['wrapper']);
        }

        $trix = "<input name=\"{$name}\" id=\"{$id}\" type=\"hidden\" ";
        $value = array_key_exists('value', $opts) ? $opts['value'] : '';
        $trix .= "value=\"$value\" ";
        $attributes = [];

        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        } 

        $o = ['attributes' => $attributes];  
        $params = array_merge($o, $opts);    

        $trix .= implode(' ', $params['attributes']);
        $trix .= " />";        
        $trix .= "<trix-editor input=\"{$id}\"></trix-editor>";
        
        if($label)
        {
            $label = "<label for=\"{$id}\">{$label}</label>";
            return $this->_withLabel($label, $trix, $wrap);
        }
        return $trix;
    }  

    public function date(string $name, array $opts): string
    {        
        $label = isset($opts['label']) ? $opts['label']: false ;
        $for = $opts['id'] ?? $name;
        $id = $opts['id'] ?? $name;
        $min = array_key_exists('min', $opts) ? 'min="'.$opts['min'].'"' : ' ';
        $max = array_key_exists('max', $opts) ? 'max="'.$opts['max'].'"' : ' ';
        unset($opts['id']);
        $attributes = ['type' => ' type="date"'];
        
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes'], [$min, $max]);
            unset($opts['attributes']);
        } 

        $o = ['attributes' => $attributes , 'value' => null];  
        $params = array_merge($o, $opts);

        $input = '<input name="' . $name .'" id="' .$id . '" ';
        $input .= implode(' ', $params['attributes']);

        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';

        $input .= ' />';
        if($label)
        {
            $label = "<label for=\"{$for}\">{$label}</label>";
            return $this->_withLabel($label, $input);
        }
        return $input;
    }


    public function switch(string $name, array $opts = []): string {

        $label = isset($opts['label']) ? $opts['label']: false;        
        $id = $opts['id'] ?? $name;
        unset($opts['id']);
        $attributes = ['type' => 'type="checkbox"']; 
          
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        }

        $o = ['attributes' => $attributes , 'value' => null, 'checked' => 0];  

        $params = array_merge($o, $opts);

        $input = '<span>' . $label . '</span>';
        $input .= '<span class="switch-container check">';
        $input .= '<input name="' . $name .'" id="' .$id . '" ';
        $input .= implode(' ', $params['attributes']);
        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';
        if($params['checked'] == 1)
            $input .= ' checked ';
        $input .= ' />';    
        $label = "<label for=\"{$id}\"></label>";
        $input .= $label;        
        $input .= '</span>';
        return $this->_withContainer($input);
    }

    public function switchy(string $name, array $opts = []): string {

        $wrap = [];
        $label = isset($opts['label']) ? $opts['label']: false;        
        $id = $opts['id'] ?? $name;
        unset($opts['id']);
        $attributes = ['type' => 'type="checkbox"']; 

        if(isset($opts['wrapper'])) {
            $wrap = $opts['wrapper'];
            unset($opts['wrapper']);
        }
          
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        }

        $o = ['attributes' => $attributes , 'value' => null, 'checked' => 0];  

        $params = array_merge($o, $opts);
        
        $input = '<span class="label">' . $label . '</span>';
        $input .= '<span class="switch-container check">';
        $input .= '<input name="' . $name .'" id="' .$id . '" ';
        $input .= implode(' ', $params['attributes']);
        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';
        if($params['checked'] == 1)
            $input .= ' checked ';
        $input .= ' />';    
        $label = "<label for=\"{$id}\"></label>";
        $input .= $label;        
        $input .= '</span>';
        return $this->_switchContainer($input, $wrap);
        
    }

    public function slide(string $name, array $opts = []): string
    {
        $attributes = array_merge(['type' => 'type="checkbox"']);
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        }
        $o = ['attributes' => $attributes , 'value' => null]; 
        $params = array_merge($o, $opts);
        
        $id = isset($params['id']) ? $params['id'] : $name;

        $input  = '<span class="switch-container check">';
        $input .= '<input name="' . $name .'" id="' . $id . '"';
        $input .= implode(' ', $params['attributes']);

        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';

        $input .= ' />';        
        $input .= '<label for="' . $id . '"></label>';
        $input .= '</span>';
        
        return $input;  
    }

    /**
     * Construit un champ de type recherche via ajax
     *
     * @param string $name
     * @param array $opts
     * @return string
     */
    public function datalist(string $name, array $opts): string
    {
        $attributes = array_merge(['type' => 'type="text"']);
        
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        }            

        $o = ['attributes' => $attributes , 'value' => null];  

        $params = array_merge($o, $opts);

        $input = '<input name="' . $name .'" ';
        if(isset($params['placeholder']))
            $input .= ' placeholder="' . $params['placeholder'] . '"';

        $input .= implode(' ', $params['attributes']);

        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';

        $input .= ' />';
        $dataList = '<ul ' . implode(' ', $params['datalist']). '></ul>';
        if(isset($params['label']))
        {
            $label = '<label >' . $params['label'] . '</label>';
            $input = $this->surround($label.$input);
        }
        
        return $input . $dataList;
    }

    public function search(string $name, array $opts): string
    {
        $attributes = array_merge(['type' => 'type="search"']);
        
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        }            

        $o = ['attributes' => $attributes , 'value' => null];  

        $params = array_merge($o, $opts);

        $input = '<input name="' . $name .'" ';
        if(isset($params['placeholder']))
            $input .= ' placeholder="' . $params['placeholder'] . '"';

        $input .= implode(' ', $params['attributes']);

        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';

        $input .= ' />';
        $dataList = '';
        if(isset($params['datalist']))
            $dataList = '<ul class="datalist"></ul>';
        if(isset($params['label']))
        {
            $label = '<label >' . $params['label'] . '</label>';
            $input = $this->surround($label.$input);
        }
        
        return $input . $dataList;
    }

    public function checkbox(string $name, array $opts): string
    {
        $attributes = array_merge(['type' => 'type="checkbox"']);
        
        if(array_key_exists('attributes', $opts)){
            $attributes = array_merge($attributes, $opts['attributes']);
            unset($opts['attributes']);
        }            

        $o = ['attributes' => $attributes , 'value' => null];  

        $params = array_merge($o, $opts);

        $input = '<input name="' . $name .'" ';
        if(isset($params['placeholder']))
            $input .= ' placeholder="' . $params['placeholder'] . '"';

        $input .= implode(' ', $params['attributes']);

        if($params['value'] !== null)
            $input .= ' value="' . $params['value'] . '"';

        $input .= ' />';
        if(isset($params['label']))
        {
            $input .= '<label >' . $params['label'] . '</label>';            
        }
        return $input;
    }

    /**
     * Constuit un element <select>
     *
     * @param string $name
     * @param array $opts
     * @return string
     */
    public function selecta(string $name, array $opts): string
    {
        $params = array_merge(['attributes' => [], 'values' => []], $opts);
        // si on a des attributs spécifiques pour les options
        
        $option_attr = array_key_exists('options', $params);
        $options = [];
        $select = "<select name=\"$name\" ";
        $select  .= implode(' ', $params['attributes']);
        $select .= '>';
        // les options
        if(isset($params['placeholder']))
            $options[] = "<option value >{$params['placeholder']}</option>";
        // La data
            
        foreach($params['values'] as $value)
        {
            $attrs = '';
            if(!is_array($value) && !is_object($value)):
                $val = $value;
                $text = $value;
            elseif(is_array($value)):
                $val = isset($value['value']) ? $value['value'] : $value['text'];
                $text =  $value['text']; 
                if( $option_attr):
                    
                    foreach($params['options'] as $k => $o):
                     $attrs .= "{$k}=\"{$value[$o]}\" "; 
                    endforeach;
                endif;  
            elseif(is_object($value)):
                $val = isset($value->{'value'}) ? $value->value : $value->text;
                if( $option_attr):
                   
                   foreach($params['options'] as $k => $o):
                    $attrs .= "{$k}=\"{$value->$o}\" "; 
                   endforeach;
                endif;
                $text =  $value->text;   
            endif;
            if(isset($params['selected']) && $val == $params['selected']) $selected = 'selected ';
            else $selected = '  ';            
            $widget = "<option value=\"{$val}\" {$selected} {$attrs} >{$text}</option>";
            $options[] = $widget;           
        }
        
        $select .= implode(' ', $options);
        $select .= '</select>';
        return $select;
    } 

    /**
     * Constuit un element <select>
     *
     * @param string $name
     * @param array $opts
     * @return string
     */
    public function select(string $name, array $opts): string
    {
        $wrap = [];
        $label = isset($opts['label']) ? $opts['label']: false ;
        $for = $opts['id'] ?? $name;
        $option_attr = $opts['opt_attributes']?? [];

        if(isset($opts['wrapper'])) {
            $wrap = $opts['wrapper'];
            unset($opts['wrapper']);
        }
        $params = array_merge(['attributes' => [], 'values' => []], $opts);
        $options = [];
        $select = "<select name=\"$name\" ";
        $select .= "id=\"{$for}\" ";
        $select  .= implode(' ', $params['attributes']);
        $select .= '>';

        // les options
        // si on a des attributs spécifiques pour les options
       
        if(isset($params['placeholder']))
            $options[] = "<option value >{$params['placeholder']}</option>";
        // La data
        if($this->is_assoc($params['values']))
        {
            foreach($params['values'] as $k => $value)
            {
                $attrs = '';
                $widget = "<option value=\"{$k}\"";
                if(isset($params['selected']) && $k == $params['selected'])
                    $widget .= " selected >{$value}</option>";
                else
                    $widget .= " >{$value}</option>";
                    $options[] = $widget;
            }
        }
        else
        {
            foreach($params['values'] as $k => $value)
            {  

            $attrs = '';
                // Si on a un simple tableau numérique
                if(!is_array($value) && !is_object($value))
                {
                    // Si on a un parametre integer dans les attributs
                    if(isset($params['valueTypeOf']) && $params['valueTypeOf'] == 'INT')
                    {
                        $widget = "<option value=\"{$k}\"";   
                        if(isset($params['selected']) && $k == $params['selected'])
                            $widget .= " selected >{$value}</option>";
                        else
                            $widget .= " >{$value}</option>";
                    }    
                    else{
                        $widget = "<option value=\"{$value}\"";
                        if(isset($params['selected']) && $value == $params['selected'])
                            $widget .= " selected >{$value}</option>";
                        else
                            $widget .= " >{$value}</option>";
                    }
                    
                    $options[] = $widget;
                }
                // Sinon
                else {
                    if(!is_object($value))
                        $value = (object)$value;
                    if(isset($value->{'value'}))
                    {
                        $widget = "<option value=\"{$value->value}\"";
                                            
                        foreach($option_attr as $k => $o):
                            $widget .= " {$k}=\"{$value->$o}\" "; 
                        endforeach;             
                        
                        if(isset($params['selected']) && $value->value == $params['selected'])
                            $widget .= " selected >{$value->text}</option>";
                        else
                            $widget .= " >{$value->text}</option>";
                        $options[] = $widget;
                    } else
                    {
                        $options[] = "<option value=\"{$value->text}\">{$value->text}</option>";
                    }
                }
            }
        }            
        
        $select .= implode(' ', $options);
        $select .= '</select>';
        if($label)
        {
            $label = "<label for=\"{$for}\">{$label}</label>";
            return $this->_withLabel($label, $select, $wrap);
        }
        return $select;
    } 
    public function textarea($name, $label, $options = []) {
        if(isset($options['class'])) {
            $wrap_class = array_key_exists('wrap',$options['class'] ) ? ' class="field field-textarea '. $options['class']['wrap']. '" ' : ' class=" field field-textarea" '; 
            
            $field_class = array_key_exists('field',$options['class'] ) ? ' class="field-input textarea '. $options['class']['field'] . '" ' : ' class=" field-input textarea" '; 
        }
        if(isset($options['value']) && $this->getValue($name) == null){
           $value = $options['value'];
        } else {
           $value = $this->getValue($name);
        }
        $wrap = '<div ' . $wrap_class . '>';
        $wrap .= '<label class="field-label">' . $label . '</label>';
        
        $wrap .= '<div class="textarea"><textarea  id="' . $name . '" name="' . $name . '"' . $field_class . ' >' . $value . '</textarea></div>';
        $wrap .= '</div>';
        return $wrap;

    }


    public function pell($name, $label, $options = []) {
        $pell = "<div ";
        if(isset($options['class'])) {
            $wrap_class = array_key_exists('wrap',$options['class'] ) ? ' class="field field-textarea '. $options['class']['wrap']. '" ' : ' class=" field field-textarea" '; 
            
            $field_class = array_key_exists('field',$options['class'] ) ? ' class="field-input textarea '. $options['class']['field'] . '" ' : ' class=" field-input textarea" '; 
        }
        if(isset($options['value']) && $this->getValue($name) == null){
           $value = $options['value'];
        } else {
           $value = $this->getValue($name);
        }
        $wrap = '<div ' . $wrap_class . '>';
        $wrap .= '<label class="field-label">' . $label . '</label>';
        
        $wrap .= '<div class="textarea"><textarea  id="' . $name . '" name="' . $name . '"' . $field_class . ' >' . $value . '</textarea></div>';
        $wrap .= '</div>';
        return $wrap;

    }


    

    public function widgetColor($name)
    {
        $widget = '<div class="select" tabindex="0">';
        $widget .= '<span class="value"></span>';
        $widget .= '<ul class="optList hidden">';          
               
        foreach ($this->colors as $color): $color = (object) $color;
            $widget .= '<li class="option color" data-value="'. $color->value . '" style="'. $color->style .'">'. $color->text .'</li>';                
        endforeach;				
        $widget .= '</ul>';
        $widget .='</div>';
        $widget .= '<select name="'. $name .'" form="addCustomToCart">';
		foreach ($this->colors as $color): $color = (object) $color;
			$widget .= '<option value="'. $color->value. '">'. $color->text .'</option>';		
		endforeach;			
        $widget .= '</select>';
        return $widget;
    }

    public function inputColor($search)
    {
        $search = str_replace(' ', '-', mb_strtolower($search));
        $key = array_search($search, array_column($this->colors, 'value'));
        $color = $this->colors[$key];
        return '<span class="element color-element" style="' . $color['style'] . '">'. $color['text'] . '</span>';
    }

    public function widgetSponsors($sponsors,  $img = 'https://www.kutvek-kitgraphik.com/images/charte/gabarits-motocross.png')
    {
        
        $widget = '<div class="places">';
        $widget .= '<img src="' . $img . '" />';
        $widget .= '</div>';
       
        foreach($sponsors as $sponsor)
            {
                
                $widget .= '<div class="places">';
                $widget .= '<span>Emplacement ' . $sponsor->place . '</span>';  
                if(!property_exists($sponsor, 'text') || $sponsor->text === null || empty($sponsor->text))               
                    $text =  'Fichier';
                else
                    $text = $sponsor->text;
                if(!property_exists($sponsor, 'file') || $sponsor->file === null || empty($sponsor->file)):
                    $widget .= '<span>'. $text .'</span>';
                else:
                    $widget .= '<span><a href="'. $sponsor->file.'" target="_blank"> ' . $text . '</a></span>';
                endif;                
                $widget .= '</div>';
            }            
        return $widget;
    }

    public function attachedFiles(?array $files)
    {
       
        if($files === null) return false;

        $widget = '';
        foreach($files as $k => $file)
        {
            $idx = (int)$k + 1;
            $widget .= '<div class="places">';
            $widget .= '<span>Fichier ' . $idx . ': </span>';
            $widget .= '<span><a href="'. $file.'" target="_blank"> fichier-' . $idx . '</a></span>';
            $widget .= '</div>';
        }
        return $widget;
        

    }

    public function inputTypo($img)
    {
        return '<span class="element typo-element"><img src="'. $img . '" /></span>';
    }
    public function inputLogo($img)
    {
        return '<span class="element typo-element"><img src="'. $img . '" /></span>';
    }

    public function inputElement($value)
    {
        return '<span class="element std-element">'. $value. '</span>';
    }

    private function _withLabel($label, $field, array $wrap = [])
    {
        $div = "<div ";

        if(!empty($wrap))
        {
            $div .= implode(' ', $wrap['attributes']);
            $div .= ">{$label}{$field}</div>";
            return $div;    
        }
        $div .= "class=\"field-wrapper\">{$label}{$field}</div>";
        return $div; 

    }

    private function _withContainer(string $input) : string
    {
        return "<p class=\"switch-wrapper\">{$input}</p>";
    }

     private function _switchContainer(string $field, array $wrap = []) : string
    {
        $div = "<div ";
        if(!empty($wrap))
        {
            $div .= implode(' ', $wrap['attributes']);
            $div .= ">{$field}</div>";
            return $div;    
        }
        $div .= "class=\"field-wrapper switch\">{$field}</div>";
        return $div;         
    }




}