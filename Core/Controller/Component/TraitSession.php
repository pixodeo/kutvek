<?php
namespace Core\Controller\Component;
trait TraitSession{

    public function session(){
        return $this;
    }

    public function setFlash($key, $message){
        $_SESSION['flash'][$key] = $message;
    }

    public function hasFlashes(){
        return isset($_SESSION['flash']);
    }

    public function getFlashes(){
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    public function write($key, $value){
        $_SESSION[$key] = $value;
    }

    public function read($key){
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function unsetKey($key){
        unset($_SESSION[$key]);
    }

    public function alerting($alert){
        die('<pre>'.$alert.'</pre>');
    }



}