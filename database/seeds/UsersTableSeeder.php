<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        factory(App\Models\User::class, 50)->create()->each(function(User $u){
            $u->posts()->save(factory(App\Models\Post::class)->make());
        });
    }
}
