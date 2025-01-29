<?php

namespace App\Http\Services\SMS;

use App\Http\Contracts\SMS;

class SMSService implements SMS
{
    private $provider;

    public function __construct(SMS $provider) {
        $this->provider = $provider;
    }

    public function send(string $number, string $brand_name, string $message): bool
    {
        if (strlen($message) > 140) {
            throw new \Exception('SMS maximum length is 140 characters');
        }

        return $this->provider->send($number, $brand_name, $message);
    }
}
