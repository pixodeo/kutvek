<?php

namespace Core\Component;

use Core\Routing\RouterInterface;

/**
 * Les fonctionnalités nécéssaires pour le bon affichage d'une vue
 * import css js
 * balises meta...
 */
trait TraitView { 

    protected $title = '';
    protected $description = '';
    protected $css = [];
    protected $scriptBottom = [];
    protected $layout = 'Erp/dashboard';
    protected $_router;

    
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

    public function viewPath(){
        return str_replace(['Strategies', 'Strategy', 'Behavior'], ['View', 'View', 'View'],dirname($this::VIEW_PATH));
    }

    public function _render(string $view, $variables = []) : string
    {
        ob_start();        
        extract($variables);
        require(VIEW_PATH . str_replace('.', DS, ucfirst($view)) . '.php');
        $this->view = ob_get_clean();
        ob_start();       
        require(VIEW_PATH . str_replace('.', DS, $this->_layout) . '.php');        
        return ob_get_clean();
    }

    /**
	 * [render description]
	 * @param  string       $view      [description]
	 * @param  bool|boolean $ajax      [description]
	 * @param  array|null   $variables [description]
	 * @return string                  [description]
	 */
    protected function render(string $view, array $variables = []): string{
        $this->content = $this->partial($view, $variables);
        ob_start(); 
        require(VIEW_PATH . 'Layout' . DS . $this->layout . '.php');      
        return ob_get_clean();
    }

    /**
     *  du contenu secondaire comme un menu
     * @return string 
     */
    public function partial(string $file, array $variables = []): string { 
        ob_start();        
        extract($variables);        
        require($this->viewPath() . DS . str_replace('.', DS, $file) . '.php');
        return ob_get_clean();
    }  

    protected function getView($view, $ajax = false, $variables = null){
        ob_start();
        if($variables){
        extract($variables);
        }
        if($ajax){
            require(VIEW_PATH . str_replace('.', DS, ucfirst($view)) . '.html');
        }else{
            require(VIEW_PATH . str_replace('.', DS, ucfirst($view)) . '.php');
        }
        return ob_get_clean();
    }

    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }

    public function getRouter(): RouterInterface
    {
        return $this->_router;
    }  
    
    public function setRouter(RouterInterface $router):void 
    {
        $this->_router = $router;
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


    public function script(string $src, array $attributes = []): void
    {      
        $script = '<script src="/js/' . $src . '" ';
        $script .= implode(' ', $attributes);
        $script .= '></script>';
        $this->scriptBottom[] = $script;        
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

    public function uri_0(string $name, ?array $params = [], string $method = 'GET'): string {
       $url =  '/'. $this->_router->uri($name, $params, $method);
       return $url;      
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
        $href =  $this->uri($name, $params, $method);
        
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

    public function setCss($files, $media = null){
        if(is_array($files)){
            foreach ($files as $k => $v) {
                if($v == null)
                $this->css[] = '<link rel="stylesheet" href="/css/'.$k.'" media="screen"/>'; 
                else 
                $this->css[] = '<link rel="stylesheet" href="/css/'.$k.'" media="'.$v.'"/>';             
            }
        }else {
            if($media != null)
                $this->css[] = '<link rel="stylesheet" href="/css/'.$files.'" media="'.$media.'"/>';

            else   
                $this->css[] = '<link rel="stylesheet" href="/css/'.$files.'" media="screen"/>';
        }
    }

    public function addScript(array $files = []){
        if(count($files) == 0) return;
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


     protected function css($files, $media = null){
        if(is_array($files)){
            foreach ($files as $k => $v) {
                if($v == null)
                $this->css[] = '<link rel="stylesheet" href="/css/'.$k.'" media="screen"/>'; 
                else 
                $this->css[] = '<link rel="stylesheet" href="/css/'.$k.'" media="'.$v.'"/>';             
            }
        }else {
            if($media != null)
                $this->css[] = '<link rel="stylesheet" href="/css/'.$files.'" media="'.$media.'"/>';

            else   
                $this->css[] = '<link rel="stylesheet" href="/css/'.$files.'" media="screen"/>';
        }
    }
}