<?php
namespace Core\Library;

trait TraitUtils {    
    public $_trustpilot;
    /* Utile pour les array_filter, ne filtre que les null */

    public function is_not_null($val){
        return !is_null($val);
    }

     // this removes whitespace and related characters from the beginning and end of the string
    public  function trim_value($value)
    {   if(!is_array($value)) {
            
           return trim($value);
        } else return array_filter($value);
    } 

    public function arem($array,$value){
        $holding=array();
        foreach($array as $k => $v){
            if($value!=$v){
                $holding[$k]=$v;
            }
        }    
        return $holding;
    }

    public function akrem($array,$key){
        $holding=array();
        foreach($array as $k => $v){
            if($key!=$k){
                $holding[$k]=$v;
            }
        }    
        return $holding;
    }

    public function debug($value) {
        $debug = '<pre>';
        $debug .= print_r($value, 1);
        $debug .= '</pre>';
        die($debug);
    }

    public function array_remove(array $array = [], array $keys = [])
    {
        foreach($keys as $key)
        {
            unset($array[$key]);
            return $array;
        }
    }

    public function is_assoc($var)
    {
        return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
    }


    public function widgetSlugs(array $slugs)
    {
        $items = array();
        foreach($slugs as $slug)
        {       
            if((int)$slug->current === $this->getL10nId()) $item = '<li class="current">';
            else $item = '<li>';            
            $item .= '<a href="' .  $this->matchPrefixUriFDQN($slug->l10n, $slug->slug) . '" class="trad-link" data-l10n="'. $slug->l10n .'">';          
            $item .= '<span class="short-desc">' . $slug->short_designation. '</span>';            
            $item .= '</a>';
            $item .= '</li>';
            $items[] = $item;
        }
        $ul = '<ul data-l10n="' . $this->getL10nId() . '">';        
        $ul .= implode('', $items);
        $ul .= '</ul>';     
        return $ul;
    }

    public function widgetCountryCurrencySelector()
    {
        $table = $this->getTable('store');

        $sql = "SELECT 
        c.id, 
        c.country_iso, 
        CONCAT('/img/flags/1x1/', LOWER(c.country_iso),'.svg') AS 'flag', 
        CASE WHEN :l10n = 1 THEN c.name_fr ELSE c.name_en END AS 'name',
        CONCAT_WS(' ', cur.currency_symbol, cur.currency_lib) AS 'text',
        c.default_currency,
        cur.currency_lib,
        cur.currency_symbol
        FROM country AS c
        JOIN currency AS cur ON cur.currency_id = c.default_currency
        ORDER BY  preferences_position DESC, name";
        $countries = $table->query($sql, ['l10n' => $this->getL10nId()]);
        $currentCurrency = "";
        $currentCountry = null;
        
        foreach ($countries as $country) {
            if ($country->country_iso === $this->getCountry()) {
                $currentCountry = $country;
                $currentCurrency = $country->text;
                break;
            }
        }
        $this->_path = $this->_path = $this->_widgetPath;
        $this->_view = 'widgets.country-selector';
        return $this->partial(compact('currentCountry', 'currentCurrency'));
    }

    public function topMenu():void{}
   
    public function widgetTrustpilot(string $type = 'star')
    { 
        //return $path;     
        if (is_null($this->_trustpilot)) {
            $table = $this->getTable('store');             
            $this->_trustpilot = $table->trustpilot();
        }
        $trustpilot = $this->_trustpilot;
        $lang = $this->getLang();
        if ($type == 'star') {
            $this->_path = $this->_widgetPath;
            $this->_view = 'widgets.trustpilot-microstar';
            return $this->partial(compact('trustpilot', 'lang'));
        }
        $this->_path = $this->_widgetPath; 
        $this->_view = 'widgets.trustpilot-slider';
        return $this->partial( compact('trustpilot', 'lang'));
    }

    public function socials(): string
    {
        $table = $this->getTable('store');    

        $sql = "SELECT
        f_i.name,
        f_i.icon,
        f_i.external_link,
        f_i.depth
        FROM vue_footer_items AS f_i
        LEFT JOIN l10ns AS l10n ON l10n.id = :l10n
        WHERE f_i.node_left <= 8 AND f_i.l10n = l10n.id AND website = :website
        ORDER BY f_i.id;";

        $socials = $table->query($sql, ['website' => WEBSITE_ID, 'l10n' => $this->getL10nId()]);  
        
        $bloc = '<p class="h5">'. mb_strtoupper($socials[0]->name) .'</p>';
        $bloc .= '<div class="social-medias">';
        foreach ($socials as $social) {
            if ($social->depth >= 1) {
                $bloc .= '<a href="'. $social->external_link .'" target="_blank">';
                $bloc .= '<img src="'. $social->icon .'"></a>';
            }
        }
        $bloc .= '</div>';
        return $bloc;
    }    
}