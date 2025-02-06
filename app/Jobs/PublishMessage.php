<?php

namespace App\Jobs;

use App\DTOs\PublishedMessage;
use App\Events\MessagePublished;
use App\Http\Services\TelegramService;
use App\Models\Message;
use App\Notifications\MessagePublished as NotificationsMessagePublished;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Message $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Log::info('publish message #' . $this->message->id . ' into chat ' . $this->message->messagable->channel->chat_id);
        $response = TelegramService::make($this->message->messagable)->publish();

        //TODO: $this->message->messagable()->first()?->user()->first()->notify(new NotificationsMessagePublished($this->message));

        MessagePublished::dispatch(new PublishedMessage(
            message: $this->message,
            response: $response
        ));
    }
}
