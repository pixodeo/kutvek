<?php
declare(strict_types=1);
namespace Library\HTML;

trait TraitImg {
	public function img(string $img_url){

		$curl = curl_init($img_url);        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSLVERSION, 6);
		curl_setopt($curl, CURLOPT_NOBODY, true);
        $resp = curl_exec($curl);
        $info = curl_getinfo($curl);
        $errno = curl_errno($curl);

        curl_close($curl);
        if((int)$info['http_code'] === 404):
         	return false;	
        else:
        	return  true;	
        endif;
	}
}