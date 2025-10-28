<?php
declare(strict_types=1);
namespace Library\Security;

trait TraitJWT {

	public $xsrfToken;
    private $_secret    = "EN8filUn2VikM_RqU0xvB7o_56nV4obHXm_RPsuUyFlmmgg1QCl2bCyJeRBSYGEsngYvzGrM2Dtu48Z0";
    private $_algo = 'sha384';
    private $_header = ['alg' => 'HS384', 'typ' =>  'JWT'];
    protected $_payload;
    private $_header64;
    private $_payload64;
    private $_signature;
    private $_iat = null; // in seconds
    private $_expTime = 3600; // in seconds default 1 hour
    private $_exp = null; // in seconds
    protected $_accessToken;
    private $_iss = DOMAIN;


    public function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), 4 - ((strlen($data) % 4) ?: 4), '=', STR_PAD_RIGHT));
    }

    public function isValid(string $token): bool 
    {
        $elements = explode('.', $token);

        list($header, $payload, $signature) = $elements;
        $this->_payload = $payload;
        // On compare les hash
        $signature_hash_2 = hash_hmac($this->_algo, $header . '.' . $payload, $this->_secret);
        return hash_equals($signature, $this->base64url_encode($signature_hash_2));
    }

    public function getPayload(){
        return $this->_payload ? json_decode($this->base64url_decode($this->_payload)) : false;
    }

    public function check(string $claim = 'exp'): bool {
        switch($claim){
            case 'exp':
                return $this->_checkExp();
                break;
            case 'xsrf':
                return $this->_checkXsrfToken();
                break;
        }
        return false;
    }

    
    private function _checkExp(): bool {
        return false;
    }

    /**
     * On vérifie que le token csrf est identique à celui du payload
     *
     * @return     bool  ( description_of_the_return_value )
     */
    private function _checkXsrfToken(): bool
    {
        return $this->getPayload()->xsrfToken === $this->xsrfToken;
        
    }

    /**
     * Compare la durée de validité
     */
    public function isExpired() {
        $now = new \DateTimeImmutable();
        //$iat = $now->setTimestamp();        
    }

    protected function generateJWT($payload): string
    {
        $this->_header64 =  $this->base64url_encode(json_encode($this->_header));
        $this->_payload64 = $this->base64url_encode(json_encode($payload));
        $this->_accessToken = $this->_signatureHMAC()->_jwt();
        return $this->_accessToken;
    }

    private function _signatureHMAC(){
        $data = $this->_header64 . '.' .$this->_payload64;
        $this->_signature = hash_hmac($this->_algo, $data, $this->_secret);
        return $this;
    }
    
    private function _jwt(): string
    {
        return $this->_header64 . '.' . $this->_payload64 . '.' . $this->base64url_encode($this->_signature);
    }   

    /** Ajout date expiration */
    protected function _setExp()
    {
        $this->_exp = $this->getIat() + $this->_expTime;
        //iat et exp vont dans le payload
        //$this->_header['iat'] = $this->_iat;
        //$this->_header['exp'] = $this->_exp;
        return $this->_exp;
    }

    public function getExp(){
        return $this->_exp ?? $this->_setExp();
    }

    private function _setIat(){
        $this->_iat = time();
        return $this->_iat;
    }

    public function getIat(){
        return $this->_iat ?? $this->_setIat();
    }

    protected function setAud(string $aud) {
        $this->_aud = $aud;
    }

    protected function _hmac(string $data, bool $binary = false)
    {
        $hash =  hash_hmac($this->_algo, $data, $this->_secret, $binary);
        return $hash;
    }
}