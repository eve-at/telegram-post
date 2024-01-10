<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\File;
use App\Models\Media;
use App\Models\MediaGroup;
use App\Models\MediaGroupFile;
use App\Models\Photo;
use App\Models\User;
use App\Models\Video;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MediaGroup>
 */
class MediaGroupFactory extends Factory
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
            'title' => str(fake()->sentence(rand(2, 4)))->beforeLast('.'),
            'body' => fake()->emoji() . ' ' . fake()->paragraph(),
            'source' => '@' . fake()->firstName() . '_' . fake()->lastName(),
        ];
    }

    public function configure() 
    {
        return $this->afterCreating(function (MediaGroup $mediaGroup) {
            collect(range(1, rand(2, 10)))->each(function ($item) use ($mediaGroup) {
                static $order = 0;
                $mediable = $this->mediableType();
                $file = File::factory()->create([
                    'type' => $mediable[0],
                    'filename' => fake()->slug() . $mediable[1],
                ]);
                MediaGroupFile::create([
                    'media_group_id' => $mediaGroup->id,
                    'file_id' => $file->id,
                    'order' => $order++,
                ]);
            });
        });
    }
    
    protected function mediableType() 
    {
        return [
            ['photo', '.jpg'],
            ['video', '.mp4'],
            ['document', '.pdf'],
        ][rand(0, 2)];
    }
    
}
