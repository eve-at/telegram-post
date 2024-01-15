<?php
namespace App\Http\Services;

use App\Http\Contracts\TelegramMessagable;
use App\Models\Post;

class MessagablePost implements TelegramMessagable
{
    function __construct(protected Post $post) {
        //
    }

    public static function make($object): TelegramMessagable
    {
        return new self($object);
    }

    public function type(): String
    {
        return class_basename($this->post);
    }

    public function showSignature(): Bool
    {
        return $this->post->show_signature;
    }

    public function message(): Array
    {

        $text = '';
        
        if ($this->post->show_title) {
            $text .= "<b>{$this->post->title}</b>" . PHP_EOL . PHP_EOL;
        }
        
        $text .= $this->post->body;

        if ($this->post->source) {
            $text .= PHP_EOL . PHP_EOL . "<i>{$this->post->source}</i>";
        }
        
        return [
            'text' => $text,
            'parse_mode' => 'HTML',
        ];
    }    
}