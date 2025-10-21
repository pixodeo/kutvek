<?php
declare(strict_types=1);
namespace App\Checkout\Domain;

use Core\Model\Entity;

class CartItemEntity extends Entity
{
	/**
	 * $i = [
                    'item_id' => $item->id,
                    'unit_amount' => [ 'currency_code' => $item->currency_code, 'value' =>  $unit_amount],
                    'tax' => [ 'currency_code' => $item->currency_code, 'value' => $tax],
                    'item_category' => $item->item_category,
                    'item_type' => $item->item_type,
                    'item_weight' => $item->weight,
                    'item_comment' => $item->item_comment,
                    'item_visual'   => $item->item_visual,
                    'item_url'      => $item->item_url,
                    'item_paid' => $item->item_paid,
                    'quantity' => $item->qty,
                    'sku' => $item->sku,
                    'category' => 'PHYSICAL_GOODS',
                    'name' => $item->description,
                    'links'     => [
                    'self' => '/api/items/' . $item->id, 
                    'order' => '/api/orders/' . $item->id_order                    
                    ],
                    'tax_included' => $item->tax_included,
                    'behavior' => $item->behavior
                ];       
	 */
	
	public $item_id;
	public $order;
	
	public $tax;
	public $item_category;
	public $item_type;
	public $item_weight;
	public $item_comment;
	public $item_visual;
	public $item_url;
	public $item_paid;
	public $item_qty;
	public $sku;
	public $category = 'PHYSICAL_GOODS';
	public $name;
	public $tax_included;
	public $behavior;
	
	public function getUnit_amount(){

	}
	

	
}