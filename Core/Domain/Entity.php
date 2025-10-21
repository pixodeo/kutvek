<?php
declare(strict_types=1);
namespace Core\Domain;

use Core\Library\TraitL10n;
use Core\Routing\{RouteInterface,TraitRequest};
use Psr\Http\Message\ServerRequestInterface;

#[\AllowDynamicProperties]
class Entity
{
    
    use TraitL10n, TraitRequest;
    public $tax_rate = 20;  
    protected RouteInterface $_route;   

    public function __get($key)
    {
        $method = 'get' . ucfirst($key);
        $this->$key = $this->$method();
        return $this->$key;
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

    protected function _withVAT(float $price):float{
        if($this->country_vat > 0) return $price *  (1 + ($this->tax_rate / 100));
        return $price;
    }

     public function setRoute(RouteInterface $route): void {$this->_route = $route;}   
}