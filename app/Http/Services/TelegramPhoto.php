<?php
namespace App\Http\Services;

use Telegram\Bot\Objects\Message as TelegramMessage;
use App\Http\Contracts\TelegramPublishable;
use App\Models\MediaGroup;
use App\Models\MediaGroupFile;
use Exception;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramPhoto implements TelegramPublishable
{
    protected static $publishable;
    protected $chat_id;

    protected function __construct(protected MediaGroup $group, $concept = false) 
    {
        if ($concept && ! config('app.TELEGRAM_CONCEPT_CHANNEL_ID')) {
            throw new Exception('Concept Channel ID is missing or empty. Fill out TELEGRAM_CONCEPT_CHANNEL_ID env variable');
        }

        $this->chat_id = $concept 
            ? config('app.TELEGRAM_CONCEPT_CHANNEL_ID') 
            : $group->channel->chat_id;
    }

    public static function make(MediaGroup $group, bool $concept = false)
    {
        return (new self($group, $concept));
    }

    public function publish(): array
    {
        $response = $this->send();
        
        $this->updateFiles($response);

        return [$response->message_id];
    }

    protected function send(): TelegramMessage
    {
        return Telegram::sendPhoto([
            'chat_id' => $this->chat_id,
            'caption' => $this->caption(),
            'parse_mode' => 'HTML',
            'photo' => $this->media($this->group->filenames[0]),
        ]);
    }

    public function caption(): String
    {
        $caption = '';
        
        if ($this->group->show_title) {
            $caption .= "<b>{$this->group->title}</b>" . PHP_EOL . PHP_EOL;
        }
        
        $caption .= $this->group->body;

        if ($this->group->source) {
            $caption .= PHP_EOL . PHP_EOL . "<i>{$this->group->source}</i>";
        }

        if ($this->group->show_signature) {
            $caption .= (strlen($caption) > 0 ? PHP_EOL . PHP_EOL : '')
                     . $this->group->channel->signature;
        }

        return $caption;
    } 

    protected function media(MediaGroupFile $media): mixed
    {
        return $media->file_id 
            ?? InputFile::create(
                storage_path('app\\public\\media\\' . session('channel.id') . '\\' . $media->filename), 
                $media->filename
            );
    }

    // response for Photo Telegram\Bot\Objects\Message
    // {
    //     "message_id":51,
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
    //     "date":1705396565,
    //     "photo":[
    //         {"file_id":"AgACAgEAAx0EfPL4aAADM2WmSVWCnW0w970o4Q3Q-xKYWsCWAAJ8rDEbXckwRdlPs4mRgJdIAQADAgADcwADNAQ","file_unique_id":"AQADfKwxG13JMEV4","file_size":1113,"width":90,"height":60},
    //         {"file_id":"AgACAgEAAx0EfPL4aAADM2WmSVWCnW0w970o4Q3Q-xKYWsCWAAJ8rDEbXckwRdlPs4mRgJdIAQADAgADbQADNAQ","file_unique_id":"AQADfKwxG13JMEVy","file_size":14603,"width":320,"height":213},
    //         {"file_id":"AgACAgEAAx0EfPL4aAADM2WmSVWCnW0w970o4Q3Q-xKYWsCWAAJ8rDEbXckwRdlPs4mRgJdIAQADAgADeAADNAQ","file_unique_id":"AQADfKwxG13JMEV9","file_size":53227,"width":696,"height":464}
    //     ],
    //     "caption":"Photo post: Nemo aut sunt cupiditate1\n\n😍 Queen, who was peeping anxiously into its nest. Alice crouched down among the trees under which she concluded that it made Alice quite jumped; but.3\n\nMarcelo_McDermott",
    //     "caption_entities":[
    //         {"offset":0,"length":37,"type":"bold"},{"offset":192,"length":17,"type":"italic"}
    //     ]
    // }

    protected function updateFiles(TelegramMessage $message)
    {
        $this->updateFile($message, $this->group->filenames[0]);
    }

    protected function updateFile(mixed $message, MediaGroupFile $media)
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