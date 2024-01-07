<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Post;
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

        return [
            'channel_id' => Channel::find(1) ?? Channel::factory()->create(),
            'post_id' => Post::factory(),
            'text' => 
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
