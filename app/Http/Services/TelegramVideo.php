<?php
namespace App\Http\Services;

use Telegram\Bot\Objects\Message as TelegramMessage;
use App\Http\Contracts\TelegramPublishable;
use App\Models\Post;
use App\Models\PostFile;
use Exception;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramVideo implements TelegramPublishable
{
    protected static $publishable;
    protected $chat_id;
    protected $message = [];

    protected function __construct(protected Post $post, $concept = false) 
    {
        if ($concept && ! config('app.TELEGRAM_CONCEPT_CHANNEL_ID')) {
            throw new Exception('Concept Channel ID is missing or empty. Fill out TELEGRAM_CONCEPT_CHANNEL_ID env variable');
        }

        $this->chat_id = $concept 
            ? config('app.TELEGRAM_CONCEPT_CHANNEL_ID') 
            : $post->channel->chat_id;

        $this->prepare();
    }

    public static function make(Post $post, bool $concept = false)
    {
        return (new self($post, $concept));
    }

    protected function prepare(): void
    {
        $this->message = [
            'chat_id' => $this->chat_id,
            'caption' => $this->caption(),
            'parse_mode' => 'HTML',
            'video' => $this->media($this->post->filenames[0]),
            'supports_streaming' => true, //autoplay
        ];
    }

    public function publish(): array
    {
        $response = $this->send();
        
        $this->updateFiles($response);

        return [$response->message_id];
    }
    
    protected function send(): TelegramMessage
    {
        return Telegram::sendVideo($this->message);
    }

    public function caption(): String
    {
        $caption = '';
        
        if ($this->post->show_title) {
            $caption .= "<b>{$this->post->title}</b>" . PHP_EOL . PHP_EOL;
        }
        
        $caption .= $this->post->body;

        if ($this->post->source) {
            $caption .= PHP_EOL . PHP_EOL . "<i>{$this->post->source}</i>";
        }

        if ($this->post->show_signature) {
            $caption .= (strlen($caption) > 0 ? PHP_EOL . PHP_EOL : '')
                     . $this->post->channel->signature;
        }

        return $caption;
    } 

    protected function media(PostFile $media): mixed
    {
        return $media->file_id 
            ?? InputFile::create(
                storage_path('app\\public\\media\\' . session('channel.id') . '\\' . $media->filename), 
                $media->filename
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

    protected function updateFiles(TelegramMessage $message)
    {
        $this->updateFile($message, $this->post->filenames[0]);
    }

    protected function updateFile(mixed $message, PostFile $media)
    {
        if ($media->file_id) {
            return $media;
        }

        if ($media->type === 'video') {
            $media->file_id = $message['video']['file_id'];
            $media->file_unique_id = $message['video']['file_unique_id'];
        } else if ($media->type === 'photo') {
            $telegramPhoto = collect($message['photo'])->pop();
            $media->file_id = $telegramPhoto['file_id'];
            $media->file_unique_id = $telegramPhoto['file_unique_id'];
        } else {
            throw new Exception('Invalid type of Telegram Media');
        }
        
        return $media->save();
    }
}