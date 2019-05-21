<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

/**
 * Class CollectionServiceProvider
 *
 * Adds methods to the Laravel Collection object for use throughout the application
 *
 * @package App\Admin\Providers
 */
class CollectionServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		/**
		 * toAssoc() Collection method
		 *
		 * Creates an associative array from collection of items with two values
		 *
		 * <code>
		 * $example = collect([
		 * 	['john@example.com', 'John'],
		 *	['jane@example.com', 'Jane'],
		 * ])->toAssoc();
		 * // => [
		 *	//  'john@example.com' => 'John',
		 *	//  'jane@example.com' => 'Jane',
		 * ]
		 * </code>
		 */
		Collection::macro('toAssoc', function () {
			return $this->reduce(function ($assoc, $keyValuePair) {
				list($key, $value) = $keyValuePair;
				$assoc[$key] = $value;
				return $assoc;
			}, new static);
		});

		/**
		 * mapToAssoc() Collection method
		 *
		 * Combines the toAssoc() function with map() to allow a more concise transformation
		 *
		 * <code>
		 * $example = collect([
		 * 	[
		 *	'name' => 'John',
		 *	'department' => 'Sales',
		 *	'email' => 'john@example.com'
		 *	],
		 *	[
		 *	'name' => 'Jane',
		 *	'department' => 'Marketing',
		 *	'email' => 'jane@example.com'
		 *	]
		 * ])->mapToAssoc(function($person) {
		 * 		return [$person['email'], $person['name']];
		 * });
		 * // => [
		 *	//  'john@example.com' => 'John',
		 *	//  'jane@example.com' => 'Jane',
		 * ]
		 * </code>
		 */
		Collection::macro('mapToAssoc', function ($callback) {
			return $this->map($callback)->toAssoc();
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}