<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Channel;
use App\Models\File;
use App\Models\Media;
use App\Models\MediaGroup;
use App\Models\MediaGroupFile;
use App\Models\Message;
use App\Models\Photo;
use App\Models\Poll;
use App\Models\Post;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use function Laravel\Prompts\password;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $users = [User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ])];

        $channel = Channel::factory()->create([
            'chat_id' => env('TELEGRAM_CHANNEL_ID'),
            'name' => env('TELEGRAM_CHANNEL_NAME'),
            'signature' => '<a href="#">Subscribe</a>',
        ]);        

        collect([
            Post::class,
            Photo::class,
            Video::class,
            MediaGroup::class,
            Poll::class,
        ])->each(function ($class) use ($channel, $users) {
            $messagable = $class::factory(50)->recycle($users)->create();
            $messagable->each(function($messagable) use ($channel) {
                Message::factory()->create([
                    'channel_id' => $channel,
                    'messagable_type' => get_class($messagable),
                    'messagable_id' => $messagable->id,
                    'body' => $messagable->body ? $messagable->body . '<i>Source: Lorem Ipsum</i>' . $channel->signature : null,
                ]);
            });
        });
    }
}
