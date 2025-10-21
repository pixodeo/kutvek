<?php
namespace Domain;
/**
 * Class Autoloader
 * 
 */
class Autoloader 
{
    /**
     * Enregistre notre autoloader
     */
    static function register(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }
    /**
     * Inclue le fichier correspondant à notre classe
     * @param $class string Le nom de la classe à charger
     */
    static function autoload($class) {       
        if (strpos($class, __NAMESPACE__ . '\\') === 0){

            $class = str_replace(__NAMESPACE__ . '\\', '', $class);
            $class = str_replace('\\', '/', $class);
            $file =  __DIR__ . '/' . $class . '.php'; 

            if(file_exists($file)) {
               
                require __DIR__ . DS . $class . '.php';   
            } else {
                 throw new \Exception('Le fichier '. $class .' n\'existe pas !');     
            }          
        } 
    }
}