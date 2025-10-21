<?php
declare(strict_types=1);
namespace App\Checkout\PSP\PayPal;

use App\Checkout\PSP\PayPal;

/**
 * Compte Sandbox PayPal https://www.sandbox.paypal.com/
 * compte businnes : sb-dnqei27799314@business.example.com / KV4g+h6+KV4g+h6+
 * compte acheteur sebastien.gay-buyer@hotmail.com / mBMGZm-k3H22c>_ * 
 * 
 */
final class Sandbox extends PayPal
{
    protected $appName = 'KutvekSandboxApp';
    protected string $endpoint_url = 'https://api-m.sandbox.paypal.com';
    protected string $clientID = 'AXpWAW4J7Iv-07bblZ4JIwYyc9FVQZ_0UrE2gb_M706hLvqP7xUPrb8PA7WBgG-4huZkUL7NdTj44A6w';   
    protected string $secret = 'EI7FMKpC9IHlET35iAmb6-yB95QDdhEfNecmieLKVc6kzVc8CkFulH2b_C9ufz-mUpMwHE2SHes_zvAz';        
    public function getClientID(): string {return $this->clientID;}
    public function getEndpointUrl(): string {return $this->endpoint_url;}    
    public function getSecret(): string {return $this->secret;}
}