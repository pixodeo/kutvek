<?php
namespace Core\Component;


abstract class Widget 
{
    
    protected $_data;
    protected $_form;
    protected $_extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'JPG', 'PNG', 'JPEG', 'GIF', 'WEBP'];   

    public function __construct($data)
    {
          $this->_data = $data;      
    } 

    public function setForm($form)
    {
        $this->_form = $form;
    }    
}