<?php
declare(strict_types=1);
namespace Domain\Table;

use Core\Domain\Table;

class Option extends Table {
	public function plateColors(){
        $this->setEntity(null);
        $params = ['l10n_id' => $this->getL10nId()];
        $sql = "SELECT id,
            designation,
            is_for_plate, 
            is_for_number,
            text_color,
            hexa_color,
            printing_ref,
            is_plate_default,
            is_number_default 
            FROM vue_plate_colors 
            WHERE l10n = :l10n_id 
            AND (is_for_plate = 1 OR is_for_number = 1)
            ORDER BY designation;";
        return $this->query($sql,$params);
    }

    public function options(array $options = [], bool $pro = false)
    {
        $this->setEntity('Option');
        if(count($options) === 0) return $options;
        $data = $this->namedPlaceHolder($options);
        $sql = "SELECT
        _i18n.name,
        opt.id AS 'option_id', 
        opt.sibling,
        opt.label_id,
        opt.input_name,
        opt.input_type,
        opt.modal,       
        CASE WHEN pc.currency = 3 THEN ROUND(pc.price*1.20, 0) ELSE ROUND(pc.price, 0) END AS 'price', 
        cur.currency_lib AS 'currency_code',
        country.vat,
        l10ns._locale,
        _i18n.picto
        FROM options AS opt
        LEFT JOIN l10ns ON l10ns.id = :l10n_id
        LEFT JOIN options_i18n AS _i18n ON (_i18n.option = opt.id AND _i18n.l10n = l10ns.id)        
        LEFT JOIN currency AS cur ON cur.currency_id = :cur_id
        LEFT JOIN country ON country.country_iso = :country
        LEFT JOIN option_prices AS pc ON (pc.option = opt.id AND pc.currency = cur.currency_id)        
        WHERE opt.id IN ({$data->place_holder}) 
        AND pc.id = (
            SELECT MAX(option_prices.id) 
            FROM option_prices 
            WHERE option_prices.option = pc.option
            AND option_prices.currency = pc.currency
            AND option_prices.valid_since <= current_timestamp() 
            AND (option_prices.valid_until IS NULL OR option_prices.valid_until >= current_timestamp())
            AND option_prices.pro = CASE WHEN :is_pro = 1 THEN 1 ELSE option_prices.pro END
        );";
        $params = array_merge(
            $data->values, 
            array('l10n_id' => $this->getL10nId(), 'cur_id'=> $this->getCurrencyId(), 'country' => $this->getCountryCode(), 'is_pro' => (int)$pro)
        );
        return $this->query($sql,$params);
    }    

    public function optionnalStickers(int $family_id){
        $this->setEntity('Option');
        $sql = "SELECT
            o.id,
            o_i18n.name,  
            o.opt_type,         
            cur.currency_lib AS 'currency_code',
            l10ns._locale AS 'locale',
            country.vat,
            (SELECT 
            CASE WHEN o_price.currency IN (3,4) THEN (o_price.price * 1.20) ELSE o_price.price END
            FROM option_prices o_price
            WHERE o_price.option = o.id 
            AND o_price.currency = cur.currency_id                
            AND o_price.valid_since <= current_timestamp()
            AND (o_price.valid_until IS NULL OR current_timestamp() <= o_price.valid_until)
            AND JSON_CONTAINS(o_price.universes, :family, '$') = 1
            #AND o_price.kit_type = 2                
            ORDER BY o_price.id DESC 
            LIMIT 1) AS 'price'
            FROM options AS o
            LEFT JOIN l10ns ON l10ns.id = :l10n_id
            LEFT JOIN options_i18n AS o_i18n ON (o_i18n.option = o.id AND o_i18n.l10n = l10ns.id)            
            LEFT JOIN currency AS cur ON cur.currency_id = :cur
            LEFT JOIN country ON country.country_iso = :country
            WHERE o.opt_type IN('hubs_stickers', 'mini_plates')
            ORDER BY o.opt_type, o._order;";

            return $this->query($sql, ['l10n_id' => $this->getL10nId(), 'cur' => $this->getCurrencyId(), 'family' => $family_id, 'country' => $this->getCountryCode()]);        

    }  	
}