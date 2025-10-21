<?php
declare(strict_types=1);
namespace Domain\Table;


class Sportswear extends Catalog {

    protected $section; 

	public function cards(array $slices = []): array {
        if(count($slices) < 1) return [];
        $this->setEntity('Card');
        $pl = $this->namedPlaceHolder2($slices);       
        $params = array_merge(['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n_id'=>$this->getL10nId()], $pl->values);        
        $sql = " SELECT i.id, 
            i.department, 
            i.weight, 
            i.license, 
            i.parent,  
            l10n.id AS 'l10n_id',
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',           
            b._type AS 'behavior_type',
            i_l10n.item_slug,
            JSON_OBJECT('price',ROUND(prices.price)) AS 'price',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            df.full_designation AS 'df_full_designation',
            f.url AS 'cover'                    
            FROM items i
            LEFT JOIN l10ns l10n ON l10n.id = :l10n_id 
            LEFT JOIN currency cur ON cur.currency_id = :cur                  
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)
            LEFT JOIN prices ON (prices.item = i.id AND prices.currency = cur.currency_id)   
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
            LEFT JOIN vue_files f ON (f.product = i.id AND f.cover = 'on')
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso            
            WHERE i.id IN ({$pl->place_holder})            
            ORDER BY i.id DESC
        ";
        return $this->query($sql, $params);
    }

    public function read(int $section){
        //$this->setConstructorArgs([$this->_route]);
        $sql = "SELECT 
            m.id AS 'menu_id',
            m_l10n.title,
            m_l10n.name,
            m_l10n.short_desc,
            m_l10n.description,
            m_l10n.features,
            m_l10n.faq,
            m_l10n.further_info,
            m_l10n.meta_title,
            m_l10n.meta_description,
            m_l10n.files,
            l10n.id AS 'l10n_id',
            m_w.filters,
            m_w.attr,
            m_w.category,
            m_w.website,
            CASE 
                WHEN m_w.department_store IS NOT NULL THEN m_w.department_store 
                ELSE (
                    SELECT mw_2.department_store
                    FROM menu_websites mw_2
                    LEFT JOIN menus m2 ON (m2.id = mw_2.menu AND m2.workspace = m.workspace)
                    WHERE  mw_2.website = m_w.website 
                    AND mw_2.department_store IS NOT NULL
                    AND m2.node_left < m.node_left 
                    AND m2.node_right > m.node_right 
                    ORDER BY m2.node_left DESC LIMIT 1
                ) 
            END AS 'department_store',                      
            (SELECT JSON_ARRAYAGG(menu_product_types.product_type) FROM menu_product_types WHERE menu_product_types.menu = m.id) AS types,
            (SELECT JSON_ARRAYAGG(m_c.category)  FROM menu_categories m_c WHERE m_c.menu = m.id AND m_c.website = m_w.website) AS 'categories'          
            FROM menus m
            LEFT JOIN l10ns l10n ON l10n.id = :l10n_id
            LEFT JOIN menu_l10ns m_l10n ON (m_l10n.menu = m.id AND m_l10n.l10n = l10n.id)
            LEFT JOIN menu_websites m_w ON (m_w.menu = m.id AND m_w.website = :ws)
            WHERE m.id = :id;";
        $this->section = $this->query($sql,['id'=>$section, 'l10n_id'=>$this->getL10nId(), 'ws'=>WEBSITE_ID],true);
        if($this->section):         
            $this->section->types = json_decode($this->section->types??'[]',true);
            $this->section->categories = json_decode($this->section->categories??'[]',true);

            $this->section->items = $this->itemsInSection();
            $this->section->l10ns = $this->setL10ns();
        endif;
        return $this->section;
    }




}