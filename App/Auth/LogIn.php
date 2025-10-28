<?php
declare(strict_types=1);
namespace App\Auth;

use App\AppAction;
use Error;
use Exception;
use Library\Security\Cookie;

/**
 * Ajoute une adresse de livraison à domicile 
 */
class LogIn extends AppAction {

	public function __invoke(){

		// Vérifier si on est connecté
		$cookies = $this->getRequest()->getCookieParams();
		$cookieClass = new Cookie();
		if($cookieClass->isValid($cookies['session_token'] ?? false)):
			$location = $this->url('customer.dashboard', ['fdqn' => 1]);
			$this->_response =  $this->_response->withStatus(302);
			$this->_response = $this->_response->withHeader('location', $location);
			return $this->_response;
		endif;
		$this->_layout = 'login';
		$this->_view = 'identities.login';
		$this->_content = $this->partial(compact('cookies'));
		$this->_response->getBody()->write($this->_print());
		return $this->_response;
	}
}