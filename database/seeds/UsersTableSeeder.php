<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run(){
		DB::table('users')->insert([
			'name' => str_random(10),
			'email' => str_random(10).'@beidou.com',
			'password' => bcrypt('123456'),
		]);
	}
}
