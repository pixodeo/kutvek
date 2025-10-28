<?php
declare(strict_types=1);
namespace App\Checkout\Types;
use Core\Action;
use Exception;
use Library\Chronopost\RelayPointInfo;
use Domain\Table\Checkout;

class ChronoRelay extends Action {
	private Checkout $_checkoutTable;	
	protected false|int $userSessionId = false;
	public function __invoke(int $id)
	{
		$this->_checkoutTable = new Checkout($this->_setDb());
		$this->_checkoutTable->beginTransaction();
		try {
			$queries = $this->getRequest()->getQueryParams(); 
			$post = $this->getRequest()->getParsedBody();
			// check if connected 
			$cookies = $this->getRequest()->getCookieParams();
			$userConnected = $this->getCookie($cookies['session_token'] ?? false);
			if($userConnected) $this->userSessionId = (int)$userConnected->uid;	
			
			$this->_checkoutTable->setRoute($this->_route);
			$this->_checkoutTable->setUserSessionId($this->userSessionId);
			$this->_checkoutTable->setOrderId((int)$id);
			

			$component = new RelayPointInfo($this->_route);
			$relay = $component($post['delivery_address']);
			$address = [				
				'line1'			=> (string)$relay->adresse1,
				'line2' 		=> (string)$relay->adresse2,				
				'zipcode' 	 	=> (string)$relay->codePostal,
				'country' 		=> $this->_checkoutTable->countryId((string)$relay->codePays),
				'country_code'	=> (string)$relay->codePays,
				'city' 	 		=> (string)$relay->localite,
				'relay_name' 	=> (string)$relay->nom,
				'relay_id'	 	=> (string)$relay->identifiant	
			];
			$address = array_filter($address);
			$this->_checkoutTable->setTable('addresses');
			$this->_checkoutTable->create($address);

			$address_id = $this->_checkoutTable->lastInsertId();
			$address['id'] = $address_id;	
			$userId = $this->_checkoutTable->userId()??$this->userSessionId;		
			
			$address_user = [
				'user' => $userId,
				'address' => $address_id,
				'is_active' => '0'
			];
			$this->_checkoutTable->setTable('address_user');
			$this->_checkoutTable->create($address_user);

			$order = [
				'id_user'			=> $userId,
				'delivery_address' 	=> $address_id,
				'delivery_type' 	=> $post['delivery_type'],
				'com_shipping' 		=> $post['delivery']['cost']
			];
			$this->_checkoutTable->setTable('_order');
			$this->_checkoutTable->update($id, $order);

			$this->_checkoutTable->commit();
			$this->_response = $this->_response->withStatus(303);
			$location = $this->url('checkout.payment', ['fqdn' => 1,'queries'=>['id'=> $id]]);
			$this->_response = $this->_response->withHeader('Location', $location);			
			return $this->_response;
		}
		catch(Exception $e){
			$this->_checkoutTable->rollback();
			$this->_response = $this->_response->withStatus(400);
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
			$this->_response->getBody()->write(json_encode(['msg' => $e->getMessage()]));
			return $this->_response;
		}
	}
}