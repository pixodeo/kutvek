<?php 
declare(strict_types=1);
namespace App\Checkout\PSP\PayPal;

use App\Checkout\PSP\PayPal;

final class Live extends PayPal
{
    protected string $endpoint_url = 'https://api-m.paypal.com';
    protected string $clientID = 'Ae46z7J-oLV-fKLeNli8mNfq87boL79IbRe8RtWBv0WwCAzMWC7-_WbqAiv48byk-uvFjEiRA9tjHLR2';    
    protected string $secret = 'EI86YCI-OEbhSIgBa-RmqYTvNkXP-8q2i1cO5iERQuHShfwYnJZzD-jGkZ1guu-fqlTtTo_i8eIAQs4P';
    public function getClientID(): string{return $this->clientID;}
    public function getEndpointUrl(): string {return $this->endpoint_url;}
    public function getSecret(): string {return $this->secret;}    
}