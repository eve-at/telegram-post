<?php

namespace App\Console\Commands;

use App\Jobs\PublishMessage;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PublishMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push messages to Telegram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queue = 'default'; // 'ads'

        // $message = Message::first();
        // dispatch(new PublishMessage($message))->onQueue($queue);
        // $this->info(sprintf('Pushed to `%s` queue: [%s] `%s` ', $queue, $message->messagable->type, $message->messagable->title));

        $now = Carbon::now();
        Message::whereBetween(
                'publish_at', 
                [$now->startOfMinute()->toDateTimeString(), $now->endOfMinute()->toDateTimeString()]
            )
            ->where('status', 0)
            ->each(function (Message $message) use ($queue) {
                dispatch(new PublishMessage($message))->onQueue($queue);       
                $this->info(sprintf('Pushed to `%s` queue: [%s] `%s` ', $queue, $message->messagable->type, $message->messagable->title));         
            });
    }
}
