<?php

namespace App\Console\Commands;

use App\Jobs\UnpublishMessage;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UnpublishMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:unpublish-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove message from Telegram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queue = 'default'; // 'ads'

        $now = Carbon::now();
        Message::whereBetween(
                'ad_delete_at', 
                [$now->startOfMinute()->toDateTimeString(), $now->endOfMinute()->toDateTimeString()]
            )
            ->where('status', 1)
            ->where('ad_deleted_at', null)
            ->each(function (Message $message) use ($queue) {
                dispatch(new UnpublishMessage($message))->onQueue($queue);       
                $this->info(sprintf('Pushed to `%s` queue: [%s] `%s` ', $queue, $message->messagable->type, $message->messagable->title));         
            });
    }
}
