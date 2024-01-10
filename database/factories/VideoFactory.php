<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'channel_id' => Channel::first() ?? Channel::factory(),
            'user_id' => User::factory(),
            'title' => str(fake()->sentence())->beforeLast('.'),
            'body' => fake()->emoji() . ' ' . fake()->realText(150),
            'filename' => fake()->slug() . '.mp4',
            'file_id' => fake()->sha256(),
            'file_unique_id' => fake()->md5(),
            'width' => [null, 600, 800][rand(0, 2)],
            'height' => [null, 600, 800][rand(0, 2)],
            'duration' => rand(20, 200),
            'mime_type' => 'video/mp4',
            'source' => '@' . fake()->firstName() . '_' . fake()->lastName(),
        ];
    }
}
