<?php

namespace App\Http\Services;

use App\Helpers\Helper;
use Telegram\Bot\Objects\Message;
use App\Http\Contracts\TelegramPublishable;
use App\Models\Post;
use App\Models\PostFile;
use Exception;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\InputMedia\InputMediaPhoto;
use Telegram\Bot\Objects\InputMedia\InputMediaVideo;

class TelegramMediaGroup implements TelegramPublishable
{
    protected static $publishable;
    protected $channel_id;
    protected $chat_id;
    protected $reuse_file = false;
    protected $filesToUpload = [];
    protected $message = [];

    protected function __construct(protected Post $post, $concept = false)
    {
        if ($concept && !config('app.TELEGRAM_CONCEPT_CHAT_ID')) {
            throw new Exception('Concept Channel ID is missing or empty. Fill out TELEGRAM_CONCEPT_CHAT_ID env variable');
        }

        $this->chat_id = $concept
            ? config('app.TELEGRAM_CONCEPT_CHAT_ID')
            : $post->channel->chat_id;

        $this->channel_id = $concept
            ? config('app.TELEGRAM_CONCEPT_CHANNEL_ID')
            : $post->channel_id;

        $this->prepare();
    }

    public static function make(Post $post, bool $concept = false)
    {
        return (new self($post, $concept));
    }

    protected function prepare(): void
    {
        $this->filesToUpload = [];

        $medias = $this->medias();

        if ($caption = $this->caption()) {
            $medias[0]['caption'] = $caption;
            $medias[0]['parse_mode'] = 'HTML';
        }

        $this->message = [
            'chat_id' => $this->chat_id,
            'media' => json_encode($medias),
            ...$this->filesToUpload
        ];
    }

    public function publish(): Message
    {
        $response = Telegram::sendMediaGroup($this->message);

        Log::info($response);

        //TODO: move outside
        $this->updateFiles($response);

        //return $response->pluck('message_id')->toArray();
        return $response;
    }

    public function caption(): string
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

    protected function medias(): array
    {
        return $this->post->filenames->map(function (PostFile $media) {
            return match ($media->type) {
                'photo' => $this->inputMediaPhoto($media),
                'video' => $this->inputMediaVideo($media),
                //'document' => $this->inputMediaDocument($media),
                default => new Exception('Invalid Media Group file type: ' . $media->type),
            };
        })->toArray();
    }

    protected function inputMediaPhoto(PostFile $media): InputMediaPhoto
    {
        return new InputMediaPhoto([
            'type' => 'photo',
            'media' => $this->media($media),
        ]);
    }

    protected function inputMediaVideo(PostFile $media): InputMediaVideo
    {
        return new InputMediaVideo([
            'type' => 'video',
            'media' => $this->media($media),
        ]);
    }

    protected function media($media): string
    {
        if ($media->file_id) {
            return $media->file_id;
        }

        $this->filesToUpload[$media->filename] = InputFile::create(
            Helper::filepath($this->channel_id, $media->filename),
            $media->filename
        );

        return "attach://$media->filename";
    }

