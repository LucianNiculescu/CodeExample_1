<?php

/**
 * Guest factory for creating a fake guest for seeding during testing
 */
$factory->define(App\Models\AirConnect\User::class, function (Faker\Generator $faker) {
	return [
		'site'		=> function() {
			return factory(App\Models\AirConnect\Site::class)->create()->id;
		},
		'user' 		=> $faker->email,
		'name' 		=> $faker->name,
		'type' 		=> 'email',
		'password' 	=> bcrypt(str_random(10)),
		'mac'		=> $faker->macAddress,
		'status'	=> 'active'
	];
});
