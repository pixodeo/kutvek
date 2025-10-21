<?php
/**
 *  Pour les filtres acceptant les options,  utilisez ce format
 *  $options = array(
 *       'options' => array(
 *           'default' => 3, // valeur à retourner si le filtre échoue
 *           // autres options ici...
 *           'min_range' => 0
 *       ),
 *       'flags' => FILTER_FLAG_ALLOW_OCTAL,
 *   );
 * 
 */
namespace Core\Library;
trait TraitValidator{
    public $success = true;
    protected $errors = [];
    private function getField($field)
    {
        if (!isset($this->data[$field])) {
            return null;
        }
        return $this->data[$field];
    }
    public function isFloat($field, $value) {
        if (!filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)){
            return false;                   
        }
        return true;
    }
  
    public function isEmail($field, $value){
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)){
            return false;                   
        }
        return true;
    }

    public function required($field, $value){        
        if (trim($value) == ''){
           return false;                 
        }
        return true;
    }

    /**
     * Checks if a value is a non empty string
     * @param mixed $val Value provided by filter_var
     * @return mixed
     */
    public function FILTER_STRING($val){
        $options = 
            // default options
            array(
                "step"      => 1,
                "default"   =>NULL,    // Value to return on fail
                "strict"    => false,   // Check value for correct type
                "cast"      => false    // Cast the value in a certain type
            );
    
        return trim($val) == '' ? $options["default"] : $val;        
    }
}