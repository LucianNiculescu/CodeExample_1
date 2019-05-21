<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RolesCreationData extends Migration
{
	public static $roles = [
		'1' => 'System Admin',
		'2' => 'Airangel Sales',
		'3' => 'Airangel Account Manager',
		'4' => 'Airangel Support - third line',
		'5' => 'First and Second line support',
		'9' => 'Partner Admin',
		'21' => 'Super Admin',
		'22' => 'Admin',
		'23' => 'Manager'

	];

	public function up()
	{
		// Check if the table exists before trying to create it
		if (!Schema::connection('airconnect')->hasTable('roles')) {

			// Create
			Schema::connection('airconnect')->create('roles', function (Blueprint $table) {

				$table->mediumIncrements('id');
				$table->string('role', 64);
				$table->integer('site_id')->nullable();
				$table->string('status', 16)->default('active');
				$table->timestamp('updated')->useCurrent();
				$table->timestamp('created')->nullable()->default( null ); // Create with todays' date
			});

			// SEED DATA
			// After creation we need to see the data with the default roles
			DB::connection('airconnect')->table('roles')
				->insert([
					[ 'role'=>'Dev', 'created'=>date('Y-m-d H:i:s') ],
				]);

			// Update the Dev role ID to the correct one
			DB::connection('airconnect')->table('roles')
				->where('role', 'Dev')
				->update(['id' => 0]);

			// The next role added should be 3, not 4
			DB::statement("ALTER TABLE `airconnect`.`roles` AUTO_INCREMENT = 1;");

			//Check if the id exists and change the name or add a new record having the id and name
			foreach(self::$roles as $id => $name) {
				DB::connection('airconnect')
					->table('roles')
					->insert([
						'id' 		=> $id,
						'role'		=> $name,
						'site_id' 	=> null,
						'status'  	=> 'active',
						'created' 	=> date('Y-m-d H:i:s')
					]);

			}
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Test if the table exists before dropping
		Schema::connection('airconnect')->dropIfExists('roles');
	}
}
