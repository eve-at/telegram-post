<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\MediaGroup;
use App\Models\MediaGroupFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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
            'type' => Arr::random(['message', 'photo', 'video', 'media_group']),
            'body' => fake()->emoji() . ' ' . fake()->paragraph(),
            'source' => fake()->firstName() . '_' . fake()->lastName(),
        ];
    }

    public function configure() 
    {
        return $this->afterCreating(function (MediaGroup $mediaGroup) {
            if ($mediaGroup->type === 'message') {
                return;
            }

            $nFiles = 1;
            if ($mediaGroup->type === 'media_group') {
                $nFiles = rand(2, 10);
            }

            collect(range(1, $nFiles))->each(function ($item) use ($mediaGroup) {
                static $order = 0;
                $mediable = $this->mediableType($mediaGroup->type);

                MediaGroupFile::create([
                    'media_group_id' => $mediaGroup->id,
                    'type' => $mediable[0],
                    'filename' => fake()->slug() . $mediable[1],
                    'file_id' => fake()->sha256(),
                    'file_unique_id' => fake()->md5(),
                    'order' => $order++,
                ]);
            });
        });
    }
    
    protected function mediableType(?string $type = null) 
    {
        if ($type === 'media_group') {
            $type = Arr::random(['photo', 'video']);
        }

        return [
            'photo' => ['photo', '.jpg'],
            'video' => ['video', '.mp4'],
            //'document' => ['document', '.pdf'],
        ][$type];
    }
    
}
