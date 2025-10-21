<?php
declare(strict_types=1);
namespace Component;

use Core\Component;
use Domain\Table\Catalog;

final class Megamenu extends Component {

	public $cookie;

	public function __invoke()
	{		
		$cookie = $this->getRequest()->getCookieParams()['country_currency'] ?? false;
		if($cookie)$this->cookie = $this->getCookie($cookie);
		else $this->cookie = (object)['country' => $this->getCountryCode(), 'currency' => $this->getCurrencyCode()];		
		$table = new Catalog($this->_setDB());		
		$table->setRoute($this->_route);
		
		$items = $table->megamenu();		
		$this->_view = 'partials.megamenu';
		return $this->partial(compact('items', 'cookie'));
		//return $items;
	}

}