<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Post;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Post::class, function(Faker $faker){
    return [
        'title'         => $faker->name,
        'description'   => $faker->name,
        'category_id'   => rand(1, 10),
        //		'keywords'    => $faker->name(),
        'content'       => $faker->text(1024),
        'status'        => 1,
        'view_count'    => 0,
        'comment_count' => 0,
        'praise_count'  => 0,
    ];
});
