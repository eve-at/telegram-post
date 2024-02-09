<?php

namespace App\Console\Commands;

use App\Http\Services\Scheduler;
use App\Models\Channel;
use Illuminate\Console\Command;

class AutoSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-schedule messages in each channel on shoosen hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Channel::all()->each(function ($channel) {
            if (in_array(now($channel->timezone)->hour, $channel->hours)) {
                Scheduler::autoScheduledMessage(
                    channel: $channel,
                    publishAt: now()->addMinutes(2)
                );
            }
        });
    }
}
