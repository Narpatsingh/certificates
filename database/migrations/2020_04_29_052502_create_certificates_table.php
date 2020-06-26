<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('certificates', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('event_id');
			$table->text('payload', 65535)->nullable();
			$table->string('name')->nullable();
			$table->string('email')->nullable();
			$table->text('certificate_url', 65535)->nullable();
			$table->timestamps();
			$table->dateTime('expired_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('certificates');
	}

}
