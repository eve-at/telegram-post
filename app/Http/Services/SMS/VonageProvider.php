<?php

namespace App\Http\Services\SMS;

use App\Http\Contracts\SMS;
use Illuminate\Support\Facades\Log;

class VonageProvider implements SMS
{
    private $client;

    public function __construct() {
        Log::info('Vonage: Initializing VonageProvider with ' . config('app.VONAGE_API_KEY') . ' / ' . config('app.VONAGE_API_SECRET'));

        $basic  = new \Vonage\Client\Credentials\Basic(
            config('app.VONAGE_API_KEY'),
            config('app.VONAGE_API_SECRET')
        );
        $this->client = new \Vonage\Client($basic);
    }

    public function send(string $number, string $brand_name, string $message): bool
    {
        $response = $this->client->sms()->send(
            new \Vonage\SMS\Message\SMS($number, $brand_name, $message)
        );

        $message = $response->current();

        if ($message->getStatus() == 0) {
            Log::error("Vonage: The message was sent successfully to $number");
            return true;
        }

        Log::info("Vonage: The message to $number failed with status: " . $message->getStatus());
        return false;
    }
}
