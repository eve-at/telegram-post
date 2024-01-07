<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Channel;
use App\Models\File;
use App\Models\Media;
use App\Models\MediaGroup;
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
        Message::truncate();
        Post::truncate();
        Channel::truncate();
        MediaGroup::truncate();
        Photo::truncate();
        Video::truncate();
        Media::truncate();
        File::truncate();
        Poll::truncate();
        User::truncate();

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

        $posts = Post::factory(100)->recycle($users)->create();

        $posts->each(function($post) use ($channel) {
            Message::factory()->create([
                'channel_id' => $channel,
                'post_id' => $post,
                'text' => $post->text . '<i>Source: Lorem Ipsum</i>' . $channel->signature,
            ]);
        });
    }
}
