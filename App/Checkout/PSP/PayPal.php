<?php 
declare(strict_types=1);
namespace App\Checkout\PSP;

use Core\Component;
use App\Checkout\Domain\Entity\Purchase;
use App\Checkout\Domain\Table\PayPal AS Table;    

abstract class PayPal extends Component implements ServiceProvider {
    protected $appName = 'KUTVEK KITGRAPHIK';
    protected $_accessToken;
    protected string|false $_clientToken = false;
    protected $purchase; 
    protected Table $table;      
    abstract protected string $endpoint_url {
        get;
    } 
    abstract protected string $clientID {
        get;
    }

    abstract protected string $secret {
        get;
    }

    abstract public function getClientID(): string;

    abstract public function getEndpointUrl(): string;
    
    abstract public function getSecret(): string;

    public function getClientToken(): string
    {
        if($this->_clientToken) return $this->_clientToken;
        $this->_clientToken = $this->_generateClientToken();
        return $this->_clientToken;
    }    

    public function getPurchase()
    {
        return $this->purchase;
    }

    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
        return $this;
    }

    protected function _generateClientToken(){
        $url = $this->getEndpointUrl() . "/v1/identity/generate-token";
        $curl = curl_init($url);        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        $accessToken = $this->_generateAccessToken();
        $headers = array(
           "Accept: application/json",
           "content-type:application/json;charset=utf-8",
           "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        $decode = json_decode($resp);
        return $decode->client_token;
    }

    protected function _generateAccessToken(){
        $url = $this->getEndpointUrl() . "/v1/oauth2/token";
        $ch = curl_init($url);        
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION , 6); //NEW ADDITION
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_USERPWD, $this->getClientID().":".$this->getSecret());
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        $result = curl_exec($ch);
        curl_close($ch);
        if(empty($result))die("Error: No response.");
        else
        {
            $json = json_decode($result);
            $this->_accessToken = $json;
            return $json->access_token;
        }
    }

    public function create() {
        $this->table = new Table($this->_setDb());
        $this->table->setRoute($this->_route);
        $queries = $this->getRequest()->getQueryParams();
        $id = (int)$queries['order'];
        $items = $this->table->items($id);
        $purchase = new Purchase;
        $purchase->id = $items[0]->order_id;
        $purchase->customer = json_decode($items[0]->customer);
        $purchase->country_shipping = $items[0]->country_shipping;        
        $purchase->currency_code = $items[0]->currency_code;
        $purchase->created = $items[0]->created;
        $purchase->vat = $items[0]->vat;
        $purchase->items = $items;
        $purchase->shipping = $items[0]->delivery !== null ? json_decode($items[0]->delivery) :  (object)['type'=>$items[0]->delivery_name, 'cost' => $items[0]->com_shipping];       
        $purchase->coupon = json_decode($items[0]->coupon);         
        $purchase->amount();   
        unset($purchase->coupon);
        unset($purchase->vat);
        unset($purchase->country_shipping);
        unset($purchase->currency_code);
        unset($purchase->tax_rate);
        unset($purchase->reedem);
        unset($purchase->customer);       
        $url = $this->getEndpointUrl() .'/v2/checkout/orders';
        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_URL, $url);       
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        $accessToken = $this->_generateAccessToken();                 
        $headers = [
           "Accept: application/json",
           "Content-Type:application/json;charset=utf-8",
           "Authorization: Bearer {$accessToken}"
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $purchase->__toString());
        $resp = json_decode(curl_exec($curl));
        curl_close($curl);  
        $resp->purchase = $purchase;
        $resp = json_encode($resp,JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE);
        
        return $resp;
    }

    public function capture(){        
        $post = $this->getRequest()->getParsedBody();
        $paypalOrderId = $post['paypalOrderId'];       

        $url = $this->getEndpointUrl() . "/v2/checkout/orders/{$paypalOrderId}/capture";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);       
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        $accessToken = $this->_generateAccessToken();                 
        $headers = [
           "Accept: application/json",
           "Content-Type:application/json;charset=utf-8",
           "Authorization: Bearer {$accessToken}"
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{}');
        $resp = json_encode(curl_exec($curl));
        curl_close($curl);
        if(!property_exists($resp, 'debug_id')):
            return $this->approve();
        endif;
        return json_encode($resp);
    }

    
    public function approve(){
        $queries = $this->getRequest()->getQueryParams();
        $post = $this->getRequest()->getParsedBody();
        $paypalOrderId = $post['paypalOrderId'];
        $order = (int)$queries['order'];
        $transac = ['geo' => $this->getL10nCode(),'com_id' => $order,'PayerID' => $paypalOrderId];
        $this->table = new Table($this->_setDb());
        $this->table->setRoute($this->_route);
        $this->table->setTable('_order');
        $data = [
            'order_state',
            'paid' => 1, 
            'retour' => json_encode($transac), 
            'platform' => 'fr', 
            'date_paid' => \date('Y-m-d H:i:s'), 
            'v2' => 0, 
            'payment' => 1
        ];
        $this->table->update($order, $data);
        return '{}';
    }    
}