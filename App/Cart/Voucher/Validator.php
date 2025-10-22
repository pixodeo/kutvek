<?php 
declare(strict_types=1);
namespace App\Cart\Voucher;
use Core\Component;
use Domain\Table\Checkout;
use Domain\Entity\Checkout\Voucher;
use Exception;

/**
 * Classe de base pour représenter un produit
 */
final class Validator extends Component {	
	private false|Voucher $_code = false;
	protected Checkout $table;
	private $_body;
	private int $_orderId;

	public function check()
	{
		$this->_body =  json_decode($this->getRequest()->getBody()->getContents());		
		$queries = $this->getRequest()->getQueryParams();		
		$this->_orderId = (int)$queries['id'];
		$this->_body->customer = $this->_body->customer ?? 0;	
		$this->table = new Checkout($this->_setDb());
		$this->table->setRoute($this->_route);
		$this->_code = $this->_codeInfo($this->_body->code);		
		$checks = [
            'pro'              => $this->checkAccount(),                   
            'validity'         => $this->checkValidity(),
            'options_excluded' => $this->checkOptions(),
            'balance'          => $this->checkBalance(),
            'already_used'     => $this->checkApplicable(),
            'min_purchase'     => $this->checkMinPurchase()
        ];
	    foreach($checks as $k => $value)
	    {
	        if(!$value) {
	            $error = $this->getError($k);
	            throw new Exception(json_encode($error, JSON_NUMERIC_CHECK, JSON_UNESCAPED_SLASHES));	           
	            break;
	        }
	    }
	    // Tout va bien, on enregistre le code promo
        $this->apply();
	}

	private function _codeInfo(string $code): false|Voucher
    {        
    	$this->table->setEntity('Checkout\Voucher');
    	$sql = "SELECT c.id, 
    	c.code, 
    	c.code_type, 
    	c.discount, 
    	c.amount, 
    	c.created, 
    	c.expire, 
    	c.limited, 
    	c.min_purchase,
    	c.pro_available AS 'pro',
    	c.with_options,
    	CASE WHEN c.fallback IS NOT NULL THEN f.code ELSE i18n.content END AS 'fallback'
    	FROM promo_codes c
    	LEFT JOIN promo_codes f ON f.id = c.fallback
    	LEFT JOIN i18ns i18n ON (i18n.node = 'fallback' AND i18n.l10n = :l10n)
    	WHERE c.code = :code 
        AND c.website = :website               
        AND c.created <= current_timestamp()
        AND (c.expire IS NULL OR c.expire >= current_timestamp())";        
     	$query = $this->table->query($sql, ['code' => $code, 'website' => 1, 'l10n' => $this->getL10nId()], true);
     	if($query):
     		$query->min_purchase = (float)$query->min_purchase ?? 0;
     	endif;
     	$this->table->setEntity(null);
     	return $query;              
    } 

    public function getCode(){
    	return $this->_code;
    } 

	/**
	 * On vérifie si client connecté est un compte pro avec remise
	 *
	 * @return     bool  ( description_of_the_return_value )
	 */
	public function checkAccount():bool{
		$id = $this->_body->customer > 0 ? $this->$this->_body->customer : false;	
		if(!$id) return true;

		// si on a un code qui fonctionne pour les pros
		if($this->getCode()->pro > 0) return true;

		$sql = "SELECT u.rebate FROM business_customer u WHERE u.id = :id AND u.rebate > 0;";   		
		$customer = $this->table->query($sql, ['id' => $id], true);

		// On ne doit pas avoir de résultat 
		return $customer === false;
	}		

	/**
	 * On vérifie que le code promo est utilisable / existe
	 */
	public function checkValidity(): bool{	
		if(!$this->getCode()) return false;
		return true;
	}

	/**
	 * On verifie que ce code promo n'a pas déjà été utilisé sur une commande du client
	 *
	 * @return     bool  ( description_of_the_return_value )
	 */
	public function checkApplicable(): bool {
		$id = $this->_body->customer > 0 ? $this->$this->_body->customer : false;	
		if(!$id) return true;

		// Vérifie si limite d'utilisation sur le code
		$limited = $this->_code && $this->_code->limited > 0 ? (int)$this->_code->limited : 0;		
		if($limited === 0) return true;

		$sql = "SELECT id FROM _order WHERE id_user = :id AND promo_code = :code AND paid = 1;";		
		$orders = $this->table->query($sql, ['id' => $id, 'code' => $this->_code->id]);

		// Nombre de commandes est inférieur à la limite
		if(count($orders) >= $limited ) return false;

		return true;
	}

	public function checkMinPurchase(): bool {
		if(!$this->_code) return false;
		return $this->table->amount($this->_orderId, $this->_code->min_purchase);
	}

	
	/**
	 * On vérifie si options autorisées
	 *"opts":90,
	 * @return     bool  ( description_of_the_return_value )
	 */
	public function checkOptions(): bool {
		if(!$this->_code) return false;
		if((int)$this->_code->with_options > 0) return true;		

		$sql = "SELECT SUM(CASE WHEN JSON_VALUE(item_price, '$.opts') IS NOT NULL THEN  JSON_VALUE(item_price, '$.opts') ELSE 0 END) AS 'opts'
		FROM order_item WHERE order_item.id_order = :id;";
		$query = $this->table->query($sql,['id' => $this->_orderId], true);
		return $query ? !(float)$query->opts > 0 : false;
	}

	/**
	 * On vérifie que le montant restant est > 0
	 */
	public function checkBalance(): bool{
		return true;
	}

	public function apply(){
		return;
		if(!$this->_code) return;		
		$sql = "UPDATE _order SET promo_code = :code WHERE id = :id;";
		return $this->table->query($sql, ['id' => $this->_orderId, 'code' => $this->_code->id], true);
	}	

	/**
	 * On envoi l'erreur rencontrée
	 *
	 * @param      string  $error  The error
	 */
	public function getError(string $error){
		$this->table->setEntity('Checkout\VoucherError');
		$sql = "SELECT e.id AS 'error',
		e.name,
		l10n.designation, 
		l10n.description,		
		:min_purchase AS 'min_purchase',
		:fallback AS 'fallback',
		(SELECT cur.currency_lib FROM order_item oi JOIN currency cur ON cur.currency_id = oi.currency WHERE oi.id_order = :order LIMIT 1) AS 'currency_code' 
		FROM errors AS e 
		JOIN error_l10ns AS l10n ON (l10n.error = e.id AND l10n.l10n = :l10n)
		WHERE e.error_type = 'promo_code' 
		AND e.name = :error;";
		$params = [
			'error' => $error, 
			'l10n' => $this->getL10nId(),
			'order' => $this->_orderId,
			'min_purchase' => $this->_code->min_purchase ?? 0,
			'fallback' => $this->_code->fallback
		];
		return $this->table->query($sql, $params, true);
	}
}