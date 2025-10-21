<?php
namespace Core\Library;
trait TraitAcl{

    /*static $instance;

    static function getInstance(){
        if(!self::$instance){
            self::$instance = new Session();
        }
        return self::$instance;
    }

    public function __construct(){
        session_start();
    }*/
    

    public function acl(){
        return $this;
    }



    public function check(){
        // verification des permissions
      return true;
        //die($this->session()->read('auth'));

    }
    



}