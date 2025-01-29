<?php

namespace App\Http\Contracts;

interface SMS
{
    public function send(string $number, string $brand_name, string $message): bool;
}
