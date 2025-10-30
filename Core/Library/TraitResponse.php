<?php
declare(strict_types=1);
namespace Core\Library;

use Core\Responder;
use Core\Routing\{RouterInterface,RouteInterface};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait TraitResponse {  
    protected RouterInterface $_router;
    protected RouteInterface $_route;
    protected ServerRequestInterface $_request;
    protected ResponseInterface $_response; 
    protected Responder | false $_responder =  false;     
    protected $_layout;
    protected $_view;
    protected $_content;
    protected $_path;
    protected array $css = [];
    protected $scriptBottom = []; 
    protected $_payload;   
    
    
    /**
     * Return HTML with only layout, the content is created by the Controller
     *
     * @return     string  ( description_of_the_return_value )
     */
    protected function _print(array $variables = []): string {
        return $this->getBody($variables);
    }

    public function getBody(array $variables = []): string {
        ob_start(); 
        extract($variables);
        if(strpos($this->_layout, '.')) $this->_layout = ucfirst($this->_layout);

        if(!$this->_path) $dir = LAYOUT_PATH;
        else $dir = $this->_path.DS.'View'.DS.'Layout'. DS;

        require($dir . $this->_layout .  '.php');      
        return ob_get_clean();
    }

    /**
     *  du contenu secondaire comme un menu
     * @return string 
     */
    public function partial(array $variables = []): string { 
        ob_start();        
        extract($variables);
        if(strpos($this->_view, '.')) $this->_view = ucfirst($this->_view);
        
        if(!$this->_path) $dir = VIEW_PATH;
        else $dir = $this->_path.DS.'View'.DS;

        require($dir .  str_replace('.', DS, $this->_view) . '.php');
        return ob_get_clean();
    }

    public function uri(string $name, ?array $params = [], string $method = 'GET'): string {
        return $this->url($name, $params, $method);      
    }

    public function url(string $name, ?array $params = [], string $method = 'GET'): string {
        $uri =  $this->_router->uri_2($name, $params, $method);         
        return $uri;             
    }

    /**
     *  Given a valid file location (it must be an path starting with "/"), i.e. "/css/style.css",
     *  it returns a string containing the file's mtime, i.e. "/css/style.0123456789.css".
     *  Otherwise, it returns the file location.
     *
     *  @param $file  the file to be loaded.
     */
    public function auto_version($file) {
        // if it is not a valid path (example: a CDN url)
        if (strpos($file, '/') !== 0 || !file_exists(WEBROOT . $file)) return $file;

        // retrieving the file modification time
        // https://www.php.net/manual/en/function.filemtime.php
        $mtime = filemtime(WEBROOT . $file);

        return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
    } 
}