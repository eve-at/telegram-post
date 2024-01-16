<?php
namespace App\Http\Services;

use Telegram\Bot\Objects\Message as TelegramMessage;
use App\Http\Contracts\TelegramPublishable;
use App\Models\Video;
use Exception;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramVideo implements TelegramPublishable
{
    protected static $publishable;
    protected $chat_id;
    protected $reuse_file = false;

    protected function __construct(protected Video $video, $concept = false) 
    {
        if ($concept && ! env('TELEGRAM_CONCEPT_CHANNEL_ID')) {
            throw new Exception('Concept Channel ID is missing or empty. Fill out TELEGRAM_CONCEPT_CHANNEL_ID env variable');
        }

        $this->chat_id = $concept 
            ? env('TELEGRAM_CONCEPT_CHANNEL_ID') 
            : $video->channel->chat_id;
    }

    public static function make(Video $video, bool $concept = false)
    {
        return (new self($video, $concept));
    }

    public function publish(): array
    {
        $response = $this->send();
        
        $this->updateFile($response);

        return [$response->message_id];
    }

    protected function send(): TelegramMessage
    {
        return Telegram::sendVideo([
            'chat_id' => $this->chat_id,
            'caption' => $this->caption(),
            'parse_mode' => 'HTML',
            'video' => $this->video(),
            'supports_streaming' => true, //autoplay
        ]);
    }

    public function caption(): String
    {
        $caption = '';
        
        if ($this->video->show_title) {
            $caption .= "<b>{$this->video->title}</b>" . PHP_EOL . PHP_EOL;
        }
        
        $caption .= $this->video->body;

        if ($this->video->source) {
            $caption .= PHP_EOL . PHP_EOL . "<i>{$this->video->source}</i>";
        }

        if ($this->video->show_signature) {
            $caption .= (strlen($caption) > 0 ? PHP_EOL . PHP_EOL : '')
                     . $this->video->channel->signature;
        }

        return $caption;
    } 

    protected function video() 
    {
        if ($this->video->file_id) {
            $this->reuse_file = true;
            return $this->video->file_id;
        }

        return InputFile::create(
            storage_path('app\\public\\medias\\' . $this->video->filename), 
            $this->video->filename
        );
    }

    // response for Video Telegram\Bot\Objects\Message
    // {
    //     "message_id":67,
    //     "sender_chat":{
    //         "id":-100XXX,
    //         "title":"Concept testing",
    //         "type":"channel"
    //     },
    //     "chat":{
    //         "id":-100XXX,
    //         "title":"Concept testing",
    //         "type":"channel"
    //     },
    //     "date":1705412011,
    //     "video":{
    //         "duration":52,
    //         "width":1920,
    //         "height":1080,
    //         "file_name":"ZZO2oHuIZAaNls58rk6T3qYKgSMEm7XiCkJUILbV.mp4",
    //         "mime_type":"video/mp4",
    //         "thumbnail":{
    //             "file_id":"AAMCAQADHQR88vhoAANDZaaFqw99_oGRhCUG5IKJ92U5KucAAlEDAAJdyTBFyc5HFevEJxQBAAdtAAM0BA",
    //             "file_unique_id":"AQADUQMAAl3JMEVy",
    //             "file_size":11489,
    //             "width":320,
    //             "height":180
    //         },
    //         "thumb":{
    //             "file_id":"AAMCAQADHQR88vhoAANDZaaFqw99_oGRhCUG5IKJ92U5KucAAlEDAAJdyTBFyc5HFevEJxQBAAdtAAM0BA",
    //             "file_unique_id":"AQADUQMAAl3JMEVy",
    //             "file_size":11489,
    //             "width":320,
    //             "height":180
    //         },
    //         "file_id":"BAACAgEAAx0EfPL4aAADQ2WmhasPff6BkYQlBuSCifdlOSrnAAJRAwACXckwRcnORxXrxCcUNAQ",
    //         "file_unique_id":"AgADUQMAAl3JMEU",
    //         "file_size":2742445
    //     },
    //     "caption":"A video title 51\n\nBody goes here\n\nThe source 51\n\nSubscribe",
    //     "caption_entities":[
    //         {"offset":0,"length":17,"type":"bold"},
    //         {"offset":35,"length":13,"type":"italic"}
    //     ]
    // }

    protected function updateFile(TelegramMessage $message)
    {
        if ($this->reuse_file) {
            return;
        }

        $this->video->file_id = $message->video->file_id;
        $this->video->file_unique_id = $message->video->file_unique_id;
        return $this->video->save();
    }
}