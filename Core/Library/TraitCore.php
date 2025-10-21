<?php
namespace Core\Library;

trait TraitCore {
	
	protected $_i18n = 'fr';
	protected $_l10n = null;
	protected $_locale = 'fr_FR';
	protected $_l10nUri;
	protected $_currency = 'EUR';
	protected $_currencyId = 1;
	protected $_currencySymbol = ' €';
	protected $_platform = 'fr';
	protected $_website = 1;
	protected $_prefixUrl = null;
	protected $_oldPrefixUrl = null;
	protected $_l10nId = 1;
	protected $_i18nId = 3;
	protected $_country = 'FR';
	public $_workplace = 6;
	public $_workspace = 2;
	public $param = 'fr';
	public $intlphone = '+33385303024';
	public $phone = '03 85 30 30 24';
	public $_countryL10nId = 0;
	protected $_prefixes = ['en','it', 'es', 'pt', 'de', 'en-gb', 'en-uk', 'fr-ca', 'fr', 'en-ca', 'en-us', 'en-fr', 'fr-fr'];

	public function setI18n($i18n = 'fr')
	{
		switch($i18n)
		{	
			case 'fr':                         
                $this->_l10nUri = 'fr'; 
                $this->_oldPrefixUrl = 'fr-fr';                
                break; 	
            case 'fr-fr':               
                $this->_l10nUri = 'fr';  
                $this->_prefixUrl = 'fr-fr';                                           
                break; 				
			case 'fr-ca':
				$this->_i18n = 'fr';
				$this->_l10n = 'ca';
				$this->_currency = 'CAD';
				$this->_currencyId = 2;
				$this->_currencySymbol = ' $CA';
				$this->_l10nUri = 'fr-ca';
				$this->_prefixUrl = 'fr-ca';				
				$this->_platform = 'ca';
				$this->_website = 4;
				$this->_l10nId = 2;
				$this->_locale = 'fr_CA';	
				break;			
			case 'en':
				$this->_i18n = 'en';
				$this->_l10nUri = 'en';
				$this->_prefixUrl = 'en';
				$this->_oldPrefixUrl = 'en-fr';							
				$this->_l10nId = 3;	
				$this->phone = '+33 385 303 024';
				$this->_i18nId = 2;	
				$this->_locale = 'en';	
				break;
			case 'it':
				$this->_i18n = 'it';
				$this->_l10nUri = 'it';
				$this->_prefixUrl = 'it';									
				$this->_l10nId = 10;	
				$this->phone = '+33 385 303 024';
				$this->_i18nId = 6;	
				$this->_locale = 'it';	
				break;
			case 'es':
				$this->_i18n = 'es';
				$this->_l10nUri = 'es';
				$this->_prefixUrl = 'es';									
				$this->_l10nId = 11;	
				$this->phone = '+33 385 303 024';
				$this->_i18nId = 5;	
				$this->_locale = 'es';	
				break;	
			case 'en-fr':
				$this->_i18n = 'en';				
				$this->_prefixUrl = 'en-fr'; 
				$this->_l10nUri = 'en-fr';								
				$this->_l10nId = 3;
				$this->phone = '+33 385 303 024';
				$this->_i18nId = 2;
				$this->_locale = 'en';	
				break; 
			case 'en-ca':
				$this->_i18n = 'en';
				$this->_l10n = 'ca';
				$this->_l10nUri = 'en-ca';
				$this->_prefixUrl = 'en-ca';  
				$this->_currency = 'CAD';	
				$this->_currencyId = 2;			
				$this->_platform = 'ca';
				$this->_currencySymbol = ' $CA';
				$this->_website = 4;
				$this->_l10nId = 5;
				$this->_i18nId = 2;
				$this->_locale = 'en_CA';	
				break;
			case 'en-us':
				$this->_i18n = 'en';
				$this->_l10n = 'us';
				$this->_l10nUri = 'en-us'; 
				$this->_prefixUrl = 'en-us'; 
				$this->_currency = 'USD';	
				$this->_currencyId = 3;			
				$this->_platform = 'us';
				$this->_website = 2;
				$this->_l10nId = 4;
				$this->phone = '450-598-1140';
				$this->intlphone = '4505981140';
				$this->_currencySymbol = ' $US';
				$this->_i18nId = 2;
				$this->_locale = 'en_US';	
				break;
			case 'en-gb':
				$this->_i18n = 'en';
				$this->_l10n = 'gb';
				$this->_l10nUri = 'en-gb'; 
				$this->_prefixUrl = 'en-gb'; 
				$this->_oldPrefixUrl = 'en-uk'; 
				$this->_currency = 'GBP';
				$this->_currencySymbol = ' £';
				$this->_currencyId = 4;					
				$this->_platform = 'gb';
				$this->_website = 3;
				$this->_l10nId = 6;
				$this->phone = '+33 385 303 024';
				$this->_i18nId = 2;
				$this->_locale = 'en_GB';	
				break;	
			case 'en-uk':
				$this->_i18n = 'en';
				$this->_l10n = 'gb';
				$this->_l10nUri = 'en-uk'; 
				$this->_prefixUrl = 'en-uk'; 
				$this->_currency = 'GBP';
				$this->_currencySymbol = ' £';
				$this->_currencyId = 4;					
				$this->_platform = 'gb';
				$this->_website = 3;
				$this->_l10nId = 6;
				$this->phone = '+33 385 303 024';
				$this->_i18nId = 2;
				$this->_locale = 'en_GB';	
				break;			
			case 'nl':
				$this->_i18n = 'nl';
				$this->_l10nUri = 'nl'; 				
				$this->_prefixUrl = 'nl';
				$this->_l10nId = 7;
				$this->phone = '+33 385 303 024';
				$this->_i18nId = 4;
				break;
			case 'de':
				$this->_i18n = 'de';
				$this->_l10nUri = 'de';				
				$this->_prefixUrl = 'de';
				$this->_l10nId = 8;
				$this->phone = '+33 385 303 024';
				$this->_i18nId = 1;
				break;
			default:				
				$this->_l10nUri = 'fr'; 			
				break;							
		}
		$this->param = $i18n;
		
		
		/*if(is_object($this->form))
		{
			$this->form->setI18n($i18n);
		}*/
	}

