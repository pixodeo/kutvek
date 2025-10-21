<?php
//namespace App;
use Core\Config;
use Core\Database\MysqlDatabase;

class App {

    private $db_instance = [];
    private static $_instance = null;
   
    public static function getInstance(){
        if(is_null(self::$_instance)){
            self::$_instance = new App();
        }
        return self::$_instance;
    }
    
    public static function load(){  
             
        // autoriser CORS
        self::cors();        
       
        require APP.DS.'Autoloader.php';
        App\Autoloader::register();

        require CORE.DS.'Autoloader.php';
        Core\Autoloader::register();  

        require LIBRARY.DS.'Autoloader.php';
        Library\Autoloader::register();

        require VENDOR.DS.'Autoloader.php';
        Vendor\Autoloader::register();  

        require COMPONENT.DS.'Autoloader.php';
        Component\Autoloader::register();  

        require PSR.DS.'Autoloader.php';
        Psr\Autoloader::register();  

        require _DOMAIN_.DS.'Autoloader.php';
        Domain\Autoloader::register(); 

        require MIDDLEWARE.DS.'Autoloader.php';
        Middleware\Autoloader::register();                       
    }

    public function getTable($name){
        // si $name est un object
        if(is_object($name)) {
         $class_name = '\\App\\Model\\Table\\' . ucfirst($name->table) . 'Table';
         return new $class_name($this->setDb($name->config?? null));
        }else {
             $class_name = '\\App\\Model\\Table\\' . ucfirst($name) . 'Table';
             return new $class_name($this->setDb());
        }      
    }
   

    // Voir à passer getDb dans le controlleur Core\Controller    
    public function setDb($dbConf = null){
        if($dbConf !== null)  $config = Config::getInstance(CONFIG.DS.$dbConf.'.php');          
        else  $config = Config::getInstance(CONFIG.DS.'DbConf.php');                  
                    
        if(!isset($this->db_instance[$config->get('db_name')])) {
            $this->db_instance[$config->get('db_name')] = new MysqlDatabase($config->get('db_name'), $config->get('db_user'), $config->get('db_pass'), $config->get('db_host'), $config->get('db_port'), $config->get('charset'));
        }
        return $this->db_instance[$config->get('db_name')];       
    }

    private static function cors() {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }

        //echo "You have CORS!";
    }

   

}