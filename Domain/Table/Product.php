<?php
declare(strict_types=1);
namespace Domain\Table;


class Product extends Catalog  {

	public function read(int $id){
        $this->setEntity('Product');
        $this->setConstructorArgs([$this->_route]);
        $params = ['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n_id'=>$this->getL10nId(), 'item_id' => $id];        
        $sql = "SELECT i.id, 
            i.department, 
            i.weight, 
            i.license, 
            i.parent,
            i.version,  
            l10n.id AS 'l10n_id',
            l10n._locale,
            l10n._prefix,
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code', 
            i.behavior AS 'behavior_id',          
            b._type AS 'behavior_type',
            i_l10n.item AS 'has_content',
            JSON_OBJECT('price',ROUND(prices.price)) AS 'price',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            df.full_designation AS 'df_full_designation'                            
            FROM items i
            LEFT JOIN l10ns l10n ON l10n.id = :l10n_id 
            LEFT JOIN currency cur ON cur.currency_id = :cur                  
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)
            LEFT JOIN item_prices_2 prices ON (prices.id = i.id AND prices.currency_id = cur.currency_id)   
            LEFT JOIN category_default_content df ON (
                df.l10n = l10n.id 
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
            WHERE i.id = :item_id           
            ORDER BY i.id DESC
        ";

        $q = $this->query($sql, $params, true);
        $this->unsetConstructorArgs();
        $q->files = $this->files($id);
        return $q;
    }

    public function readBySlug(string $slug){
        $this->setEntity('Product');
        $this->setConstructorArgs([$this->_route]);
        $params = ['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n_id'=>$this->getL10nId(), 'item_slug' => $slug];        
        $sql = "SELECT i.id, 
            i.department, 
            i.weight, 
            i.license, 
            i.parent,
            i.version,  
            l10n.id AS 'l10n_id',
            l10n._locale,
            l10n._prefix,
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code', 
            i.behavior AS 'behavior_id',          
            b._type AS 'behavior_type',
            i_l10n.item AS 'has_content',
            JSON_OBJECT('price',ROUND(prices.price)) AS 'price',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            df.full_designation AS 'df_full_designation'                            
            FROM items i
            LEFT JOIN l10ns l10n ON l10n.id = :l10n_id 
            LEFT JOIN currency cur ON cur.currency_id = :cur                  
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)
            LEFT JOIN item_prices_2 prices ON (prices.id = i.id AND prices.currency_id = cur.currency_id)   
            LEFT JOIN category_default_content df ON (
                df.l10n = l10n.id 
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
            WHERE i_l10n.item_slug = :item_slug        
            ORDER BY i.id DESC
        ";
        $q = $this->query($sql, $params, true);
        $this->unsetConstructorArgs();
        $q->files = $this->files($q->id);
        return $q;
    }

    public function behaviorInfo($id){
        $sql = "SELECT 
        i.id,
        i.behavior AS 'behavior_id',          
        b._type AS 'behavior_type'
        FROM items i
        LEFT JOIN behaviors b ON b.id = i.behavior                    
        WHERE i.id = :id;";
        return $this->query($sql,['id' => $id], true);
    }

    public function info(int $id){
        $sql = "
            SELECT gk.vehicle AS 'vehicle_id',            
            gk.design AS 'design_id',
            design.designation AS 'design_name',
            gk.color AS 'color_id',
            color.designation AS 'color_name',
            gk.kit_type AS 'item_type',
            i_l10n.short_desc AS 'l10n_short_desc',
            i_l10n.description AS 'l10n_description',
            i_l10n.meta_title AS 'l10n_meta_title',
            i_l10n.meta_description AS 'l10n_meta_description',            
            i_l10n.features AS 'l10n_features',
            i_l10n.composition_care AS 'l10n_composition_care',
            JSON_OBJECT(
                'finish', 
                CASE         
                WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.finish') IS  NULL THEN JSON_VALUE(parent.attr, '$.finish') 
                ELSE JSON_VALUE(gk.attr, '$.finish') END,
                'switch',
                CASE 
                WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.switch') IS  NULL THEN JSON_VALUE(parent.attr, '$.switch') 
                ELSE JSON_VALUE(gk.attr, '$.switch') 
                END,
                'opts',
                CASE 
                WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.opts') IS  NULL THEN JSON_VALUE(parent.attr, '$.opts') 
                ELSE JSON_VALUE(gk.attr, '$.opts') 
                END,
                'door_stickers', JSON_VALUE(gk.attr, '$.door_stickers'),
                'seat_cover', JSON_VALUE(gk.attr, '$.seat_cover'),
                'rim_sticker', JSON_VALUE(gk.attr, '$.rim_sticker'),
                'chrome',
                CASE 
                # si on a un parent, un null pour chrome
                WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.chrome') IS  NULL THEN JSON_VALUE(parent.attr, '$.chrome') 
                ELSE JSON_VALUE(gk.attr, '$.chrome') 
                END,
                'plastics',JSON_VALUE(gk.attr, '$.plastics'),
                'sled_color', JSON_VALUE(v.attr, '$.sled_color'),
                'mini_plates',
                CASE         
                WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.mini_plates') IS  NULL THEN JSON_VALUE(parent.attr, '$.mini_plates') 
                ELSE JSON_VALUE(gk.attr, '$.mini_plates') END,          
                'hubs_stickers', 
                CASE         
                WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.hubs_stickers') IS  NULL THEN JSON_VALUE(parent.attr, '$.hubs_stickers') 
                ELSE JSON_VALUE(gk.attr, '$.hubs_stickers') END,            
                'tunnel', JSON_VALUE(v.attr, '$.tunnel'),
                'turbo', JSON_VALUE(v.attr, '$.turbo'),
                'cylinder', JSON_VALUE(v.attr, '$.cylinder'),
                'starter', JSON_VALUE(v.attr, '$.starter'),
                'reverse', JSON_VALUE(v.attr, '$.reverse'),
                'wide', JSON_VALUE(v.attr, '$.wide'),
                'skid_plates_versions',
                CASE         
                WHEN i.parent IS NOT NULL AND JSON_VALUE(gk.attr, '$.skid_plates_versions') IS  NULL THEN JSON_VALUE(parent.attr, '$.skid_plates_versions') 
                ELSE JSON_VALUE(gk.attr, '$.skid_plates_versions') END    

            ) AS 'attributes',
            JSON_OBJECT(
            'id', v.id, 
            'designation', v.name,
            'full_designation', v.fullname,
            'family', JSON_OBJECT('id', v.universe, 'designation', v.fam_name),
            'brand', JSON_OBJECT('id', v.brand, 'designation', v.brand_name),
            'model', JSON_OBJECT('id', v.model, 'designation', v.model_name),
            'sponsors', JSON_OBJECT('template', v.template_sponsors, 'quota', v.nb_sponsor),
            'years', JSON_ARRAY()          
            ) AS 'vehicle', 
            v.name AS 'vehicle_designation',
            v.universe AS 'family_id'
            FROM products_old gk
            JOIN items i ON i.id = gk.id
            LEFT JOIN currency cur ON (cur.currency_id = :currency_id)
            LEFT JOIN l10ns ON l10ns.id = :l10n_id
            LEFT JOIN products_old AS parent ON parent.id = i.parent
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10ns.id)
            LEFT JOIN vue_vehicle_2 AS v ON (v.id = gk.vehicle AND v.l10n = l10ns.id)
            LEFT JOIN vue_designs design ON design.id = gk.design 
            LEFT JOIN vue_colors color ON (color.id = gk.color AND color.l10n = l10ns.id)  
            WHERE gk.id = :id;
        ";

