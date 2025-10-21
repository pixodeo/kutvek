<?php

namespace Core\Library;

/**
 * Les fonctionnalités nécéssaires pour le bon affichage d'une vue
 * import css js
 * balises meta...
 */
trait TraitController { 

    protected $title = '';
    protected $description = '';
    protected $css = [];
    protected $scriptBottom = [];
    // Scripts js qu'on doit rajouter en toute fin
    protected $dedicatedScripts = [];

    public function fetch($type){
        $str='';
        foreach ($this->$type as $v) {
            $str.= "$v\n";
        }
       return $str;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
    
}