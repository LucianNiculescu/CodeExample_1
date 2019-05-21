<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QueueCreation extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('airconnect')->create('queue', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('queue');
			$table->longText('payload');
			$table->tinyInteger('attempts')->unsigned();
			$table->unsignedInteger('reserved_at')->nullable();
			$table->unsignedInteger('available_at');
			$table->unsignedInteger('created_at');
			$table->index(['queue', 'reserved_at']);
		});

		Schema::connection('airconnect')->create('queue_failed', function (Blueprint $table) {
			$table->increments('id');
			$table->text('connection');
			$table->text('queue');
			$table->longText('payload');
			$table->longText('exception');
			$table->timestamp('failed_at')->useCurrent();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('airconnect')->dropIfExists('queue');
		Schema::connection('airconnect')->dropIfExists('queue_failed');
	}
}
