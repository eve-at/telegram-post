<?php
namespace App\Http\Services;

use Telegram\Bot\Objects\Message;
use App\Http\Contracts\TelegramPublishable;
use App\Models\Poll;
use Exception;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramPoll implements TelegramPublishable
{
    protected static $publishable;
    protected $chat_id;
    protected $reuse_file = false;
    protected $message = [];

    protected function __construct(protected Poll $poll, $concept = false) 
    {
        if ($concept && ! config('app.TELEGRAM_CONCEPT_CHAT_ID')) {
            throw new Exception('Concept Channel ID is missing or empty. Fill out TELEGRAM_CONCEPT_CHAT_ID env variable');
        }

        $this->chat_id = $concept 
            ? config('app.TELEGRAM_CONCEPT_CHAT_ID') 
            : $poll->channel->chat_id;

        $this->prepare();
    }

    public static function make(Poll $poll, bool $concept = false)
    {
        return (new self($poll, $concept));
    }

    protected function prepare(): void
    {
        $this->message = [
            'chat_id' => $this->chat_id,
            'type' => $this->poll->type,
            'question' => $this->poll->title,
            'options' => $this->poll->options,
            'explanation' => $this->poll->explanation,
            'correct_option_id' => $this->poll->correct_option_id,
            'is_anonymous' => $this->poll->is_anonymous,
        ];
    }

    public function publish(): Message
    {
        return $this->send();
    }
    
    protected function send(): Message
    {
        return Telegram::sendPoll($this->message);
    }

    // response for Poll Telegram\Bot\Objects\Message
    // {
    //     "message_id":143,
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
    //     "date":1705497922,
    //     "poll":{
    //         "id":"4990306953440788562",
    //         "question":"The poll question is... 3",
    //         "options":[
    //             {"text":"First!","voter_count":0},
    //             {"text":"and one more!","voter_count":0},
    //             {"text":"The right one","voter_count":0},
    //             {"text":"forth option","voter_count":0}
    //         ],
    //         "total_voter_count":0,
    //         "is_closed":false,
    //         "is_anonymous":true,
    //         "type":"quiz",
    //         "allows_multiple_answers":false,
    //         "correct_option_id":2,
    //         "explanation":"Add some options 444",
    //         "explanation_entities":[]
    //     }
    // }
}