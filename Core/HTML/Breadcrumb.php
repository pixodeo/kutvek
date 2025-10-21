<?php

namespace Core\HTML;

/**
 * ClassBreadcrumb
 * Génère un fil d'ariane
 */

 class Breadcrumb
 {
    private $_data = [];

    public function setData(array $data)
    {
        $this->_data = $data;
    }

    public function setRow($data)
    {
        $this->_data[] = $data;
        return $this;
    }

    public function __toString()
    {
        if(empty($this->_data)) return '<nav aria-label="breadcrumb"></nav>';
        
        // le dernier item 
        $last = array_pop($this->_data);

        $output = '<nav aria-label="breadcrumb">';
        $output .= '<ol class="breadcrumb">';
        foreach($this->_data as $item)
        {
            $output .= '<li class="breadcrumb-item">';
            $output .= $item;
            $output .= '</li>';
        }
        $output .= '<li class="breadcrumb-item active" aria-current="page">';
        $output .= $last;
        $output .= '</li>';    
        $output .= '</ol></nav>';
        return $output;
    }

    public function __invoke()
    {
        if(empty($this->_data)) return '<nav aria-label="breadcrumb"></nav>';
        
        // le dernier item 
        $last = array_pop($this->_data);

        $output = '<nav aria-label="breadcrumb">';
        $output .= '<ol class="breadcrumb">';
        foreach($this->_data as $item)
        {
            $output .= '<li class="breadcrumb-item">';
            $output .= $item;
            $output .= '</li>';
        }
        $output .= '<li class="breadcrumb-item active" aria-current="page">';
        $output .= $last;
        $output .= '</li>';    
        $output .= '</ol></nav>';
        return $output;
    }

 }