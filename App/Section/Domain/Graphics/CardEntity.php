<?php
declare(strict_types=1);
namespace App\Section\Domain\Graphics;

use Core\Model\Entity;

class CardEntity extends Entity
{
    public $title = null;
    public $currency = 'EUR';
    public $l10n = 'fr';
    public $visual;
    public $mockup;
    public $plural_title;
    public $trad_id = null;
    public $url;
    public $slug;
    public $designation;
    public $promo = null;
    public $offerPicto = '/img/blank.png';
    public $offer;
    public $offerf;
    public $is_pro = false;
    public $price = 0;
    public $prix = 0;
    public $price_2 = 0;
    public $prices = null;
    public $priceMin = false;
    public $priceMax;
    public $price_0 = 0;
    public $vat = 1;
    public $tax_rate = 20;
    public $f_slug;

    public function __construct()
    {
        $this->price = $this->_substractVAT($this->price, $this->vat);
        $this->price_2 = $this->_substractVAT($this->price_2, $this->vat);
        $this->prix = $this->_substractVAT($this->prix, $this->vat);

        if($this->promo !== null && !$this->is_pro){
            $this->promo = json_decode($this->promo);
            $this->offerPicto = $this->promo->img;
            if($this->promo->type == 'rate'){
                $p = $this->price ?? $this->price_2 ?? $this->prix;
                $this->offer = $p - ($p * $this->promo->discount / 100);
                $this->offerf = $this->_setPrice($this->offer);
            }
        }
        
        $this->price_0 = $this->_setPrice($this->price ?? $this->price_2 ?? $this->prix, 2);

        if($this->prices !== null){
            $this->prices = json_decode($this->prices);
            $this->prices->min = $this->_substractVAT($this->prices->min, $this->vat);
            $this->prices->max = $this->_substractVAT($this->prices->max, $this->vat);

            if($this->prices->max > $this->prices->min)
                $this->priceMin = number_format($this->prices->min, 2, ',', ' ');

            $this->priceMax = $this->prices->max ? $this->_setPrice($this->prices->max, 2) : $this->price_0;
        } else $this->priceMax = $this->price_0;

        $search = ['{{vehicle_fullname}}','{{design_name}}', '{{color_name}}', '{{type}}'];
        $replace = [$this->vehicle_name, $this->design_name, $this->color_name, null];
        $this->designation = mb_strtolower(str_replace(
            $search,
            $replace,
            $this->designation ?? ''
        ));

        if ($this->old_url) {
            $this->url = '/' . $this->old_url;
        } else {
            $this->slug = str_replace(
                array('{{family}}', '{{design}}', '{{brand}}', '{{vehicle}}', '{{color}}', '{{type}}'),
                array($this->f_slug, $this->design_name, $this->brand_name, $this->name, $this->color_name),
                $this->slug ?? ''
            );
            $translit = \Transliterator::createFromRules("::Latin-ASCII; ::Lower; [^[:L:][:N:]]+ > '-';");
            $this->slug = trim($translit->transliterate($this->slug), '-');
    
            $url_fr = '/' .$this->slug . '-' . $this->id;
            $url = '/' . $this->_locale . '/' . $this->slug . '-' . $this->id;
            $this->url = ($this->l10n == 'fr' ? $url_fr : $url);
        }
    }

    protected function _setPrice($price = 0, $digits = 2)
    {
        $a = new \NumberFormatter($this->_locale, \NumberFormatter::CURRENCY);
        $a->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $digits);
        return $a->formatCurrency((float)$price, $this->currency_code); // outputs €12.345,12
    }    

   
}