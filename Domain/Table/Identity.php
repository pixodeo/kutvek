<?php
declare(strict_types=1);
namespace Domain\Table;
use Core\Domain\Table;

class Identity extends Table {

	public function login(string $login){
		$sql = "SELECT
		u.id,
		u.email,
		u.pwd_hash,
		u.pwd,
		u.workspace,
		'customer' AS 'role',
		JSON_OBJECT('id', u.workspace, 'scope', wsp.strategy) AS 'workspace',
		CONCAT_WS(' - ', UPPER(b_c.company), CONCAT_WS(' ', u.firstname, UPPER(u.lastname))) AS 'fullname',
		CASE WHEN b_c.id IS NOT NULL THEN 'pro' ELSE 'std' END AS 'type',
		CASE WHEN b_c.id IS NOT NULL THEN b_c.rebate ELSE '0.00' END AS 'rebate',
    	CASE WHEN b_c.id IS NOT NULL THEN b_c.deferred_payment ELSE 0 END AS 'payLater'
		FROM user AS u
    	LEFT JOIN business_customer AS b_c ON b_c.id = u.id
    	LEFT JOIN workspaces AS wsp ON wsp.id = u.workspace
    	WHERE u.email = :login AND u.workspace = :ws;
		";
		return $this->query($sql, ['ws' => WORKSPACE, 'login' => $login], true);
	}

	public function saveRefreshToken(int $user, string $refreshToken) {
    	$sql = "INSERT INTO user_tokens (user, token, active) VALUES(:user, :token, :active);";
    	return $this->query($sql, ['token' => $refreshToken, 'active' => 1, 'user' => $user], true);
  	}
}