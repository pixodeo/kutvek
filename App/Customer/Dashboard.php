<?php
declare(strict_types=1);
namespace App\Customer;

use App\AppAction;
use Error;
use Exception;
use Library\Security\Cookie;

/**
 * Ajoute une adresse de livraison à domicile 
 */
class Dashboard extends AppAction {
	
	public function __invoke(){
		// Vérifier si on est connecté
		$cookies = $this->getRequest()->getCookieParams();
		$cookieClass = new Cookie();
		if(!$cookieClass->isValid($cookies['session_token'] ?? false)):
			$location = $this->url('auth.logIn');			
			$this->_response =  $this->_response->withStatus(401); 
			$this->_layout = 'login';
			$this->_view = 'identities.login';
			$this->_content = $this->partial(compact('cookies'));
			$this->_response->getBody()->write($this->_print());
			return $this->_response;	
		endif;
		$this->_response->getBody()->write('Espace Client');
		return $this->_response;			

	}
}