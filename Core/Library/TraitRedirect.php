<?php

namespace Core\Library;

trait TraitRedirect { 

    public function redirect($url, $status = null){
        if($status) {
            header($this->status[$status]);
        }
        header("Location: $url");
        exit();
    }    
}