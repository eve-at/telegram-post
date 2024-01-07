<?php

namespace Database\Factories;

use App\Models\File;
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
            'file_id' => File::factory(),
            'file_name' => fake()->slug(rand(1,3)) . '.jpg',
            'width' => [null, 600, 800][rand(0, 2)],
            'height' => [null, 600, 800][rand(0, 2)],
            'duration' => rand(20, 200),
            'mime_type' => 'video/mp4',
            'source' => fake()->firstName() . ' ' . fake()->lastName(),
        ];
    }
}
