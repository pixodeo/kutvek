<?php
declare(strict_types=1);
namespace App\Product\Types;
use Core\Component;

use Psr\Http\Server\MiddlewareInterface;
use Library\{TraitCart,HTML\TraitView,HTML\Form,HTML\TraitSanitize, TraitFiles};
use Component\{Megamenu, WidgetCountries,TopNav,Footer};
use Domain\Table\Product;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionMethod;

/**
 * Pattern Strategy 
 * This class is the Interface 
 */
abstract class Type extends Component implements  MiddlewareInterface{ 
	use TraitSanitize,TraitView,TraitCart, TraitFiles;

	protected $product;	
	protected array $finishes = [];
    protected array $premiums = [];
    protected array $years = []; 
    public array $uploadedFiles = [];   
   	public function setProduct(object $product):void {$this->product = $product;}
   	public Form $form;
   	public Product $_table;

   	
    public function widgetVehicles(){
        return '';
    }
    public function widgetFinish()
    {        
        return '';
    }

    public function premium()
    {        
        return '';
    }  

     /**
     * Ajoute 2 inputs hidden pour envoyer les infos du premium choisi (id, name)
     */
    public function premiumInfo(){        
        return '';
    } 
    /**
     * Ajoute 2 inputs hidden pour envoyer les infos de la finition choisie (id, name)
     */
    public function finishInfo(){        
        return '';
    }

    public function itemTypeInfo(){
        return '';
    }

     /**
     * Construit le sku soit année . design . color
     *
     * @return     bool  ( description_of_the_return_value )
     */
    public function sku(){      
        return '';
    }

   	public function megamenu(){
		$component = new Megamenu($this->_route);
		$component->setRequest($this->getRequest());		
		$component->setRouter($this->_router);
		return $component();		
	}

	public function widgetCountries(){
		$component = new WidgetCountries($this->_route);
		$component->setRequest($this->getRequest());		
		$component->setRouter($this->_router);
		return $component();
	}

	public function topNav(){
		$component = new TopNav($this->_route);
		$component->setRequest($this->getRequest());
		$component->setRouter($this->_router);
		return $component();
	}

	public function footer(){
		$component = new Footer($this->_route);
		$component->setRequest($this->getRequest());
		$component->setRouter($this->_router);
		return $component();
	}

	/**
	 * Self Canonical
	 */
	public function canonical() {
		$url = $this->getRequest()->getUri()->getPath();
		return '<link rel="canonical" href="' . DOMAIN . $url .'"/>';
	}


	/**
	 * hreflangs
	 *
	 * @param      array   $slugs  The slugs
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	//link rel="alternate" href="https://example.com/en-us" hreflang="en-us" />
	//<link rel="alternate" href="https://example.com/en-au" hreflang="en-au" />
	public function alternate(array $slugs = []) {
		//print_r($slugs);
		$links = [];
		foreach($slugs as $l10n)
		{
			if($l10n->_locale == 'en' || $l10n->l10n === 3 ):
				$links[] = '<link rel="alternate" href="' . DOMAIN .DS . $l10n->slug .'" hreflang="x-default" />';		
			endif;
			$links[] = '<link rel="alternate" href="' . DOMAIN .DS . $l10n->slug .'" hreflang="' . mb_strtolower($l10n->_locale??'') .'" />';				
		}

		if(count($links)>0)
			return implode("\n", $links);		
			return "\n";
	}

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
}