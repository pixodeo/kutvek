<?php
declare(strict_types=1);
namespace Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Core\Middleware;
use Core\Domain\Table;
use ReflectionMethod;
/**
 * 	
 */
final class UrlStatus extends Middleware
{
    
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    { 
        /*if(isset($_GET['test'])): 
            $h = print_r($handler->getMiddlewares(),true);
            $c = __CLASS__;
            echo <<<EOT
                <p>$c</p>
                <pre>{$h}</pre>
            EOT;
            
            
            exit;
        endif;   */        
        $this->lang = $request->getAttribute('lang', 'fr'); 
        $this->setRequest($request);
        //$this->_request = $request;
        $this->setI18n($this->lang);
        $this->_response =  $handler->handle($request);       
        $this->__invoke();  
        return $this->_response;
    }

    public function __invoke(){
       
    	$uri = ltrim($this->getRequest()->getUri()->getPath(), '/');
    	$uri = $this->withoutPrefix($uri);
    	$table = new Table($this->_setDb());
    	$sql = "SELECT r.location , r.status_code FROM redirections r WHERE slug = :slug AND l10n = :l10n;";
    	$sth = $table->query($sql, ['slug'=>$uri, 'l10n'=> $this->getL10nId()],true);
    	if($sth) {
    		$location = $this->translation($sth->location);    		
    		$this->_response = $this->_response->withHeader('Location', $location);
    		$this->_response = $this->_response->withStatus(301);   	
            $this->_response = $this->_response->withHeader('Server', 'Peyredragon');       
            $this->_response = $this->_response->withHeader('X-Powered-By', 'Visaerys Targaryen');
            $this->_response = $this->_response->withHeader('X-Redir', $sth->location );	
    	}
        
       
        
        $this->_response = $this->_response->withHeader('X-Middleware',array_merge([ __CLASS__],$this->_response->getHeader('X-Middleware')));     
    	//$this->_response = $this->_response->withStatus(304);
        	
    	
    }

      
}