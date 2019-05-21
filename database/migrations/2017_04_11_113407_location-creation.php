<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LocationCreation extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::connection('airconnect')->hasTable('locations')) {
			Schema::connection('airconnect')->create('locations', function (Blueprint $table) {
				$table->mediumIncrements('id');
				$table->mediumInteger('site_id')->unsigned();
				$table->string('name', 64);
				$table->string('room_no', 64);
				$table->string('type', 32);
				$table->string('status', 16)->default('active');
				$table->timestamp('updated')->useCurrent();
				$table->timestamp('created')->nullable()->default( null ); // Create with todays' date
			});
		}

		if (!Schema::connection('airconnect')->hasTable('vlan')) {
			Schema::connection('airconnect')->create('vlan', function (Blueprint $table) {
				$table->mediumIncrements('id');
				$table->mediumInteger('location_id')->unsigned();
				$table->string('vlan', 32);
				$table->timestamp('updated')->useCurrent();
				$table->timestamp('created')->nullable()->default( null ); // Create with todays' date
			});
		}

		if (!Schema::connection('airconnect')->hasTable('location_portal')) {
			Schema::connection('airconnect')->create('location_portal', function (Blueprint $table) {
				$table->mediumIncrements('id');
				$table->mediumInteger('location_id')->unsigned();
				$table->mediumInteger('portal_id')->unsigned();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('airconnect')->dropIfExists('locations');
		Schema::connection('airconnect')->dropIfExists('location_portals');
		Schema::connection('airconnect')->dropIfExists('location_portal');
	}
}
