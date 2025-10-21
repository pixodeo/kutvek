<?php
namespace Middleware;

use Core\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use App\Page\Read;
use Core\Domain\Table;

/**
 * 	$handler = Core\Routing\Route;
 */
class IsPage extends  Middleware
{
    private Table $_table;       

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {        
        $this->_table = new Table($this->_setDb());
        $this->_table->setRoute($handler);
        $this->lang = $request->getAttribute('lang', 'fr'); 
        $this->setRequest($request);       
        $this->setI18n($this->lang);
       
        $url = ltrim($this->getRequest()->getUri()->getPath(), '/');
        $sql = "SELECT p.id
            FROM page_l10ns AS p_l10n
            JOIN pages p ON (p.id = p_l10n.page AND p.website = :website)        
            WHERE p_l10n.slug = :slug
            AND p_l10n.l10n = :l10n_id;";
        $query = $this->_table->query($sql, ['slug' => $url, 'l10n_id' => $this->getL10nId(), 'website' => WEBSITE_ID], true);       
        if($query):               
            $this->_middleware = new Read($this->_router);
            $handler->unpipe();                 
        endif; 
        $this->_response  = $handler->handle($request);             
        $this->handle($this->_request);       
        $this->_response = $this->_response->withHeader('X-Middleware',array_merge([ __CLASS__],$this->_response->getHeader('X-Middleware')));
        return $this->_response;   
    }    
}