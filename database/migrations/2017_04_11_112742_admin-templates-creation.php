<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdminTemplatesCreation extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('airconnect')->dropIfExists('admin_templates');

		// checks the admin_templates table doesn't already exist
		if (!Schema::connection('airconnect')->hasTable('admin_templates')) {

			// creates the templates table
			Schema::connection('airconnect')->create('admin_templates', function (Blueprint $table) {

				$table->mediumIncrements('id');
				$table->string('name', 32);
				$table->tinyInteger('http')->default(1);
				$table->string('url', 255);
				$table->string('status', 16)->default('active');
				$table->timestamp('updated')->useCurrent();
				$table->timestamp('created')->nullable()->default( null ); // Create with todays' date

			});

			// seed data
			DB::connection('airconnect')->table('admin_templates')
				->insert([
					[
						'name' 		=> 'system',
						'url' 		=> 'no referrer',
						'http' 		=> 0,
						'created' 	=> date('Y-m-d H:i:s')
					],
					[
						'name' 		=> 'airangel',
						'url' 		=> 'local.myairangel.net',
						'http' 		=> 0,
						'created' 	=> date('Y-m-d H:i:s')
					],
					[
						'name' 		=> 'airangel',
						'url' 		=> 'dev.myairangel.net',
						'http' 		=> 0,
						'created' 	=> date('Y-m-d H:i:s')
					],
					[
						'name' 		=> 'airangel',
						'url' 		=> 'staging.myairangel.net',
						'http' 		=> 0,
						'created' 	=> date('Y-m-d H:i:s')
					]
				]);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('airconnect')->dropIfExists('admin_templates');

	}
}
