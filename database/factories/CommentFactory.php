<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\{Post, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'comment' => $this->faker->text,
            'post_id' => Post::inRandomOrder()->first()?->id ?? User::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
