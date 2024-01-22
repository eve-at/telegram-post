<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Post;
use App\Models\Poll;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = Carbon::make(fake()->dateTimeBetween('-1 year', 'now'));
        $publishedAt = Carbon::make($createdAt)->addDays(60);

        $messagable = [
            Post::class,
            Poll::class,
        ][rand(0, 1)];

        return [
            'channel_id' => Channel::first() ?? Channel::factory(),
            'messagable_type' => $messagable,
            'messagable_id' => $messagable::factory(),
            'body' => 
                fake()->emoji() 
                . fake()->paragraphs(1, asText: true)
                . fake()->emoji() 
                . fake()->paragraphs(1, asText: true)
                . fake()->emoji(),
            'created_at' => $createdAt,
            'published_at' => $publishedAt,
            'status' => Carbon::now()->greaterThan($publishedAt),
        ];
    }
}
