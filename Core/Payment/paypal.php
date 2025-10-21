<?php 
$password = '3ZP3B26EY246WCW3';
$signature = 'AwxPW2ytsIcGqA2BCFyBuPeBrrfwAVPn9AoB8rOwkxno8SZPqB2OJsFX';
$user = 'sebastien.gay-facilitator_api1.hotmail.com';
$params = array(
'METHOD' => 'SetExpressCheckout',
'USER'		=> $user;
'PWD'		=> $password;
'RETURNURL'	=> 'https://dev.kutvek.com/process.php',
'CANCELURL'	=> 'https://dev.kutvek.com/cancel.php',
'PAYMENTREQUEST_0_AMT'	=> ,// total ttc
'PAYMENTREQUEST_0_CURRENCYCODE'
)

$endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => $endpoint,
	CURLOPT_POST => 1,
	CURLOPT_POSTFIELDS => $params
	CURLOPT_RETURNTRANSFER => 1
	CURLOPT_VERBOSE => 1

));
$response = curl_exec($curl);
curl_close($curl);

var_dump($response);die();
