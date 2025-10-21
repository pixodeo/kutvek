<?php
declare(strict_types=1);
namespace Component;

use Core\Component;
use Domain\Table\Catalog;

final class WidgetCountries extends Component {
	public $cookie;

	public function __invoke()
	{		
		$cookie = $this->getRequest()->getCookieParams()['country_currency'] ?? false;
		if($cookie)$this->cookie = $this->getCookie($cookie);
		else $this->cookie = (object)['country' => $this->getCountryCode(), 'currency' => $this->getCurrencyCode()];		
		$table = new Catalog($this->_setDB());
		$table->setRoute($this->_route);		
		$countries = $table->countries();	
		$currentCurrency = "";
		$currentCountry = null;		
		foreach ($countries as $country) {
			if ($country->country_code === $this->cookie->country) {
				$currentCountry = $country;
				$currentCurrency = $country->currency_name;
				break;
			}
		}	
		$this->_view = 'partials.widgets.country-selector';
		return $this->partial(compact('currentCountry', 'currentCurrency'));		
	}
}