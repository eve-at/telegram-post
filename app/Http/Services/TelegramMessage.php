<?php
namespace App\Http\Services;

use Telegram\Bot\Objects\Message;
use App\Http\Contracts\TelegramPublishable;
use App\Models\Post;
use Exception;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramMessage implements TelegramPublishable
{
    protected static $publishable;
    protected $chat_id;
    protected $reuse_file = false;

    protected function __construct(protected Post $post, $concept = false) 
    {
        if ($concept && ! config('app.TELEGRAM_CONCEPT_CHANNEL_ID')) {
            throw new Exception('Concept Channel ID is missing or empty. Fill out TELEGRAM_CONCEPT_CHANNEL_ID env variable');
        }

        $this->chat_id = $concept 
            ? config('app.TELEGRAM_CONCEPT_CHANNEL_ID') 
            : $post->channel->chat_id;
    }

    public static function make(Post $post, bool $concept = false)
    {
        return (new self($post, $concept));
    }

    public function publish(): array
    {
        return [$this->send()->message_id];
    }

    protected function send(): Message
    {
        //'protect_content' => true,
        //'disable_notification' => true,
        return Telegram::sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $this->text(),
            'parse_mode' => 'HTML',
        ]);
    }

    public function text(): String
    {
        $text = '';
        
        if ($this->post->show_title) {
            $text .= "<b>{$this->post->title}</b>" . PHP_EOL . PHP_EOL;
        }
        
        $text .= $this->post->body;

        if ($this->post->source) {
            $text .= PHP_EOL . PHP_EOL . "<i>{$this->post->source}</i>";
        }
        
        if ($this->post->show_signature) {
            $text .= (strlen($text) > 0 ? PHP_EOL . PHP_EOL : '')
                     . $this->post->channel->signature;
        }

        return $text;
    } 
}