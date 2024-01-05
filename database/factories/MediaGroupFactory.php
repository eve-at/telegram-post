<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\MediaGroup;
use App\Models\Photo;
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
            'title' => fake()->sentence(rand(2, 4)),
        ];
    }

    public function configure() 
    {
        return $this->afterCreating(function (MediaGroup $mediaGroup) {
            collect(range(1, rand(2, 10)))->each(function ($item) use ($mediaGroup) {
                static $order = 1;
                $mediableClass = $this->mediableClass();
                $mediable = $mediableClass::factory()->create();
                Media::create([
                    'media_group_id' => $mediaGroup->id,
                    'mediable_type' => $mediableClass,
                    'mediable_id' => $mediable->id,
                    'order' => $order++,
                ]);
            });
        });
    }
    
    protected function mediableClass() 
    {
        return [
            Photo::class,
            Photo::class,
            Video::class,
        ][rand(0, 2)];
    }
    
}
