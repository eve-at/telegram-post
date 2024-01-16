<?php
namespace App\Http\Services;

use App\Http\Contracts\TelegramMessagable;
use App\Models\Photo;
use Telegram\Bot\FileUpload\InputFile;

class MessagablePhoto // implements TelegramMessagable
{
    function __construct(protected Photo $photo) {
        //
    }

    protected function publish()
    {
        $body = [
            'photo' => '',
            'caption' => '',
            'parse_mode' => 'HTML',
            ...$this->body(),
            'chat_id' => $this->channelId,
        ];
        
        $response = Telegram::sendPhoto($body);

        $this->updateFileIdPhoto($response);
    }

    protected static function make($object)//: TelegramMessagable
    {
        return new self($object);
    }

    protected function type(): String
    {
        return class_basename($this->photo);
    }

    protected function showSignature(): Bool
    {
        return $this->photo->show_signature;
    }

    protected function body(): Array
    {
        $caption = '';
        
        if ($this->photo->show_title) {
            $caption .= "<b>{$this->photo->title}</b>" . PHP_EOL . PHP_EOL;
        }
        
        $caption .= $this->photo->body;

        if ($this->photo->source) {
            $caption .= PHP_EOL . PHP_EOL . "<i>{$this->photo->source}</i>";
        }

        return [
            'caption' => $caption,
            'parse_mode' => 'HTML',
            'photo' => $this->photo->file_id 
                ?? InputFile::create(storage_path('app\\public\\medias\\' . $this->photo->filename), $this->photo->filename),
        ];
    }    

    
}