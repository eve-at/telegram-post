<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poll>
 */
class PollFactory extends Factory
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
            'type'=> ['quiz', 'regular'][rand(0, 1)],
            'title' => str(fake()->sentence)->beforeLast('.') . '?', //question
            'options' => $this->options(count: rand(3, 10)),
            'correct_option_id' => 0,
            'explanation' => [null, null, str(fake()->sentence)->beforeLast('.')][rand(0, 2)],
        ];
    }

    protected function options($count = 5) 
    {
        return collect(range(1, $count))
                ->map(fn($el) => str(fake()->sentence(rand(1, 3), variableNbWords: false))->beforeLast('.'))
                ->toArray();
    }
    
}
