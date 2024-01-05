<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\InputMedia\InputMediaPhoto;
use Telegram\Bot\Objects\InputMedia\InputMediaVideo;
use Telegram\Bot\Objects\Video;

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
    return 'Hello there';
});

Route::get('/post', function () {
    
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
    ]);

    dd($response);
    return view('welcome');
});

Route::get('/video', function () {
    
    //$response = Telegram::bot('mybot')->getMe();

$message = "<b>Разворот «Рускеальского Экспресса 3»</b>

В конце 2020 года в Горном парке «Рускеала» был построен первый в современной России поворотный круг, который позволяет развернуть паровоз на 180 градусов. Раньше для смены направления движения поезда нужны были два паровоза, которые следовали в начале и в конце состава. 

Поворотный круг значительно упрощает не только смену направления движения, но и заправку паровоза водой и другое обслуживание.

<i>Видео: andrei_mikhailov</i>

📍Координаты: <a href='https://yandex.ru/maps/-/CCUvMGDoTB'>Яндекс.Карты</a>
<a href='tg://search_hashtag?hashtag=Карелия'>#Карелия</a>

<a href='https://t.me/+vSC9Gp9GWeY0Yjk6'>Моя страна - Россия! 🇷🇺</a>
";

    $response = Telegram::sendMessage([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        //'protect_content' => true,
        //'disable_notification' => true,
        'parse_mode' => 'HTML',
        'text' => $message,
        'video' => [
            'file_id' => "BAACAgEAAx0EfPL4aAADDGWW8V1357bWbErr3xCn0hE1fqLoAAJQBAAC2OW5RAvxQDWMRmvYNAQ",
            'file_unique_id' => "AgADUAQAAtjluUQ",
            'width' => 1080,
            'height' => 1920,
            'duration' => 71,
            'file_name' => 'Разворот_«Рускеальского_Экспресса_@ruskeal_expressВ_конце_2020_года',
            'mime_type' => 'medias/mp4',
            'file_size' => 31632231,
        ]
    ]);

    dd($response);
    return view('welcome');
});

Route::get('/sendvideo', function () {
    $message = "<b>Разворот «Рускеальского Экспресса 5»</b>

В конце 2020 года в Горном парке «Рускеала» был построен первый в современной России поворотный круг, который позволяет развернуть паровоз на 180 градусов. Раньше для смены направления движения поезда нужны были два паровоза, которые следовали в начале и в конце состава. 
    
Поворотный круг значительно упрощает не только смену направления движения, но и заправку паровоза водой и другое обслуживание.
    
<i>Видео: andrei_mikhailov</i>
    
📍Координаты: <a href='https://yandex.ru/maps/-/CCUvMGDoTB'>Яндекс.Карты</a>
<a href='tg://search_hashtag?hashtag=Карелия'>#Карелия</a>
    
<a href='https://t.me/+vSC9Gp9GWeY0Yjk6'>Моя страна - Россия! 🇷🇺</a>
";

    $response = Telegram::sendVideo([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        'video' => InputFile::create(storage_path('medias\\express.mp4'), 'express2.mp4'),
        'supports_streaming' => true,
        'caption' => $message,
        'parse_mode' => 'HTML',
        'type' => 'video',
        'width' => 1080,
        'height' => 1920,
        'duration' => 71,
    ]);

    dd($response);
});

Route::get('/resendvideo', function () {
    $message = "<b>Разворот «Рускеальского Экспресса 9»</b>

В конце 2020 года в Горном парке «Рускеала» был построен первый в современной России поворотный круг, который позволяет развернуть паровоз на 180 градусов. Раньше для смены направления движения поезда нужны были два паровоза, которые следовали в начале и в конце состава. 
    
Поворотный круг значительно упрощает не только смену направления движения, но и заправку паровоза водой и другое обслуживание.
    
<i>Видео: andrei_mikhailov</i>
    
📍Координаты: <a href='https://yandex.ru/maps/-/CCUvMGDoTB'>Яндекс.Карты</a>
<a href='tg://search_hashtag?hashtag=Карелия'>#Карелия</a>
    
<a href='https://t.me/+vSC9Gp9GWeY0Yjk6'>Моя страна - Россия! 🇷🇺</a>
";

    $video = new Video([
        'file_id' => 'BAACAgEAAx0EfPL4aAADEWWXgJ4OKxGD0s_90eXm39-UPvlIAAJzBAAC2OW5REjHk_9f1v-zNAQ',
        'file_unique_id' => 'AgADcwQAAtjluUQ',
        'width' => 1080,
        'height' => 1920,
        'duration' => 71,
        'file_name' => 'Разворот_«Рускеальского_Экспресса_@ruskeal_expressВ_конце_2020_года',
        'mime_type' => 'medias/mp4',
        'file_size' => 31632231
    ]);

    $response = Telegram::sendVideo([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        'video' => $video->fileId,
        'duration' => 71,
        'width' => 1080,
        'height' => 1920,
        'caption' => $message,
        'parse_mode' => 'HTML',
        'supports_streaming' => true,
    ]);

    dd($response);
});

