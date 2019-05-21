<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FormsAndPivotCreation extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::connection('airconnect')->hasTable('forms')) {
			Schema::connection('airconnect')->create('forms', function (Blueprint $table) {
				$table->mediumIncrements('id');
				$table->integer('site_id')->nullable();
				$table->string('name', 255);
				$table->string('status', 16)->default('active');
				$table->timestamp('updated')->useCurrent();
				$table->timestamp('created')->nullable()->default( null ); // Create with todays' date
			});
		}
		// Pivot table between form and portal
		if (!Schema::connection('airconnect')->hasTable('form_portal')) {
			Schema::connection('airconnect')->create('form_portal', function (Blueprint $table) {
				$table->mediumIncrements('id');
				$table->mediumInteger('form_id')->unsigned();
				$table->mediumInteger('portal_id')->unsigned();
			});
		}
		// Pivot table between form and questions
		if (!Schema::connection('airconnect')->hasTable('form_question')) {
			Schema::connection('airconnect')->create('form_question', function (Blueprint $table) {
				$table->mediumIncrements('id');
				$table->mediumInteger('form_id')->unsigned();
				$table->mediumInteger('question_id')->unsigned();
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
		Schema::connection('airconnect')->dropIfExists('forms');
		Schema::connection('airconnect')->dropIfExists('form_portal');
		Schema::connection('airconnect')->dropIfExists('form_question');
	}
}
