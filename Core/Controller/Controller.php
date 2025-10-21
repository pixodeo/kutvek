<?php
namespace Core\Controller;

/**
 *          
 */

abstract class Controller {
    
    protected $template;    
    private $status = [
        303=>"HTTP/1.1 303 See Other",
        301=>"HTTP/1.1 301 Moved Permanently",
        302=>"HTTP/1.1 302 Moved Temporarily",
        404=>'HTTP/1.0 404 Not Found',
        410=>'HTTP/1.0 410 Gone'];
    use  \Core\Library\TraitUtils, \Core\Library\TraitView;   
}