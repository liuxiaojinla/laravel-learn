<?php

namespace Plugins\article\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(24),
            'description' => $this->faker->realText(),
            'content' => $this->faker->paragraph(50),
        ];
    }
}
