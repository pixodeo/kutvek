<?php
declare(strict_types=1);
namespace Library\Security;

class Cookie {

	public $xsrfToken;
    private $_secret    = "EN8filUn2VikM_RqU0xvB7o_56nV4obHXm_RPsuUyFlmmgg1QCl2bCyJeRBSYGEsngYvzGrM2Dtu48Z0";
    private $_algo = 'sha384';
    private false|string $_cookie = false;
    private $_cookie64;
    
    /**
     * Sets the cookie.
     *
     * @param      string  $data   le cookie
     */
    public function getCookie():false|object {
        return $this->_cookie ? json_decode($this->_base64url_decode($this->_cookie)) : false;
    }

    public function isValid(false|string $cookie): bool 
    {
        if(!$cookie) return false;

        $elements = explode('.', $cookie);
        list($cookie, $signature) = $elements;
        $this->_cookie = $cookie;

        // On compare les hash
        $signature_hash_2 = hash_hmac($this->_algo, $cookie, $this->_secret);
        return hash_equals($signature, $this->_base64url_encode($signature_hash_2));
    }  
      
    /**
     * Créer un cookie sécurisé
     *
     * @param      <type>  $data  Cookie info
     *
     * @return     string  ( description_of_the_return_value )
     */
    public function create($data): string
    {
        $this->_cookie64 = $this->_base64url_encode(json_encode($data));
        $hmac = $this->_hmac($this->_cookie64);
        return $this->_cookie64 . '.' . $this->_base64url_encode($hmac);
    }

    private function _hmac(string $data, bool $binary = false)
    {
        $hash =  hash_hmac($this->_algo, $data, $this->_secret, $binary);
        return $hash;
    }

    private function _base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function _base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), 4 - ((strlen($data) % 4) ?: 4), '=', STR_PAD_RIGHT));
    }
}