<?php
declare(strict_types=1);
namespace Core\HTML;

#[\AllowDynamicProperties]
class Entity
{
    public $tax_rate = 20;  

    public function __get($key)
    {        
        if(!property_exists($this, $key)):
          $this->$key = $this->$key();      
        endif;        
        return $this->$key;
    }

    public function setData(mixed $data){ $this->data = $data;}

    /**
     * Emule la fonction CONCAT_WS SQL
     *
     * @param mixed $glue
     * @param array $pieces
     * @return string
     */
    protected function concatWs($glue = '', array $pieces = []): string
    {
        return implode($glue, array_filter($pieces));
    }
    

    /**
     * Prix TTC / (1 + taux de TVA) = Prix HT
     *
     */
    protected function _substractVAT($price, $vat = 1, $taxRate = 20)
    {
        if ($price !== null) {
            return (bool)$vat ? $price : $price / (1 + ($taxRate / 100));
        }
        return $price;
    }
}