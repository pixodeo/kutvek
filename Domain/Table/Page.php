<?php
declare(strict_types=1);
namespace Domain\Table;

class Page extends Catalog  {
    protected $page;
	
    public function notFound(){

        $sql = "SELECT p_l10n.title,
        p_l10n.content,
        p_l10n.page,
        p_l10n.slug,
        p.layout,
        p.template,
        p_l10n.meta_title,
        p_l10n.meta_description, 
        p_l10n.cover,
        p_l10n.short_description
        FROM page_l10ns AS p_l10n
        JOIN pages p ON (p.id = p_l10n.page)
        LEFT JOIN l10ns ON l10ns.id = :l10n_id
        WHERE p.id= 55
        AND p_l10n.l10n = l10ns.id;";

        return $this->query($sql, [ 'l10n_id' => $this->getL10nId()], true);
    }

	public function editorial(string $slug){
		$sql = "SELECT 
			CASE WHEN c_l10n.full_designation IS NOT NULL THEN c_l10n.full_designation ELSE c_l10n.designation END AS 'designation',
			c_l10n.short_desc,
			c_l10n.description,
			c_l10n.meta_title,
			c_l10n.meta_description,
			c_l10n.cover,
			c_l10n.portrait,
			c_l10n.category AS 'category_id',
			c_l10n.l10n AS 'l10n_id'
			FROM vue_slugs v_s 
			LEFT JOIN category_l10ns c_l10n ON (c_l10n.category = v_s.id  AND c_l10n.l10n = v_s.l10n)
			WHERE v_s.slug = :slug 
			AND v_s.l10n = :l10n
			AND v_s.slug_type = 'section';
		";
		$editorial = $this->query($sql, ['slug' => $slug, 'l10n' => $this->getL10nId()], true);

		if($editorial):
			$editorial->l10ns = $this->slugs($editorial->category_id);  

		endif;	
		return $editorial;
	}

	public function slugs(int $category_id){
		$sql = "SELECT v_s.id AS 'category', 
		CONCAT_WS('/', l10ns._prefix, v_s.slug) AS 'slug', 
		v_s.l10n, 
		l10ns._locale,
		CASE WHEN v_s.l10n = :l10n THEN 'current' ELSE '' END AS 'class' 
		FROM vue_slugs v_s 
		JOIN l10ns ON l10ns.id = v_s.l10n
		WHERE v_s.id = :category AND v_s.slug_type = 'section';";
		return $this->query($sql, ['category'=> $category_id, 'l10n' => $this->getL10nId()]);
	}    

    public function listOfProducts(int $id):array{
        $sql = "SELECT i.id
        FROM items i 
        JOIN good_deal gd ON gd.id = i.id
        WHERE i.department IN (
            (SELECT c.id
            FROM categories p
            LEFT JOIN categories c ON (c.workspace = p.workspace AND c.node_left >= p.node_left AND c.node_right <= p.node_right)
            WHERE p.id = :id
            ORDER BY c.node_left)    
        )
        AND gd.qty > 0
        ORDER BY i.id DESC;";
        $query = $this->query($sql, ['id'=>$id]);
        return $query;
    } 

