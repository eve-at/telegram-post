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
            'file_id' => File::factory()->create([
                'type' => 'video',
                'filename' => fake()->slug() . '.mp4',
            ]),
            'title' => str(fake()->sentence())->beforeLast('.'),
            'body' => fake()->emoji() . ' ' . fake()->realText(150),
            'width' => [null, 600, 800][rand(0, 2)],
            'height' => [null, 600, 800][rand(0, 2)],
            'duration' => rand(20, 200),
            'mime_type' => 'video/mp4',
            'source' => '@' . fake()->firstName() . '_' . fake()->lastName(),
        ];
    }
}
