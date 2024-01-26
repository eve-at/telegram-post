<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Channel>
 */
class ChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->firstName() . ' ' . fake()->lastName();
        return [
            'name' => $name,
            'chat_id' => '-100' . fake()->randomNumber(5, strict: true) . fake()->randomNumber(5, strict: true),
            'signature' => '<a href="' . config('app.TELEGRAM_CONCEPT_CHANNEL_LINK') . "\">$name</a>",
            'hours' => [rand(0, 12), rand(13, 23)],
            'timezone' => 'Europe/London',
        ];
    }
}
