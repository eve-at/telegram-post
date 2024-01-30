<?php

namespace App\Console\Commands;

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
        //
    }
}
