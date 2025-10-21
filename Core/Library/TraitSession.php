<?php

namespace Core\Library;

trait TraitSession {

    public function session() {
        return $this;
    }  
        
    public function write($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function read($key, ?string $value = null) {
        if($value !== null)
            return isset($_SESSION[$key][$value]) ? $_SESSION[$key][$value] : null;
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }



    public function unset($key) {
        if(isset($_SESSION[$key]))
            unset($_SESSION[$key]);
    }

    public function alerting($alert) {
        die('<pre>'.$alert.'</pre>');
    }

    public function setFlash(string $message, ?string $type) : void {
        $type = isset($type) ? $type : 'success';
        $flash = '<div class="alert alert-'.$type.'">'.$message.'</div>';        
        $_SESSION['flash'] = $flash;
    }
    public function setErrorMsg(string $text): void {
        $msg = '<div class="alert alert-error">'.$text.'</div>'; 
        $_SESSION['flash'] =  $_SESSION['flash'] . $msg;   
    }

    public function setSuccessMsg(string $text): void {
        $msg = '<div class="alert alert-success">'.$text.'</div>'; 
        $_SESSION['flash'] =  $_SESSION['flash'] . $msg; 
    }



    public function hasFlashes() {
        return isset($_SESSION['flash']);
    }

    public function getFlashes($key = null) {

        if($this->hasFlashes()) {            
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
    }
}