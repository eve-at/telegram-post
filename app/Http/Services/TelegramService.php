<?php

namespace App\Http\Services;

use App\Models\Channel;
use App\Models\Post;
use Exception;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService 
{
    protected $channelId;

    function __construct($concept = false) 
    {
        $this->channelId = $concept ? env('TELEGRAM_CONCEPT_CHANNEL_ID') : env('TELEGRAM_CHANNEL_ID');
    }

    public function sendMessage(Post $post) 
    {
        $channel = Channel::first();
        if (! $channel) {
            throw new Exception('No channel found');
        }
        $message = '';
        
        if ($post->show_title) {
            $message .= "<b>$post->title</b>
            
";
        }
        
        $message .= $post->body;

        if ($post->source) {
        $message .= "
        
<i>" . $post->source . "</i>";
        }

        $message .= "
        
" . $channel->signature;
        
        return Telegram::sendMessage([
            'chat_id' => $this->channelId, //env('TELEGRAM_CHANNEL_ID'),
            //'protect_content' => true,
            //'disable_notification' => true,
            'parse_mode' => 'HTML',
            'text' => $message,
        ]);
    }

    public static function publish(Post $postable, $concept = false)
    {
        //TODO: $postable must have an interface and have a common method to retrieve the $message body
        $message = $postable;
        
        return (new self($concept))->sendMessage($message);
    }
    
}