    public function cards(array $slices = []):array{
        if(count($slices) < 1) return [];
        $this->setEntity('SaleCard');
        $ids = array_column($slices, 'id');
        $pl = $this->namedPlaceHolder2($ids);
        $params = array_merge(['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n'=>$this->getL10nId()], $pl->values);        
        $sql = " SELECT i.id, 
            g.department,  

            i.weight, 
            i.license, 
            i.parent,  
            l10n.id AS 'l10n_id',
            cur.currency_lib AS 'currency_code',
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',
            g.graphic_range AS 'design_id', 
            g.color_ref AS 'color_id',
            CONCAT_WS(' ', d.name, d.season) AS 'design_name',
            color.designation AS 'color_name',
            g.vehicle AS 'vehicle_id',
           	CONCAT_WS(' ', v.fullname , CONCAT_WS('-', g.year_start, g.year_end) ) AS 'vehicle_fullname',
            #c.kit_type, 
            #c.behavior_id,
            b._type AS 'behavior_type',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
            df.full_designation AS 'df_full_designation',
            f.url AS 'cover',
            (SELECT 
            	JSON_OBJECT(
            		'new', CASE WHEN price.price_type = 3 THEN (
            			CASE 
            			WHEN price.currency = 1 THEN ROUND((price.price / 1.20) - ((price.price /1.20) * price.rebate / 100) , 2) 
                        WHEN price.currency = 4 THEN ROUND(price.price  -  (price.price * price.rebate / 100) ,2)
            			ELSE ROUND( price.price - (price.price * price.rebate / 100) , 2) 
            			END)
            			ELSE price.price 
            			END,  
            		'old', CASE WHEN price.currency = 1 THEN ROUND(price.price / 1.20 , 2)
                            WHEN price.currency = 4 THEN ROUND(price.price  , 2)
                            ELSE ROUND(price.price, 2) END,        		
            		'rebate', price.rebate,
            		'type', price.price_type
            	)
	            FROM prices price
	            where price.item = g.id
	            AND price.currency = cur.currency_id
	            AND price.valid_since <= current_timestamp() 
	            AND (price.valid_until IS NULL OR current_timestamp() <= price.valid_until)	            
	            ORDER BY price.id DESC 
	            LIMIT 1
	        ) AS 'price'            
            FROM good_deal g
            JOIN  items i ON i.id = g.id
            LEFT JOIN l10ns l10n ON l10n.id = :l10n
            LEFT JOIN designs d ON d.id = g.graphic_range
            LEFT JOIN color_l10ns color ON (color.color = g.color_ref AND color.l10n = l10n.id)
            LEFT JOIN vue_vehicle_2 v ON (v.id = g.vehicle AND v.l10n = l10n.id)
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id)
            LEFT JOIN category_default_content df ON (
                df.l10n = 1 
                AND df.category = (
                    SELECT c.id
                    FROM categories node 
                    LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
                    LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = l10n.id)
                    WHERE node.id = g.department
                    AND df.category IS NOT NULL
                    ORDER BY c.node_left DESC
                    LIMIT 1
                )
            )
            LEFT JOIN vue_files f ON (f.product = i.id AND f.cover = 'on')
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso
            LEFT JOIN currency cur ON cur.currency_id = :cur
            WHERE g.id IN ({$pl->place_holder}) 
            ORDER BY i.id DESC
        ";
        return $this->query($sql, $params);
    }

    public function read(int $id){
        /*$this->setEntity('GoodDeal');
        $params = ['c_iso' => $this->getCountryCode(), 'cur'=>$this->getCurrencyId(), 'l10n'=>$this->getL10nId(), 'id' => $id];
        $sql = "
            SELECT g.id,
            i.weight,
            i.workspace,
            i.behavior AS 'behavior_id',
            CASE WHEN i_l10n.full_designation IS NOT NULL THEN i_l10n.full_designation ELSE i_l10n.designation END AS 'full_designation',
             df.full_designation AS 'df_full_designation',
            i_l10n.short_desc,
            i_l10n.description,
            i_l10n.meta_title,
            i_l10n.meta_description,
            l10n.id AS 'l10n_id',
            cur.currency_lib AS 'currency_code',
            cur.currency_id,
            country.vat AS 'country_vat',
            country.country_iso AS 'country_code',
            g.graphic_range AS 'design_id', 
            g.color_ref AS 'color_id',
            CONCAT_WS(' ', d.name, d.season) AS 'design_name',
            color.designation AS 'color_name',
            g.vehicle AS 'vehicle_id',
            CONCAT_WS(' ', v.fullname , CONCAT_WS('-', g.year_start, g.year_end) ) AS 'vehicle_fullname',
            #c.kit_type, 
            #c.behavior_id,
            b._type AS 'behavior_type',
            (SELECT 
                JSON_OBJECT(
                    'new', CASE WHEN price.price_type = 3 THEN (
                        CASE 
                        WHEN price.currency = 3 
                             THEN ROUND((price.price - (price.price * price.rebate / 100)) * 1.20 , 2) 
                             ELSE ROUND( price.price - (price.price * price.rebate / 100)  , 2) 
                        END)
                        ELSE price.price 
                        END,  
                    'old', CASE WHEN price.currency = 3 THEN ROUND(price.price * 1.20 , 2) ELSE price.price END,                
                    'rebate', price.rebate,
                    'type', price.price_type
                )
                FROM prices price
                where price.item = g.id
                AND price.currency = cur.currency_id
                AND price.valid_since <= current_timestamp() 
                AND (price.valid_until IS NULL OR current_timestamp() <= price.valid_until)             
                ORDER BY price.id DESC 
                LIMIT 1
            ) AS 'price'   
            FROM good_deal g
            JOIN  items i ON i.id = g.id
            LEFT JOIN l10ns l10n ON l10n.id = :l10n 
            LEFT JOIN item_l10ns i_l10n ON (i_l10n.item = i.id AND i_l10n.l10n = l10n.id) 
            LEFT JOIN designs d ON d.id = g.graphic_range
            LEFT JOIN color_l10ns color ON (color.color = g.color_ref AND color.l10n = l10n.id)
            LEFT JOIN vue_vehicle_2 v ON (v.id = g.vehicle AND v.l10n = l10n.id)
            LEFT JOIN category_default_content df ON (
                df.l10n = 1 
                AND df.category = (
                    SELECT c.id
                    FROM categories node 
                    LEFT JOIN  categories c ON ((c.node_left <= node.node_left AND c.node_right >= node.node_right) AND c.workspace = node.workspace)
                    LEFT JOIN category_default_content df ON (df.category = c.id AND df.l10n = l10n.id)
                    WHERE node.id = g.department
                    AND df.category IS NOT NULL
                    ORDER BY c.node_left DESC
                    LIMIT 1
                )
            )
            LEFT JOIN behaviors b ON b.id = i.behavior
            LEFT JOIN country ON country.country_iso = :c_iso
            LEFT JOIN currency cur ON cur.currency_id = :cur
            WHERE g.id = :id;
        ";
        $product = $this->query($sql, $params, true);
        if($product):
            $this->setEntity(null);
            $product->suitable =  $this->query("SELECT iv.vehicle AS 'id', iv.fullname, iv.principal FROM vue_item_vehicles iv WHERE iv.item = :item;", ['item' => $id]);
            $product->files = $this->files($id);
        endif;
        return $product;*/
    }

    public function readBySlug(string $slug){
        $sql = "SELECT p_l10n.title,
        p_l10n.content,
        p_l10n.page,
        p_l10n.slug,
        p.layout,
        p.template,
        p_l10n.meta_title,
        p_l10n.meta_description, 
        p_l10n.cover,
        p_l10n.cover_portrait,
        p_l10n.short_description,
        p_l10n.faq,
        b.brand,
        p.category,
        p.d_store,
        p_l10n.l10n AS 'l10n_id'
        FROM page_l10ns AS p_l10n
        JOIN pages p ON (p.id = p_l10n.page AND p.website = :website)
        LEFT JOIN l10ns ON l10ns.id = :l10n
        LEFT JOIN brand_pages b ON b.page = p.id
        WHERE p_l10n.slug = :slug
        AND p_l10n.l10n = l10ns.id;";
        $q = $this->query($sql, ['slug' => $slug, 'l10n' => $this->getL10nId(), 'website' => WEBSITE_ID], true);
        if($q) {
            $this->page = $q;
            $this->page->l10ns = $this->setL10ns();
        }
        return $this->page;
    }
    public function setL10ns(){
        $this->setEntity(null);
        $sql = "SELECT p_l10n.page AS 'page_id', 
        CONCAT_WS('/', :fqdn, l10ns._prefix, p_l10n.slug) AS 'slug', 
        p_l10n.l10n AS 'l10n_id', 
        l10ns._locale,
        CASE WHEN p_l10n.l10n = :l10n_id THEN 'current' ELSE '' END AS 'class' 
        FROM page_l10ns p_l10n
        JOIN pages p ON p.id = p_l10n.page 
        JOIN l10ns ON l10ns.id = p_l10n.l10n        
        WHERE p_l10n.page = :page_id     
        ;";
        return $this->query($sql, ['fqdn'=>FQDN,'page_id'=> $this->page->page, 'l10n_id' => $this->page->l10n_id]);
    } 

    public function slugsById(int $page_id)
    {
        $this->entity = null;
        $sql = "SELECT 
        p_l10n.slug,
        p_l10n.l10n,
        l10n.url,
        p_l10n.title AS 'name',
        i18n.name AS 'designation', 
        CASE WHEN p_l10n.l10n = :l10n THEN 1 ELSE 0 END AS 'current',
        CASE WHEN l10n.id = 6 THEN 'en' WHEN l10n.id = 9 THEN 'en' ELSE l10n.name END AS 'short_designation'  
        FROM pages p      
        LEFT JOIN page_l10ns AS p_l10n ON p_l10n.page = p.id        
        LEFT JOIN l10ns AS l10n ON l10n.id = p_l10n.l10n
        LEFT JOIN internationalisation AS i18n ON i18n.id = l10n.i18n
        WHERE p.id = :page_id
        AND p.website = :website
        AND p_l10n.l10n IN (1,3,10);";
       return $this->query($sql, array('page_id' => $page_id, 'l10n' => $this->getL10nId(), 'website' => WEBSITE_ID));
    }

    public function files($id) { 
        $this->setEntity('File'); 
        $files = [];        
        $sql = "SELECT 
            f.id,
            f.file_id, 
            f.url, 
            f.type,
            f.product,             
            f.associate,
            f.cover, 
            f.position        
            FROM vue_files AS f             
            WHERE f.product = :id AND f.associate = 'on'
            ORDER BY f.cover DESC, f.position IS NOT NULL DESC, f.position ASC;";
        $files = $this->query($sql, ['id' => $id]);             
        return $files;               
    }

    public function reinsuranceOnCart(int $website = 5)
    {
        $this->setEntity(null);
        $sql = "SELECT 
        ri.designation,
        ri.body,
        ri.icon,
        ri.link,
        ri.l10n,
        ri.icon_type
        FROM reinsurance AS r
        JOIN reinsurance_items AS ri ON (ri.reinsurance = r.id AND ri.l10n = :l10n)        
        WHERE r.website = :website 
        AND r.place = 'cart'
        AND ri.position >= 1
        ORDER BY ri.position;";

        return $this->query($sql, ['website' => $website, 'l10n' => $this->getL10nId()]);
    }
}