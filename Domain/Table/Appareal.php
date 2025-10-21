<?php
declare(strict_types=1);
namespace Domain\Table;

use Domain\Entity\Appareal AS A;

class Appareal extends Catalog  {
    protected int $_store = 1;
    protected false|A $product = false;

    public function read(int $id){
        $this->setEntity('Appareal');
        $params = ['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n'=>$this->getL10nId(), 'id' => $id];        
        $sql = "
            SELECT i.id,
            i.weight,
            i.workspace,
            i.license, 
            i.department,
            i.behavior AS 'behavior_id',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',      
            i_l10n.short_desc,
            i_l10n.description,
            i_l10n.meta_title,
            i_l10n.meta_description,
            i.has_sizes,
            i.stock_management,
            l10n.id AS 'l10n_id',
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',          
            b._type AS 'behavior_type',
            (SELECT
                JSON_OBJECT(
                'price',  prices.price,                    
                'currency_id', cur.currency_id,
                'currency_code', cur.currency_lib)
                FROM prices                          
                WHERE prices.item = i.id
                AND prices.currency = cur.currency_id
                AND prices.valid_since <= current_timestamp() 
                AND (prices.valid_until IS NULL OR current_timestamp() <= prices.valid_until)
                ORDER BY prices.id DESC 
                LIMIT 1
            ) AS 'price'                             
            FROM items i      
            LEFT JOIN l10ns l10n ON l10n.id = :l10n 
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)           
            LEFT JOIN category_default_content df ON (
                df.l10n = 1 
                AND df.category = (
                    SELECT c.id
                    FROM categories node 
                    LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
                    LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = l10n.id)
                    WHERE node.id = i.department
                    AND df.category IS NOT NULL
                    ORDER BY c.node_left DESC
                    LIMIT 1
                )
            )
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso
            LEFT JOIN currency cur ON cur.currency_id = :cur
            WHERE i.id = :id;
        ";
        $this->product = $this->query($sql, $params, true);
        if($this->product):
            $this->setEntity(null);           
            $this->product->files = $this->files($id); 
            if($this->product->stock_management > 0):
                $this->_stocks();
            endif;        
        endif;
        return $this->product;
    }

    private function _stocks(){
        $this->setEntity(null);   
        $sql = "SELECT size_id, UPPER(size_designation) AS 'size_designation' , qty FROM item_stocks WHERE item = :id AND l10n = CASE WHEN :l10n_id NOT IN (1,3) THEN 1 ELSE :l10n_id END AND qty > 0 ORDER BY _position;";
        $this->product->stock =  $this->query($sql, ['id' => $this->product->id, 'l10n_id' => $this->getL10nId()]);
    }       
}