Route::get('/photo', function () {
    $message = "<b>Восхождение на Шалбуздаг, Республика Дагестан</b>

Священная гора Шалбуздаг (Шалбуз-Даг) находится на самом юге России в Дагестане.

<i>Фото: photo_surkhaev</i>
    
📍Координаты: <a href='https://yandex.ru/maps/-/CCUvMGDoTB'>Яндекс.Карты</a>
<a href='tg://search_hashtag?hashtag=Карелия'>#Карелия</a>
    
<a href='https://t.me/+vSC9Gp9GWeY0Yjk6'>Моя страна - Россия! 🇷🇺</a>
";

    $response = Telegram::sendPhoto([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        'photo' => InputFile::create(storage_path('medias\\2.jpeg'), '2.jpeg'),
        'caption' => $message,
        'parse_mode' => 'HTML',
    ]);

    dd($response);
});

Route::get('/resendPhoto', function () {
    $message = "<b>Восхождение на Шалбуздаг, Республика Дагестан 2</b>

Священная гора Шалбуздаг (Шалбуз-Даг) находится на самом юге России в Дагестане.

<i>Фото: photo_surkhaev</i>
    
📍Координаты: <a href='https://yandex.ru/maps/-/CCUvMGDoTB'>Яндекс.Карты</a>
<a href='tg://search_hashtag?hashtag=Карелия'>#Карелия</a>
    
<a href='https://t.me/+vSC9Gp9GWeY0Yjk6'>Моя страна - Россия! 🇷🇺</a>
";

    $response = Telegram::sendPhoto([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        'photo' => 'AgACAgEAAx0EfPL4aAADFmWXkWlveYKxCFB_SJzEOJz3PaLsAAL7rDEb2OW5RBep4ueOr0MeAQADAgADcwADNAQ',
        'caption' => $message,
        'parse_mode' => 'HTML',
    ]);

    dd($response);
});

Route::get('/media-group', function () {
    $message = "<b>Восхождение на Шалбуздаг, Республика Дагестан 6</b>

Священная гора Шалбуздаг (Шалбуз-Даг) находится на самом юге России в Дагестане.

<i>Фото: photo_surkhaev</i>
    
📍Координаты: <a href='https://yandex.ru/maps/-/CCUvMGDoTB'>Яндекс.Карты</a>
<a href='tg://search_hashtag?hashtag=Карелия'>#Карелия</a>
    
<a href='https://t.me/+vSC9Gp9GWeY0Yjk6'>Моя страна - Россия! 🇷🇺</a>
";

    // existing photo
    $file1 = new InputMediaPhoto([
        'type' => 'photo',
        'media' => 'AgACAgEAAx0EfPL4aAADFmWXkWlveYKxCFB_SJzEOJz3PaLsAAL7rDEb2OW5RBep4ueOr0MeAQADAgADcwADNAQ',
        'caption' => $message,
        'parse_mode' => 'HTML',
    ]);

    // existing photo
    $file2 = new InputMediaPhoto([
        'type' => 'photo',
        'media' => 'AgACAgEAAx0EfPL4aAADGGWXluQx0EULUakTp8pZt_rRGQwfAAL9rDEb2OW5RJO1DtuVRAtyAQADAgADcwADNAQ',
    ]);

    // existing video
    $video = new InputMediaVideo([
        'type' => 'video',
        'media' => 'BAACAgEAAx0EfPL4aAADEWWXgJ4OKxGD0s_90eXm39-UPvlIAAJzBAAC2OW5REjHk_9f1v-zNAQ',
        'supports_streaming' => false
    ]);

    // new photo
    $file3 = [
        'type' => 'photo',
        'media' => 'https://habrastorage.org/r/w1560/getpro/habr/upload_files/f05/2e6/00c/f052e600ccc8cba2ccc9163fd34a206b.jpeg'
    ];

    // new photo
    $file4 = [
        'type' => 'photo',
        'media' => 'https://habrastorage.org/r/w1560/getpro/habr/upload_files/5fc/a4a/5ca/5fca4a5caa6030b27300d479b25bc002.jpeg',
    ];

    $response = Telegram::sendMediaGroup([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        'media' => json_encode([$file1, $file2, $video, $file3, $file4]), // 2-10 elements
    ]);

    dd($response);
});

Route::get('/poll', function () {
    
    $response = Telegram::sendPoll([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        'question' => "(Quiz) Sure 3 [ʃʊə]",
        'options'=> json_encode([
            'Редко',
            'Вскоре',
            'Конечно',
            'Не знаю',
        ]),
        'explanation' => 'Правильный ответ: "Конечно"',
        'correct_option_id' => 2, // 0-based
        'is_anonymous' => true,
        'type' => 'quiz'
    ]);

    dd($response);
});