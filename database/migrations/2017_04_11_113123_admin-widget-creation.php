<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdminWidgetCreation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

	public function up()
	{
		if (!Schema::connection('airconnect')->hasTable('admin_widget')) {

			Schema::create('admin_widget', function (Blueprint $table) {

				$table->mediumInteger('admin_id')->unsigned(); // 8
				$table->mediumInteger('widget_id')->unsigned(); // 8
				$table->string('route', 60);
				$table->tinyInteger('order');  // 0-255
				$table->string('status', 32)->default( 'untoggled' );
				$table->timestamp('updated')->useCurrent();
				$table->timestamp('created')->nullable()->default( null ); // Create with todays' date

				// set tables primary keys
				$table->primary(['admin_id', 'widget_id', 'route']);
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
		//commented out because we dont want to delete the table unless we are testing the migration
		Schema::connection('airconnect')->dropIfExists('admin_widget');
	}
}
