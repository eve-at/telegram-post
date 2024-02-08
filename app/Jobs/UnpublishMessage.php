<?php

namespace App\Jobs;

use App\DTOs\PublishedMessage;
use App\Events\MessagePublished;
use App\Events\MessageUnpublished;
use App\Http\Services\TelegramService;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnpublishMessage implements ShouldQueue
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
        if (TelegramService::deleteMessage($this->message)) {
            MessageUnpublished::dispatch($this->message);
        }        
    }
}
