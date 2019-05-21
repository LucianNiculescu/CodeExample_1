<?php

/**
 * Site factory for creating a fake Site for seeding during testing
 */
$factory->define(App\Models\AirConnect\Site::class, function (Faker\Generator $faker) {
	return [
		'name'		=> $faker->company,
		'location' 	=> $faker->longitude . ', ' . $faker->latitude,
		'reference' => $faker->randomDigitNotNull,
		'contact'	=> $faker->email,
		'status'	=> 'active'
	];
});