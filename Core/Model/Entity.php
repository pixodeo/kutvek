<?php
namespace Core\Model;

use Core\Domain;

class Entity extends Domain
{
    public $tax_rate = 20;

   

    public function __get($key)
    {
        $method = 'get' . ucfirst($key);
        $this->$key = $this->$method();
        return $this->$key;
    }

    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
            //if (property_exists($this, $key)) {
            //    $this->{$key} = $value;
            //}
        }
    }

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
     *  en attendant \Transliterator::createFromRules
     */
    protected function _transliterate($string)
    {
        return (str_replace(array('\'', '/', ' ', 'é', 'É', 'è', 'à', 'â', '--', ':-'), array('-', '-', '-', 'e', 'e', 'e', 'a', 'a', '-', '://'), $string));
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
