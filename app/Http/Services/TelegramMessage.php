<?php
namespace App\Http\Services;

use Telegram\Bot\Objects\Message;
use App\Http\Contracts\TelegramPublishable;
use App\Models\Post;
use Carbon\Carbon;
use Clockwork\Request\Log;
use Exception;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramMessage implements TelegramPublishable
{
    protected static $publishable;
    protected $chat_id;
    protected $reuse_file = false;
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
            'text' => $this->text(),
            'parse_mode' => 'HTML',
        ];
    }

    public function publish(): array
    {
        return [$this->send()->message_id];
    }

    public function schedule(\DateTime $datetime): array
    {
        $this->message['schedule_date'] = Carbon::parse($datetime)->timestamp;
        
        //Log::debug("Message scheduled for $datetime [$this->message['schedule_date']]");

        return $this->publish();
    }

    protected function send(): Message
    {
        //'protect_content' => true,
        //'disable_notification' => true,
        return Telegram::sendMessage($this->message);
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