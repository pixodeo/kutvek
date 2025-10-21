<?php
declare(strict_types=1);
namespace App;
use Core\Action;
use Library\HTML\{TraitView,Form};
use Component\{Megamenu, WidgetCountries,TopNav,Footer};
use Core\Routing\RouterInterface;

class AppAction extends Action {
	use TraitView;
	public Form $form;

	public function __construct(protected RouterInterface $_router)
	{
		$this->form = New Form;	
		$this->_route = $this->_router->getRoute();     
	}

	public function megamenu(){
		$component = new Megamenu($this->_route);
		$component->setRequest($this->getRequest());

		// $l10n = new stdClass();
		// $l10n->id = $this->getL10nId();
		// $l10n->code = $this->getL10n();
		// $component->setL10n($l10n);
		$component->setRouter($this->_router);
		return $component();
	}


	public function widgetCountries(){
		$component = new WidgetCountries($this->_route);
		$component->setRequest($this->getRequest());
		/*$l10n = new stdClass();
		$l10n->id = $this->getL10nId();
		$l10n->code = $this->getL10n();
		$component->setL10n($l10n);*/
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



}