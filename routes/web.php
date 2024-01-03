<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    
    //$response = Telegram::bot('mybot')->getMe();

$message = "<b>📣 Laravel Pennant 6: Powerful Feature Flag Management for Laravel Applications</b>

Experience the ease of feature flag management with Laravel Pennant.

Feature flags are essential for controlling functions within your application. And now, introducing Laravel Pennant - a lightweight library built by Tim MacDonald, a member of the Laravel Core Team.

🚀 Key Features:

<tg-spoiler>
- Simple and intuitive interface for managing feature flags
- Enable or disable specific functionalities based on conditions
- Streamline feature rollout and A/B testing
- Flexibility to activate or deactivate flags for specific users or user groups
</tg-spoiler>

📝 Laravel Pennant allows developers to seamlessly control and toggle various features or code fragments within their Laravel applications. With its straightforward design, incorporating feature flags has never been easier.

🌐 Learn more about Laravel Pennant and its usage on our website: <a href='https://laravel.com'>laravel.com</a>

📢 Don't miss out on any updates and Laravel insights! Join our Telegram channel today: <a href='https://t.me/your_telegram_channel'>Join Channel</a>

Stay ahead with Laravel Pennant and unlock the power of feature flagging in your applications! 💪

<i>This post is not sponsored or endorsed by Laravel or its affiliates.</i>

<a href='https://t.me/your_telegram_channel'>Join Channel</a>
";

    $response = Telegram::sendMessage([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        //'protect_content' => true,
        //'disable_notification' => true,
        'parse_mode' => 'HTML',
        'text' => $message,
        // 'entities' => [
        //     $message
        // ]
    ]);

    dd($response);
    return view('welcome');
});
