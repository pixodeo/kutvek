<?php
declare(strict_types=1);
namespace App\Checkout\Types;
use Core\Action;
use Domain\Table\Checkout;
use Exception;

class ChronoStd extends Action {
	private Checkout $_checkoutTable;	
	protected false|int $userSessionId = false;

	public function __invoke(int $id)
	{
		$this->_checkoutTable = new Checkout($this->_setDb());
		$this->_checkoutTable->beginTransaction();
		try {			
			$post = $this->getRequest()->getParsedBody();
			// check if connected 
			$cookies = $this->getRequest()->getCookieParams();
			$userConnected = $this->getCookie($cookies['session_token']??false);
			if($userConnected) $this->userSessionId = (int)$userConnected->uid;	
			
			$this->_checkoutTable->setRoute($this->_route);
			$this->_checkoutTable->setUserSessionId($this->userSessionId);
			$this->_checkoutTable->setOrderId((int)$id);			

			$address_id = str_replace('d-', '', $post['delivery_address']);			
			$userId = $this->_checkoutTable->userId()??$this->userSessionId;			

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