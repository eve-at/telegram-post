<?php

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $file = [
            ['photo', '.jpg'],
            ['video', '.mp4'],
            ['document', '.pdf'],
        ][rand(0, 2)];

        return [
            'channel_id' => Channel::find(1) ?? Channel::factory()->create(),
            'file_id' => fake()->sha256(),
            'file_unique_id' => fake()->md5(),
            'type' => $file[0],
            'filename' => fake()->slug() . $file[1],
        ];
    }
}
