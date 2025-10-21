<?php
declare(strict_types=1);
namespace App\Product\Types\AddToCart;

use App\Product\Types\Type;
use Domain\Table\{Order};
use Library\HTML\Form;

/**
 * Pattern Strategy
 * This class is the concrete Strategy
 */
final class Graphics extends Type {  
   
    protected Order $_orderTable;
    
    public function __invoke(){      
        $this->form = new Form;
        $this->data = $this->getRequest()->getParsedBody();
        
        $this->_orderTable = new Order($this->_setDb());
        $this->_orderTable->setRoute($this->_route);        
        $this->_orderTable->beginTransaction(); 
        try{
                  
            $this->createOrder();          
            $this->_createItem();
            $this->_orderTable->commit();            
            $this->_sponsors();       
            $this->_customs();     
            $items = $this->_orderTable->items($this->data['item']['id_order']); 
            $cart = $this->getCart($items); $items = $this->_orderTable->items($this->data['item']['id_order']); 
            $cart = $this->getCart($items);
            $this->_view = 'checkout.cart-0';           
            $this->_response = $this->_response->withStatus(201); 
            $this->_response->getBody()->write($this->partial(compact('cart')));
            return $this->_response;
        }
        catch(\Exception $e){
            $this->_orderTable->rollback();
            $this->_response = $this->_response->withStatus(400); 
            $this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');        
            $this->_response->getBody()->write(json_encode(['error' => $e->getMessage(), 'data' => $this->data['item']]));
            return $this->_response;
        }    
    }

    private function _createItem(){
        $item_price = $this->_calculatePrice();
        $data = [
            'id_order'              => $this->data['item']['id_order'],
            'qty'                   => $this->data['item']['qty'],  
            'weight'                => $this->data['item']['weight'], 
            'item_type'             => $this->data['item']['item_type']['id'],          
            'product'               => $this->data['item']['product'],
            'sku'                   => $this->data['item']['sku'],            
            'description'           => $this->_designation(),
            'webshop_currency'      => $this->data['item']['webshop_currency'],
            'currency'              => $this->data['item']['currency'],
            'product_url'           => $this->data['item']['product_url'],
            'product_img'           => $this->data['item']['product_img'],
            'item_price'            => $item_price,
            'tax_included'          => $this->data['item']['tax_included'],      
            'item_paid'             => 0,               
            'item_category'         => $this->data['item']['item_category'],  
            'product_licence'       => $this->data['item']['product_licence'],   
            'item_treated'          => 0,        
            'workspace'             => $this->data['item']['workspace'],
            'item_comment'          => $this->_comment()
        ];
        $args = [
            'id_order'              => FILTER_VALIDATE_INT,
            'qty'                   => FILTER_VALIDATE_INT,
            'weight'                => FILTER_VALIDATE_INT,
            'item_type'             => FILTER_VALIDATE_INT,
            'product'               => FILTER_VALIDATE_INT,
            'sku'                   => FILTER_DEFAULT,
            'description'           => FILTER_DEFAULT,
            'webshop_currency'      => FILTER_DEFAULT,
            'currency'              => FILTER_VALIDATE_INT,
            'product_url'           => FILTER_VALIDATE_URL,
            'product_img'           => FILTER_VALIDATE_URL,
            'item_price'            => FILTER_DEFAULT,
            'tax_included'          => FILTER_VALIDATE_INT,
            'item_paid'             => FILTER_VALIDATE_INT,
            'item_category'         => FILTER_VALIDATE_INT, 
            'product_licence'       => FILTER_VALIDATE_INT, 
            'item_treated'          => FILTER_VALIDATE_INT,       
            'workspace'             => FILTER_VALIDATE_INT,
            'item_comment'          => FILTER_DEFAULT    
        ];
        $line = array_filter(filter_var_array($data, $args, false));   
        $this->_orderTable->setTable('order_item');
        $this->_orderTable->create($line);
        $this->data['item']['id'] = (int)$this->_orderTable->lastInsertId();
    }

    private function _designation(){
        $designation = $this->data['item']['description'];           
        return $this->specialchars_decode($designation);
    }

    private function _comment(){        
        return $this->specialchars_decode($this->data['item']['item_comment']);
    }

    private function _calculatePrice(): string {
        // Attention la finition c'est *qty de produit
        $qty = (int)$this->data['item']['qty'];
        $price = $this->data['item']['item_price'];
        if(array_key_exists('discount', $price)):
            //$sales = $price['discount'];
            //unset($price['discount']); 
        else: 
            //$sales = 0;           
        endif;
        $prices = array_merge(['product' => 0, 'opts' => 0, 'accessories' => 0, 'foam' => 0, 'install' => 0], $price);
        /*if($sales > 0):
            $p = $prices['product'] * $qty;
            $res = $p - ($p / (100/$sales));
            $prices['product'] = (float) $res;
        else:
            $prices['product'] = (float)$prices['product'] * $qty;
        endif;*/

        $prices['finish'] = (float)$prices['finish'] * $qty;
        $prices['product'] = (float)$prices['product'] * $qty;
        $prices['accessories'] = (float)$prices['accessories'] * $qty;
        $prices['premium'] = (float)$prices['premium'] * $qty;        
        
        return json_encode($prices,JSON_PRESERVE_ZERO_FRACTION);
    } 

    private function _sponsors()
    {
        // sauvegarde d'éventuels fichiers upload
        $this->uploadedFiles = $this->getRequest()->getUploadedFiles();   
        
        if(!empty($this->uploadedFiles)):
            $directory = ORDERS_DIR . DS . $this->data['item']['id_order'] . DS . 'items' . DS . $this->data['item']['id'];
            $sponsors = $this->uploadedFiles['item']['item_custom']['options']['sponsor'] ?? [];
            if(!empty($sponsors)):
                $opt_sponsor = $this->data['item']['item_custom']['options']['sponsor'] ?? [];
                foreach ($sponsors as $key => $sponsor):
                    if($sponsor['file']->getError() !== UPLOAD_ERR_OK)  continue;
                    $basename = pathinfo($sponsor['file']->getClientFilename(), PATHINFO_FILENAME); 
                    $filename = $this->moveUploadedFile($directory, $sponsor['file'], $basename);
                    $link = URL_FILES_ORDERS.  $this->data['item']['id_order'] . '/items/' . $this->data['item']['id'] .'/' .$filename;
                    $opt_sponsor[$key]['file'] = $link;
                endforeach; 
                foreach($opt_sponsor as $k => $sponsor){    
                    if(strlen(trim($sponsor['text']) ?? '') === 0  &&  strlen($sponsor['file'] ?? '') === 0) unset($opt_sponsor[$k]);
                }
                $this->data['item']['item_custom']['options']['sponsor'] = array_values($opt_sponsor);                    
            endif;
        endif;       
    }

    public function _customs(){
        $customs = json_encode($this->data['item']['item_custom'], JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        $sql = "UPDATE order_item SET item_custom = :customs WHERE id = :id;";
        $this->_orderTable->setTable('order_item');
        $this->_orderTable->query($sql,['id' => $this->data['item']['id'], 'customs' => $customs], true);
    }


    


}