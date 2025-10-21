<?php
namespace Middleware;

use Core\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Product\Read AS Product;

use Core\Domain\Table;

/**
 * 	$handler = Core\Routing\Route;
 */
class Dispatch extends  Middleware
{
    private Table $_table;       

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {        
        $this->_table = new Table($this->_setDb());
        $this->_table->setRoute($handler);
        $this->lang = $request->getAttribute('lang', 'fr'); 
        $this->setRequest($request);       
        $this->setI18n($this->lang);
        $matches = $handler->getMatches();        
        
        switch($handler->callable):
            case 'products.product':
                $this->_middleware = new Product($this->_router); 
                $this->_middleware->setQueryResult(json_decode(json_encode($matches)));      
                $handler->unpipe();              
                break;
            case 'products.section':
                // ici on vérif qu'on est pas sur un produit avec ancienne url
                $sql = "SELECT i.id 
                    FROM product_urls p_url
                    JOIN items i ON i.id = p_url.item            
                    WHERE p_url.url = :slug
                    AND p_url.l10n = :l10n_id;"; 
                    $slug = str_replace('.html','',$matches['slug']);
                    $q = $this->_table->query($sql, ['slug'=> $slug, 'l10n_id' => $this->getL10nId()],true);
                    if($q):
                        $this->_middleware = new Product($this->_router); 
                        $matches['id'] = $q->id;
                        $params =array_filter($matches);

                        $this->_middleware->setQueryResult(json_decode(json_encode($params)));   
                        $handler->unpipe();                                      
                    endif;  
                break;
        endswitch;    
        $this->_response  = $handler->handle($request);
        $this->handle($this->_request);
        $this->_response = $this->_response->withHeader('X-Ctrl', $handler->callable);  
        $this->_response = $this->_response->withHeader('X-Middleware',array_merge([ __CLASS__],$this->_response->getHeader('X-Middleware')));
        return $this->_response;          
    }    
}