<?php

namespace Database\Factories;

use App\Models\MediaGroup;
use App\Models\Photo;
use App\Models\Poll;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $postableClass = $this->postableClass();
        $postable = $postableClass::factory()->create();

        return [
            'title' => str(fake()->sentence)->beforeLast('.'),
            'text' => $postableClass === Poll::class ? null : 
                fake()->emoji() 
                . fake()->paragraphs(1, asText: true)
                . fake()->emoji() 
                . fake()->paragraphs(1, asText: true)
                . fake()->emoji(),
            'postable_type' => $postable::class,
            'postable_id' => $postable->id,
            'user_id' => User::factory()
        ];
    }

    protected function postableClass() 
    {
        return [
            Photo::class,
            Video::class,
            MediaGroup::class,
            Poll::class,
        ][rand(0, 3)];
    }
    
}
