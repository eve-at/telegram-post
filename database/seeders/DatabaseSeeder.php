<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Channel;
use App\Models\Post;
use App\Models\Message;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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

        $factories = collect([
            Post::factory(100),
            Poll::factory(10),
        ]);

        collect([       
            Channel::factory()->create([
                'chat_id' => config('app.TELEGRAM_CONCEPT_CHAT_ID'),
                'name' => config('app.TELEGRAM_CONCEPT_CHAT_NAME'),
                'signature' => '<a href="' . config('app.TELEGRAM_CONCEPT_CHAT_LINK') . '">' . config('app.TELEGRAM_CONCEPT_CHAT_NAME') . '</a>',
                'hours' => [8, 18],
            ]),
            ...Channel::factory(9)->create()
        ])->each(function ($channel) use ($users, $factories) {
            $factories->each(function ($factory) use ($channel, $users) {
                $messagable = $factory->recycle($users)->create([
                    'channel_id' => $channel,
                ]);

                $messagable->each(function($messagable) use ($channel) {
                    Message::factory()->create([
                        'channel_id' => $channel,
                        'messagable_type' => get_class($messagable),
                        'messagable_id' => $messagable->id,
                        'body' => $messagable->body ? $messagable->body . '<i>Source: Lorem Ipsum</i>' . $channel->signature : null,
                    ]);
                });
            });
        });
    }
}
