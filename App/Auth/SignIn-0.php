<?php
declare(strict_types=1);
namespace App\Auth;

use App\AppAction;
use Error;
use Exception;
use Library\{Security\TraitJWT, Security\Cookie};
use Domain\Table\Identity;


/**
 * Ajoute une adresse de livraison à domicile 
 */
class SignIn extends AppAction {
	use TraitJWT;

	public function __invoke() {
		$this->_response =  $this->_response->withStatus(400);
		$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
        $this->_response->getBody()->write('{}');
        return $this->_response;
		//$table = new Identity($this->_setDb());
		try {
			


			$login = filter_var($login, FILTER_SANITIZE_EMAIL);
			if(!filter_var($login, FILTER_VALIDATE_EMAIL)) throw new Exception('Wrong ids');

			$userInfo = $table->login($login);
			$this->_response =  $this->_response->withStatus(400);
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
            $this->_response->getBody()->write('{}');
            return $this->_response;


			if(!$userInfo) throw new Exception('Wrong ids, nobody'); 
			if($userInfo->pwd_hash === null  && $userInfo->pwd === null) throw new Exception('Wrong ids'); 
			$pwd_hash = $userInfo->pwd_hash ?? false; 
			if(!$pwd_hash && $userInfo->pwd !== null):
				$pwd_hash = $this->_hashpwd($userInfo->pwd);
				$table->update($userInfo->id, ['pwd_hash' => $pwd_hash]);
			endif;

			if(!password_verify($password, $pwd_hash)) throw new Exception('Wrong ids');

			// CSCRF token à ajouter au payload du JWT
            $csrf_token = bin2hex(\random_bytes(96)); 
            $payload = [
                'iss'   	=> DOMAIN,
                'iat'   	=> $this->getIat(),
                'exp'   	=> $this->getExp(),
                'aud'   	=> DOMAIN,
                'sub'   	=> $userInfo->id,
                'fullname'	=> $userInfo->fullname,               
                'email' 	=> $userInfo->email,
                'role'  	=> $userInfo->role,
                'workspace' => $userInfo->workspace,
                'xsrfToken' => $csrf_token
            ];

            $jwt = $this->generateJWT($payload);
            $bytes = \random_bytes(128);
            $refreshToken = $this->base64url_encode(bin2hex($bytes));
            $table->saveRefreshToken($userInfo->id,$refreshToken);

            // durée des token
            $accessTokenExpiresIn = $this->getExp();
            $refreshTokenExpiresIn = $this->getExp()+3600*24*365;

			// cookies de session 
            $cookieClass = new Cookie;
            $sessionToken = $cookieClass->create(['uid' => $userInfo->id, 'name' => $userInfo->fullname, 'type' => $userInfo->type, 'rebate' => $userInfo->rebate, 'payLater' => $userInfo->payLater]);

            $access_cookie = ['expires' => $accessTokenExpiresIn, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'strict'];
            setcookie('access_token',  $jwt, $access_cookie);

            $refresh_cookie = ['expires' => $refreshTokenExpiresIn, 'path' => '/auth/token', 'secure' => true, 'httponly' => true, 'samesite' => 'strict'];
            setcookie('refresh_token', $refreshToken, $refresh_cookie);

            // session_token
           	$session_cookie = ['expires' => $refreshTokenExpiresIn, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'strict'];
            setcookie('session_token', $sessionToken, $session_cookie);

            $json = json_encode((object)[
            	'xsrfToken'             => $csrf_token, 
                'accessTokenExpiresIn'  => $accessTokenExpiresIn, 
                'refreshTokenExpiresIn' => $refreshTokenExpiresIn,
                'sub'					=> $userInfo->id
            ]);
            $this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
            $this->_response->getBody()->write($json);
            return $this->_response;
		}
		catch(Exception|Error $e){
			$this->_response =  $this->_response->withStatus(400);
			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
			$this->_response->getBody()->write(['msg' => $e->getMessage()]);
		}
	}

	private function _hashpwd(string $password) {
        $options = ['cost' => 9];
        $password_hash = \password_hash($password, PASSWORD_BCRYPT, $options);
        return $password_hash;
    } 
}