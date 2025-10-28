<?php
declare(strict_types=1);
namespace App\Checkout;

use App\AppAction;
use Domain\Table\Checkout;
use Error;
use Exception;
use Library\HTML\TraitSanitize;

/**
 * Ajoute une adresse de livraison à domicile 
 */
class AddShippingAddress extends AppAction {
	use TraitSanitize;
	private Checkout $_checkoutTable;
	protected int $orderId;

	public function __invoke(int $id)
	{
		$this->_checkoutTable = new Checkout($this->_setDb());
		$this->_checkoutTable->setRoute($this->_route);	
		$this->_checkoutTable->beginTransaction();
		$this->orderId = $id;
		try {			
			$method =  strtolower($this->getRequest()->getMethod());
			match($method){
				'post' => $this->_save(),
				default => $this->_popup()
			};
		}
		catch(Error | Exception $e){
			$this->_checkoutTable->rollback();
			$this->_response = $this->_response->withStatus(400); 
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
			$body = json_encode(['msg' => $e->getMessage()]);			
			$this->_response->getBody()->write($body);
			return $this->_response;
		}	
	}

	private function _popup(){
		try{
			$countries = $this->_checkoutTable->countries();
			$this->_view = 'checkout.add-shipping-address';
			$this->_checkoutTable->commit();
			return $this->_response->getBody()->write($this->partial(compact('countries')));
		} catch(Error | Exception $e){throw $e;}
	}

	private function _save(){
		try {
			$post = $this->getRequest()->getParsedBody();
			$this->_response = $this->_response->withStatus(201); 
			

			$filters = [
				'firstname' 		=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => $this->sanitizeSpecialChars($val, false) ],
				'lastname' 			=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => $this->sanitizeSpecialChars($val, false) ],
				'company' 			=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => $this->sanitizeSpecialChars($val, false) ],
				'address_line_1' 	=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => $this->sanitizeSpecialChars($val, false) ],
				'address_line_2' 	=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => $this->sanitizeSpecialChars($val, false) ],
				'admin_area_1'   	=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => $this->sanitizeSpecialChars($val, false) ],
				'admin_area_2' 		=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => $this->sanitizeSpecialChars($val, false) ],
				'postal_code'		=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => $this->sanitizeSpecialChars($val, false) ],
				'country_code' 		=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => strtoupper(substr($val, 0, 2))],
				'cellphone'			=> ['filter' => FILTER_CALLBACK, 'options' => fn ($val) => $this->sanitizeSpecialChars($val, false) ],
				'country_id'		=> FILTER_VALIDATE_INT,
				'is_billing'		=> FILTER_VALIDATE_INT
			];
			$filtered = filter_var_array($post, $filters);
			$address = [
				'city' 			=> $filtered['admin_area_2'],
				'zipcode'		=> $filtered['postal_code'],
				'line1'			=> $filtered['address_line_1'],
				'line2'			=> $filtered['address_line_2'],
				'line4'			=> $filtered['admin_area_1'],
				'country'		=> $filtered['country_id'],
				'country_code' 	=> $filtered['country_code']
			];
			$this->_checkoutTable->setTable('addresses');
			$this->_checkoutTable->create($address);			
			
			$address_user = [
				'firstname'		=> $filtered['firstname'],
				'lastname' 		=> mb_strtoupper($filtered['lastname']) ,
				'company' 		=> $filtered['company'],
				'cellphone'		=> $filtered['cellphone'],				
				'is_active'		=> 1,
				'is_billing'	=> $filtered['is_billing'] ?? 0,
				'is_delivery' 	=> 1,
				'address' 		=> $this->_checkoutTable->lastInsertId(),
				'user'			=> 68754
			];
			$this->_checkoutTable->setTable('address_user');
			$this->_checkoutTable->create($address_user);


			
			$this->_checkoutTable->rollback();
			$hash = '$2y$09$lzAzSxwfjjYT8/7/7p1UVu/TBCdf5eDw.ehTWZXXOeTCR3UBZH8yG';
			$filtered['verify'] = password_verify('F4tdNd65i5wQnBM', $hash);
			$this->_response->getBody()->write(json_encode($filtered));

			return $this->_response;
		}
		catch(Error | Exception $e){throw $e;}
		

	}
}