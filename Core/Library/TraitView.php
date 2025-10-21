<?php

namespace Core\Library;

use Core\Http\Message\Response;
use Core\Routing\{RouterInterface,RouteInterface};
use Psr\Http\Message\{ServerRequestInterface,ResponseInterface};
/**
 * Les fonctionnalités nécéssaires pour le bon affichage d'une vue
 * import css js
 * balises meta...
 */
trait TraitView { 

    protected $title = '';
    protected $description = '';
    protected $view;
    protected $css = [];
    protected $scriptBottom = [];
    protected $layout = 'minimalist';
    protected RouterInterface $_router;
    protected $dedicatedScripts = [];
    protected ResponseInterface $_response;
    protected false|ServerRequestInterface $_request = false;

    public function getMetaTag(string $tag){
        return $this->{$tag};
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title ?? '';
    }

    public function getDescription(){
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description ?? '';
    }

    public function fetch($type){
        $str='';
        foreach ($this->$type as $v) {
            $str.= "$v\n";
        }
       return $str;
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

    public function _render(string $view, $variables = []) : string
    {
        ob_start();        
        extract($variables);
        require(VIEW_PATH . str_replace('.', DS, ucfirst($view)) . '.php');
        $this->view = ob_get_clean();
        ob_start();       
        require(VIEW_PATH . 'Layout' .  DS . $this->layout . '.php');        
        return ob_get_clean();
    }

     /**
     *  du contenu secondaire comme un menu
     * @return string 
     */
    public function partial(string $file, array $variables = []): string { 
        ob_start();        
        extract($variables);
        require(VIEW_PATH . str_replace('.', DS, ucfirst($file)) . '.php');
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
        require(VIEW_PATH . 'Layout' . DS . $this->layout . '.php');      
        return ob_get_clean();
    }

    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }  
    
    public function setRouter(RouterInterface $router):void 
    {
        $this->_router = $router;
    }

    public function getRouter(): RouterInterface
    {
        return $this->_router;
    }

    public function getRoute(): RouteInterface
    {
        return $this->_router->getRoute();

    }  

    public function setRequest(ServerRequestInterface $request):void {
       
        $this->_request = $request;
    }

    public function getRequest(): ServerRequestInterface
    {
        if($this->_request && $this->_request instanceof ServerRequestInterface) return $this->_request;
        return $this->_router->getRequest();
    }

    public function getResponse():Response{
        return $this->_response;
    }

    protected function _xml(string $view, $variables = []) : void 
    {
        header("Content-type: text/xml");
        ob_start();        
        extract($variables);
        require(VIEW_PATH . str_replace('.', DS, ucfirst($view)) . '.php');
        $this->content = ob_get_clean();
        
            require(VIEW_PATH . 'Layout' . DS . $this->layout . '.php');        
    }

     /**
     * Return JSON response
     *
     * @param mixed $response
     * @return void
     */
    public function xhr($response = null): void {
        
        header("Content-type: application/json; charset=utf-8");
        
        if(!is_string($response)) $response = json_encode($response);
        echo $response;
        die();
    } 

    /**
     * Return JSON response without die()
     *
     * @param mixed $response
     * @return void
     */
    public function json($response = null): void {
        
        header("Content-type: application/json; charset=utf-8");
        
        if(!is_string($response)) $response = json_encode($response);
        echo $response;        
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

    /** comme $this->uri mais affiche un préfix différent pour coller aux anciennes url **/
    public function oldUri(string $name, ?array $params = [], string $method = 'GET'): string {
        return $this->uri($name, $params, $method = 'GET');
        $uri=  $this->_router->uri($name, $params, $method);         
       
        // Soit on cherche en-fr et on le dégage ou on le cherche et on met pas de prefix        
        $last = substr($uri, -1);
        if($last == '/') $uri = substr($uri, 0, -1);

        $ds = (\strpos($uri, '/') !== 0) ? DS : '';
        
        $prefix = $this->_oldPrefixUrl ?? $this->_prefixUrl;

        if($prefix){
            return strlen($uri) > 0 ? DS . $prefix .$ds. $uri : DS . $prefix;
        }
        return strlen($uri) > 0 ? $ds . $uri : $ds;        
    }





    /**
     * Génère une balise a avec lien réécrit par le router si existe
     * 
     *
     * @param string $name ex : categories.show
     * @param string $text,  ex 'mon lien' | une image ...
     * @param array $params tableau d'options, les queries, classe, data-attributs (dataset)
     * @return string
     */
    public function link(string $name, string $text, array $params=[], string $method = 'GET'): string {   
        if($this->_router == null) return 'no router';
        //die(var_dump($this->_router, $name, $text, $params));     
        $href = array_key_exists('queries', $params) ? '/'.$this->_router->url($name, $params['queries'], $method) : '/'.$this->_router->url($name, [], $method);
        
        $class = array_key_exists('class', $params) ? 'class="'. $params['class'].'" ' : '';
        if(isset($params['?'])) {
            $queryParams = http_build_query($params['?']);            
            $href .= '?' . $queryParams;
        }
        if(isset($params['datasets']) && count($params['datasets']) > 0)           
            $datasets = implode(' ', $params['datasets']);  
        else
            $datasets = '';      
        $link = '<a href="' . $href . '" ' . $class . $datasets . '>' . $text . '</a>';
        return $link;
    }


    public function css($files, $media = null){
        if(is_array($files)){
            foreach ($files as $k => $v) {
                if($v == null)
                $this->css[] = '<link rel="stylesheet" href="'.$k.'" media="screen"/>'; 
                else 
                $this->css[] = '<link rel="stylesheet" href="'.$k.'" media="'.$v.'"/>';             
            }
        }else {
            if($media != null)
                $this->css[] = '<link rel="stylesheet" href="'.$files.'" media="'.$media.'"/>';

            else   
                $this->css[] = '<link rel="stylesheet" href="'.$files.'" media="screen"/>';
        }
    }

    public function script(string $src, array $attributes = [], $ext = false): void
    {  
        if(!$ext)    
            $script = '<script src="/js/' . $src . '" ';
        else
            $script = '<script src="' . $src . '" ';            
        $script .= implode(' ', $attributes);
        $script .= '></script>';
        $this->scriptBottom[] = $script;        
    }

    protected function js($file, $ext = false, $dedicated = false) {        
        if($ext) $script = '<script src="'.$file.'"></script>';
        else $script = '<script src="/js/'.$file.'"></script>';

        if($dedicated)
        {
            $this->dedicatedScripts[] = $script;
        } else {
            $this->scriptBottom[] = $script;
        }        
    }

    public function addScript(array $files = []){
        if(count($files) == 0) return;
        //<script src="https://unpkg.com/pell@1.0.6/dist/pell.min.js"></script>
        //<script src="/js/pell-init-2.js"></script>
        foreach($files as $file)
        {
            switch ($file['type']) {
                case 'css':
                    if(array_key_exists('media', $file)) $link = "<link rel=\"stylesheet\" href=\"/css/{$file['src']}\" media=\"{$file['media']}\"/>";
                    else $link ="<link rel=\"stylesheet\" href=\"/css/{$file['src']}\" media=\"screen\"/>";
                    $this->css[] = $link;
                    break;
                case 'js':
                    $link = "<script src=\"{$file['src']}\"></script>";
                    $this->scriptBottom[] = $link;
                    break;
            }
        }
        
    }
    
}