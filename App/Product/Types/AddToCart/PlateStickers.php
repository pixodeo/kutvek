<?php
declare(strict_types=1);
namespace App\Product\Types\AddToCart;

use App\Product\Types\Type;
use Domain\Table\{Order,Product};
use Library\HTML\Form;

/**
 * Pattern Strategy
 * This class is the concrete Strategy
 */
final class PlateStickers extends Type {  
   
    protected Order $_orderTable;
    protected int $_cartId;
    protected array $data = [];
    public $form;

    public function __invoke(){      
        $this->form = new Form;
        $this->data = $this->getRequest()->getParsedBody();
        $this->data['item']['sku'] = $this->data['item']['product'];
        $this->_orderTable = new Order($this->_setDb());
        $this->_orderTable->setRoute($this->_route);        
        $this->_orderTable->beginTransaction(); 
        try{
            if(!isset($this->data['item']['id_order'])){        
                $this->createOrder();
            }else {         
                $this->_cartId = (int)$this->data['item']['id_order'];
            }
            $this->_item($this->data);
            $this->_orderTable->commit();
            $items = $this->_orderTable->items($this->_cartId); 
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


    private function _item(array $post = []){
        $item_price = $this->_calculatePrice($post);
        $data = [
            'id_order'              => $this->_cartId,
            'qty'                   => $post['item']['qty'],  
            'weight'                => $post['item']['weight'], 
            'item_type'             => $post['item']['item_type'],          
            'product'               => $post['item']['product'],
            'sku'                   => $post['item']['sku'],            
            'description'           => $this->_designation(),
            'webshop_currency'      => $post['item']['webshop_currency'],
            'currency'              => $post['item']['currency'],
            'product_url'           => $post['item']['product_url'],
            'product_img'           => $post['item']['product_img'],
            'webshop_price'         => array_sum($post['item']['price']),
            'item_price'            => $item_price,
            'tax_included'          => $post['item']['tax_included'],      
            'item_paid'             => 0,               
            'item_category'         => $post['item']['item_category'],  
            'product_licence'       => $post['item']['product_licence'],   
            'item_treated'          => 0,        
            'workspace'             => $post['item']['workspace'],
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
            'webshop_price'         => FILTER_VALIDATE_FLOAT,
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

        $line['item_custom'] = json_encode($post['item']['item_custom'], JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        $this->_orderTable->setTable('order_item');
        $this->_orderTable->create($line);
    }

    private function _designation(){
        $designation = $this->data['item']['description'];           
        return $this->specialchars_decode($designation);
    }

    private function _comment(){
        $comment = 'Numéro de course : ' . $this->data['item']['item_custom']['opts']['plate']['raceNumber'];       
        return $this->specialchars_decode($comment);
    }

    private function _calculatePrice(array $post): string {
        // Attention la finition c'est *qty de produit
        $qty = (int)$post['item']['qty'];
        $price = $post['item']['price'];
        if(array_key_exists('discount', $price)):
            //$sales = $price['discount'];
            //unset($price['discount']); 
        else: 
            //$sales = 0;           
        endif;
        $prices = array_merge(['product' => 0, 'opts' => 0, 'accessories' => 0, 'foam' => 0, 'install' => 0], $price);

        //$prices['finish'] = (float)$prices['finish'] * $qty;
        /*if($sales > 0):
            $p = $prices['product'] * $qty;
            $res = $p - ($p / (100/$sales));
            $prices['product'] = (float) $res;
        else:
            $prices['product'] = (float)$prices['product'] * $qty;
        endif;*/
        $prices['product'] = (float)$prices['product'] * $qty;
        $prices['accessories'] = (float)$prices['accessories'] * $qty;
        //$prices['premium'] = (float)$prices['premium'] * $qty;        
        
        return json_encode($prices,JSON_PRESERVE_ZERO_FRACTION);
    }  

}