	public function matchPrefixUri(int $l10n, string $url)
	{
		$format_url = match ($l10n) {
		    1 => '/' . $url,
		    3 => '/en/' . $url,
			4 => '/en/' . $url,
		    6 => '/en/' . $url,
		    9 => '/en/' . $url,
		    10 => '/it/'. $url
		};
		return $format_url;
	}

	public function matchPrefixUriFDQN(int $l10n, string $url)
	{
		$format_url = match ($l10n) {
		    1 => '/' . $url,
		    2 => '/' . $url,
		    3 => '/en/' . $url,
			4 => '/en/' . $url,
		    6 => '/en/' . $url,
		    9 => '/en/' . $url,
		    10 => '/it/'. $url,
		};
		return DOMAIN . $format_url;
	}

	public function withoutPrefix(string $url){
		$explode = explode('/', $url);
       	$langs = array('en', 'en-gb','it','es', 'de', 'en-uk', 'fr-ca', 'fr', 'en-ca', 'en-us', 'en-fr', 'fr-fr');
        if(in_array($explode[0], $langs)){
            unset($explode[0]);
        }       
        return implode('/', $explode);
	}

	public function trimUrl(string $url){
		$explode = array_values(array_filter(explode('/', $url)));		
		if(in_array($explode[0], $this->_prefixes)){
            unset($explode[0]);
        }
		return implode('/', $explode);		
	}

	public function getWorkplace() {
		return $this->_workplace;
	}

	public function getWorkspace() {
		return $this->_workspace;
	}

	public function getI18n(bool $param = true)
	{
		//return $this->_i18n;
		return $param ? $this->param : $this->_i18n;
	}

	public function getLocale(){
		return $this->_locale;
	}

	public function getL10n()
	{
		return $this->_l10n === null ? $this->_i18n : $this->_i18n . '_' . strtoupper($this->_l10n);
	}

	/**
	 * Fabrique la valeur pour l'attribut lang des balises HTML
	 * <html lang ="en-GB">
	 */
	public function getLang() {
		return $this->_l10n === null ? $this->_i18n : $this->_i18n . '-' . strtoupper($this->_l10n);
	}

	public function getL10nId(){
		return $this->_l10nId;
	}

	public function getL10nUrl()
	{
		return $this->_l10nUri ?? 'fr';
	}

    public function setCurrency($currency){
    	
    	switch ($currency){
    		case 'USD':
    			$this->_currencyId = 3;
    			$this->_currency = $currency;
    			$this->_currencySymbol = '$';
    			break;
    		case 'CAD':
    			$this->_currencyId = 2;
    			$this->_currency = $currency;
    			$this->_currencySymbol = '$';
    			break;
    		case 'GBP':
    			$this->_currencyId = 4;
    			$this->_currency = $currency;
    			$this->_currencySymbol = '£';
    			break;
    		default:
    			$this->_currencyId = 1;
    			$this->_currency = 'EUR';
    			$this->_currencySymbol = '€';
    			break;
    	}
    }

    public function getCurrency()
    {
    	return $this->_currency;
    }

    public function getCurrencySymbol(){
    	return $this->_currencySymbol;
    }

    public function getCurrencyId()
    {
    	return $this->_currencyId;
    }

    public function setPlatform($platform){
    	$this->_platform = $platform;
    }

    public function getPlatform()
    {
    	return $this->_platform;
    }

    public function getWebsite()
    {
    	return $this->_website;
    }

    public function getWebshop()
    {
    	switch($this->_l10nUri)
    	{
    		case 'en-us':
    			return 2;    			
    		case 'en-ca': 
    			return 4;
    		case 'fr-ca':
    			return 4;
    		case 'en-gb':
    			return 3;
    		default:
    			return 1;
    	}
    }

    public function getPrefixUrl()
    {
    	return $this->_prefixUrl;
    }

    public function translation($url)
    {
    	if($this->_prefixUrl){
    		return DS . $this->_prefixUrl . DS . $url;
    	}
    	return $url;
    }

    public function fdqnUrl($url){
    	if($this->_prefixUrl){
    		return DOMAIN . DS . $this->_prefixUrl . DS . $url;
    	}
    	return DOMAIN . DS . $url;
    }

	public function setCountry(string $country)
	{
		$this->_country = $country;
	}

	public function getCountry()
	{
		return $this->_country;
	}

	public function setL10nId(int $l10nId){
		$this->_l10nId = $l10nId;
	}

	public function setCountryL10nId(int $l10n){
		$this->_countryL10nId = $l10n;
	}
}