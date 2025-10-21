<?php
declare(strict_types=1);
namespace Core;

use Core\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 *  Envoi la réponse formatée remplace la partie render des controllers.
 *  Utilisé par les actions
 */
abstract class Responder  {
	use \Core\Library\TraitCore;

	protected RouterInterface $_router;
	protected ServerRequestInterface $_request;
    protected ResponseInterface $_response;	
    protected $_payload;
    protected $_layout;
    protected $_view;
    protected $_content;
    protected $_path;
    protected $css = [];
    protected $scriptBottom = [];


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

    public function setRouter(RouterInterface $router): void 
    {
        $this->_router = $router;
    }

    public function getRouter(): RouterInterface
    {
        return $this->_router;
    }

    public function setRequest(ServerRequestInterface $request): ServerRequestInterface {        
        $this->_request = $request;
        return $this->_request;
    }

    public function getRequest(): ServerRequestInterface
    {        
        return $this->_request;
    }
    public function getResponse():ResponseInterface{
        return $this->_response;
    }

    public function setResponse(ResponseInterface $response){
        $this->_response = $response;        
        return $this->_response;
   	}     

    public function setPath(string $filename){
        return dirname($filename);
    } 

    protected function _render($variables = []) : string
    {
        
        ob_start(); 
        extract($variables);
        $this->_content = $this->partial($variables);
        require($this->_path . DS . 'View' . DS .  'Layout' . DS . $this->_layout . '.php');
        return ob_get_clean();
    }

    /**
     * Return HTML with only layout, the content is created by the Controller
     *
     * @return     string  ( description_of_the_return_value )
     */
    protected function _print(array $variables = []): string {
        ob_start(); 
        extract($variables);
        require($this->_path . DS . 'View' . DS .  'Layout' . DS . $this->_layout . '.php');      
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

        require($this->_path . DS . 'View' . DS . str_replace('.', DS, $this->_view) . '.php');
        return ob_get_clean();
    }

    public function fetch($type){
        $str='';
        foreach ($this->$type as $v) {
            $str.= "$v\n";
        }
       return $str;
    }

     public function uri(string $name, ?array $params = [], string $method = 'GET'): string {
        $uri=  $this->_router->uri($name, $params, $method);         
       
        // Soit on cherche en-fr et on le dégage ou on le cherche et on met pas de prefix        
        $last = substr($uri, -1);
        if($last == '/') $uri = substr($uri, 0, -1);

        $ds = (\strpos($uri, '/') !== 0) ? DS : '';
        
        if($this->_prefixUrl){
            return strlen($uri) > 0 ? DS . $this->_prefixUrl .$ds. $uri : DS . $this->_prefixUrl;
        }
        return strlen($uri) > 0 ? $ds . $uri : $ds;        
    }  

    public function __set(string $name, mixed $value):void {
        $this->{$name} = $value;
    }



}