    // response for MediaGroup [Telegram\Bot\Objects\Message]
    // [
    //     {
    //         "message_id":75,
    //         "sender_chat":{
    //             "id":-100XXX,
    //             "title":"Concept testing",
    //             "type":"channel"
    //         },
    //         "chat":{
    //             "id":-100XXX,
    //             "title":"Concept testing",
    //             "type":"channel"
    //         },
    //         "date":1705420415,
    //         "media_group_id":"13643363322762233",
    //         "photo":[
    //             {
    //                 "file_id":"AgACAgEAAx0EfPL4aAADS2Wmpn_A1ueZdrbo8-OQpLafeoQpAAJ8rDEbXckwRdlPs4mRgJdIAQADAgADcwADNAQ",
    //                 "file_unique_id":"AQADfKwxG13JMEV4",
    //                 "file_size":1113,
    //                 "width":90,
    //                 "height":60
    //             },{
    //                 "file_id":"AgACAgEAAx0EfPL4aAADS2Wmpn_A1ueZdrbo8-OQpLafeoQpAAJ8rDEbXckwRdlPs4mRgJdIAQADAgADbQADNAQ","file_unique_id":"AQADfKwxG13JMEVy",
    //                 "file_size":14603,
    //                 "width":320,
    //                 "height":213
    //             },{
    //                 "file_id":"AgACAgEAAx0EfPL4aAADS2Wmpn_A1ueZdrbo8-OQpLafeoQpAAJ8rDEbXckwRdlPs4mRgJdIAQADAgADeAADNAQ",
    //                 "file_unique_id":"AQADfKwxG13JMEV9",
    //                 "file_size":53227,
    //                 "width":696,
    //                 "height":464
    //             }
    //         ],
    //         "caption":"The title\n\nBody is here\n\nThe source\n\nSubscribe",
    //         "caption_entities":[
    //             {"offset":0,"length":9,"type":"bold"},
    //             {"offset":25,"length":10,"type":"italic"}
    //         ]
    //     },
    //     {
    //         "message_id":76,
    //         "sender_chat":{
    //             "id":-100XXX,
    //             "title":"Concept testing",
    //             "type":"channel"
    //         },
    //         "chat":{
    //             "id":-100XXX,
    //             "title":"Concept testing",
    //             "type":"channel"
    //         },
    //         "date":1705420415,
    //         "media_group_id":"13643363322762233",
    //         "photo":[
    //             {
    //                 "file_id":"AgACAgEAAx0EfPL4aAADTGWmpn-5EPatHjOrnisXZ4pFf5-xAAKCrDEbXckwRS4xvGOq6E-pAQADAgADcwADNAQ",
    //                 "file_unique_id":"AQADgqwxG13JMEV4",
    //                 "file_size":1384,
    //                 "width":90,
    //                 "height":67
    //             },{
    //                 "file_id":"AgACAgEAAx0EfPL4aAADTGWmpn-5EPatHjOrnisXZ4pFf5-xAAKCrDEbXckwRS4xvGOq6E-pAQADAgADbQADNAQ",
    //                 "file_unique_id":"AQADgqwxG13JMEVy",
    //                 "file_size":18354,
    //                 "width":320,
    //                 "height":240
    //             },{
    //                 "file_id":"AgACAgEAAx0EfPL4aAADTGWmpn-5EPatHjOrnisXZ4pFf5-xAAKCrDEbXckwRS4xvGOq6E-pAQADAgADeAADNAQ",
    //                 "file_unique_id":"AQADgqwxG13JMEV9",
    //                 "file_size":56180,
    //                 "width":696,
    //                 "height":523
    //             }
    //         ]
    //     },{
    //         "message_id":77,
    //         "sender_chat":{
    //             "id":-100XXX,
    //             "title":"Concept testing",
    //             "type":"channel"
    //         },
    //         "chat":{
    //             "id":-100XXX,
    //             "title":"Concept testing",
    //             "type":"channel"
    //         },
    //         "date":1705420415,
    //         "media_group_id":"13643363322762233",
    //         "video":{
    //             "duration":36,
    //             "width":1920,
    //             "height":1080,
    //             "file_name":"hj9IXSZSD8XXgEjknuCs9ujJXDG7O9cw1WwuzouZ.mp4",
    //             "mime_type":"video/mp4",
    //             "thumbnail":{
    //                 "file_id":"AAMCAQADHQR88vhoAANNZaamf-yQg2vknThhyyom1ds83DsAAlkDAAJdyTBF_LH0nq3N8bABAAdtAAM0BA",
    //                 "file_unique_id":"AQADWQMAAl3JMEVy",
    //                 "file_size":13550,
    //                 "width":320,
    //                 "height":180
    //             },
    //             "thumb":{
    //                 "file_id":"AAMCAQADHQR88vhoAANNZaamf-yQg2vknThhyyom1ds83DsAAlkDAAJdyTBF_LH0nq3N8bABAAdtAAM0BA",
    //                 "file_unique_id":"AQADWQMAAl3JMEVy",
    //                 "file_size":13550,
    //                 "width":320,
    //                 "height":180
    //             },
    //             "file_id":"BAACAgEAAx0EfPL4aAADTWWmpn_skINr5J04YcsqJtXbPNw7AAJZAwACXckwRfyx9J6tzfGwNAQ",
    //             "file_unique_id":"AgADWQMAAl3JMEU",
    //             "file_size":1361916
    //         }
    //     },
    //     {
    //         "message_id":78,
    //         "sender_chat":{
    //             "id":-100XXX,
    //             "title":"Concept testing",
    //             "type":"channel"
    //         },
    //         "chat":{
    //             "id":-100XXX,
    //             "title":"Concept testing",
    //             "type":"channel"
    //         },
    //         "date":1705420415,
    //         "media_group_id":"13643363322762233",
    //         "video":{
    //             "duration":52,
    //             "width":1920,
    //             "height":1080,
    //             "file_name":"T5rZnf2krEhDMJ70OFYnGh7hcDvqwoUOTTqJxvUG.mp4",
    //             "mime_type":"video/mp4",
    //             "thumbnail":{
    //                 "file_id":"AAMCAQADHQR88vhoAANOZaamf_aFmJDizRX6Chvzx_fW5s8AApEDAAKH_zBFUtZ86gE9_d4BAAdtAAM0BA",
    //                 "file_unique_id":"AQADkQMAAof_MEVy",
    //                 "file_size":11489,
    //                 "width":320,
    //                 "height":180
    //             },
    //             "thumb":{
    //                 "file_id":"AAMCAQADHQR88vhoAANOZaamf_aFmJDizRX6Chvzx_fW5s8AApEDAAKH_zBFUtZ86gE9_d4BAAdtAAM0BA",
    //                 "file_unique_id":"AQADkQMAAof_MEVy",
    //                 "file_size":11489,
    //                 "width":320,
    //                 "height":180
    //             },
    //             "file_id":"BAACAgEAAx0EfPL4aAADTmWmpn_2hZiQ4s0V-gob88f31ubPAAKRAwACh_8wRVLWfOoBPf3eNAQ",
    //             "file_unique_id":"AgADkQMAAof_MEU",
    //             "file_size":2742445
    //         }
    //     }
    // ]

    protected function updateFiles(Message $messages)
    {
        $messages->each(
            fn (array $message, int $index) => $this->updateFile($message, $this->post->filenames[$index])
        );
    }

    protected function updateFile(array $message, PostFile $media)
    {
        if ($media->file_id) {
            return $media;
        }

        if ($media->type === 'video') {
            $media->file_id = $message['video']['file_id'];
            $media->file_unique_id = $message['video']['file_unique_id'];
        } elseif ($media->type === 'photo') {
            $telegramPhoto = collect($message['photo'])->pop();
            $media->file_id = $telegramPhoto['file_id'];
            $media->file_unique_id = $telegramPhoto['file_unique_id'];
        } else {
            throw new Exception('Invalid type of Telegram Media');
        }

        return $media->save();
    }
}
