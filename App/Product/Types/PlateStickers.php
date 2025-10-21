<?php
declare(strict_types=1);
namespace App\Product\Types;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionMethod;

/**
 * Pattern Strategy
 * This class is the concrete Strategy
 */
final class PlateStickers extends Type {
    

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $matches = $request->getAttribute('matches');       
        $this->setRequest($request);        
        

        $countryCurrencyToken = $this->getRequest()->getCookieParams()['country_currency'] ?? false;        
        if ($countryCurrencyToken) {
           $cookie = $this->getCookie($countryCurrencyToken);
           //$this->setCountry($cookie->country);
           //$this->setCurrency($cookie->currency);                   
        }
        
        $this->_response = $handler->handle($request);
        $reflection = new ReflectionMethod($this, '__invoke');
        
        $parameters = $reflection->getParameters();
        if(count($matches) > 0):
            if(count($matches) !== count($parameters)):
                $query_params = [];
                $parameters_keys = array_column($parameters, 'name');
                $matches_keys = array_keys($matches);
                $diff = array_diff($matches_keys, $parameters_keys);
                foreach($diff as $key):
                    $query_params[$key] = $matches[$key];
                    unset($matches[$key]);
                endforeach;                
                // On combine les queries existantes avec les nouvelles
                $query_params = array_merge($this->getRequest()->getQueryParams(),$query_params);
                // On met à jour l'objet Request
                $this->setRequest($this->getRequest()->withQueryParams($query_params));        
            endif;
            $reflection->invokeArgs($this, $matches);
        else:
            $reflection->invoke($this);            
        endif;  

        return $this->_response;      
    }


   public function __invoke(){      
        //$this->product = $this->_table->product($id);
        $this->_path = dirname(__FILE__,2);
        $this->_view = 'plates.cfs';
        $table = $this->getDomain('Product');
        $reinsurance_items = $table->reinsuranceOnCart();
        $this->_content = $this->partial(compact('reinsurance_items'));
        $this->_path = false;
        $this->_layout = 'basics';
        $this->_response->getBody()->write($this->_print());
        return $this->_response;
   }
}