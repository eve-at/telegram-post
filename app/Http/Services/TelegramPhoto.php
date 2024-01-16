<?php
namespace App\Http\Services;

use Telegram\Bot\Objects\Message as TelegramMessage;
use App\Http\Contracts\TelegramPublishable;
use App\Models\Photo;
use Exception;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramPhoto implements TelegramPublishable
{
    protected static $publishable;
    protected $chat_id;
    protected $reuse_file = false;

    protected function __construct(protected Photo $photo, $concept = false) 
    {
        if ($concept && ! env('TELEGRAM_CONCEPT_CHANNEL_ID')) {
            throw new Exception('Concept Channel ID is missing or empty. Fill out TELEGRAM_CONCEPT_CHANNEL_ID env variable');
        }

        $this->chat_id = $concept 
            ? env('TELEGRAM_CONCEPT_CHANNEL_ID') 
            : $photo->channel->chat_id;
    }

    public static function make(Photo $photo, bool $concept = false)
    {
        return (new self($photo, $concept));
    }

    public function publish(): int
    {
        $response = $this->send();
        
        $this->updateFile($response);

        return $response->message_id;
    }

    protected function send()
    {
        return Telegram::sendPhoto([
            'chat_id' => $this->chat_id,
            'caption' => $this->caption(),
            'parse_mode' => 'HTML',
            'photo' => $this->photo(),
        ]);
    }

    public function caption(): String
    {
        $caption = '';
        
        if ($this->photo->show_title) {
            $caption .= "<b>{$this->photo->title}</b>" . PHP_EOL . PHP_EOL;
        }
        
        $caption .= $this->photo->body;

        if ($this->photo->source) {
            $caption .= PHP_EOL . PHP_EOL . "<i>{$this->photo->source}</i>";
        }

        if ($this->photo->show_signature) {
            $caption .= (strlen($caption) > 0 ? PHP_EOL . PHP_EOL : '')
                     . $this->photo->channel->signature;
        }

        return $caption;
    } 

    protected function photo() 
    {
        if ($this->photo->file_id) {
            $this->reuse_file = true;
            return $this->photo->file_id;
        }

        return InputFile::create(storage_path('app\\public\\medias\\' . $this->photo->filename), $this->photo->filename);
    }
    // response for Photo Telegram\Bot\Objects\Message
    // {
    //     "message_id":51,
    //     "sender_chat":{
    //         "id":-1002096298088,
    //         "title":"Concept testing",
    //         "type":"channel"
    //     },
    //     "chat":{
    //         "id":-1002096298088,
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

    protected function updateFile(TelegramMessage $message)
    {
        if ($this->reuse_file) {
            return;
        }

        $telegramPhoto = collect($message->photo)->pop();
        
        $this->photo->file_id = $telegramPhoto->file_id;
        $this->photo->file_unique_id = $telegramPhoto->file_unique_id;
        return $this->photo->save();
    }
}