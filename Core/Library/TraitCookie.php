<?php
declare(strict_types=1);
namespace Core\Library;

trait TraitCookie {
   public function getCookie(false | string $data)
    {
        if(!$data)return false;
        $elements = explode('.', $data);
        list($cookie, $signature) = $elements;        
        return json_decode($this->_base64url_decode($cookie));
    } 

    private function _base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function _base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), 4 - ((strlen($data) % 4) ?: 4), '=', STR_PAD_RIGHT));
    }    
}