        $info = $this->query($sql, ['l10n_id' => $this->getL10nId(),   'id' => $id, 'currency_id' => $this->currencyId], true);
        
        return $info;
    }
    public function finishes(int $family = 1)
    {       
        $this->setEntity('Finish');        
        $sql = "SELECT  
        opt.id,        
        opt_l10n.name,                   
        (SELECT 
            CASE WHEN o_price.currency IN (3,4) THEN (o_price.price * 1.20) ELSE o_price.price END
            FROM option_prices o_price
            WHERE o_price.option = opt.id 
            AND o_price.currency = cur.currency_id                
            AND o_price.valid_since <= current_timestamp()
            AND (o_price.valid_until IS NULL OR current_timestamp() <= o_price.valid_until)
            AND JSON_CONTAINS(o_price.universes, :family, '$') = 1
            AND o_price.kit_type = 2                
            ORDER BY o_price.id DESC 
            LIMIT 1) 
        AS 'price',
        cur.currency_lib AS 'currency_code',
        l10ns._locale AS 'locale', 
        :family AS 'family',         
        country.vat  
        FROM options AS opt
        JOIN l10ns ON l10ns.id = :l10n_id
        LEFT JOIN option_l10ns AS opt_l10n ON (opt_l10n.option = opt.id AND opt_l10n.l10n = l10ns.id)      
        LEFT JOIN currency AS cur ON cur.currency_id = :cur        
        LEFT JOIN country ON country.country_iso = :country        
        WHERE opt.opt_type = 'finish' 
        ORDER BY opt._order;";        
        $params = array_filter(array('l10n_id' => $this->getL10nId(), 'cur'=>$this->getCurrencyId(), 'country' => $this->getCountryCode(), 'family' => $family));
        return $this->query($sql, $params);
    }

    public function premiums(int $family = 1){
        $this->setEntity('Finish');
        $sql = "SELECT  
        opt.id,        
        opt_l10n.name,                   
        (SELECT 
            CASE WHEN o_price.currency IN (3,4) THEN (o_price.price * 1.20) ELSE o_price.price END
            FROM option_prices o_price
            WHERE o_price.option = opt.id 
            AND o_price.currency = cur.currency_id                
            AND o_price.valid_since <= current_timestamp()
            AND (o_price.valid_until IS NULL OR current_timestamp() <= o_price.valid_until)
            AND JSON_CONTAINS(o_price.universes, :family, '$') = 1
            AND o_price.kit_type = 2                
            ORDER BY o_price.id DESC 
            LIMIT 1) 
        AS 'price',
        cur.currency_lib AS 'currency_code',
        l10ns._locale AS 'locale', 
        :family AS 'family',         
        country.vat  
        FROM options AS opt
        JOIN l10ns ON l10ns.id = :l10n_id
        LEFT JOIN option_l10ns AS opt_l10n ON (opt_l10n.option = opt.id AND opt_l10n.l10n = l10ns.id)      
        LEFT JOIN currency AS cur ON cur.currency_id = :cur        
        LEFT JOIN country ON country.country_iso = :country        
        WHERE opt.opt_type = 'premium' 
        AND opt.full_custom = 0
        ORDER BY opt._order;";        
        $params = array_filter(array('l10n_id' => $this->getL10nId(), 'cur'=>$this->getCurrencyId(), 'country' => $this->getCountryCode(), 'family' => $family));
        return $this->query($sql, $params);
    }



}