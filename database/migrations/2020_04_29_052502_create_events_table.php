<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id');
			$table->string('name');
			$table->string('website')->nullable();
			$table->text('description', 65535)->nullable();
			$table->string('org_name')->nullable();
			$table->string('org_website')->nullable();

			$table->string('google_account_id');
			$table->string('google_slide_id');
			$table->string('google_slide_id_name');
			$table->string('google_sheet_id');
			$table->string('google_sheet_id_name'); ;
			$table->string('google_upload_folder');
			$table->string('google_upload_folder_name');
			$table->string('email_subject')->nullable();
			$table->text('email_message', 65535)->nullable();
			$table->text('email_recipient_field', 65535)->nullable();
			$table->text('email_recipient_cc', 65535)->nullable();
			$table->text('email_recipient_bcc', 65535)->nullable();
			$table->text('email_sender_name', 65535)->nullable();
			$table->string('email_sender_email')->nullable();
			$table->string('email_sender_replyto')->nullable();
			$table->enum('attach_certificate', array('Yes','No'))->default("Yes")->nullable();
			$table->enum('public_link', array('On','Off'))->default("Off")->nullable();
			$table->integer('certificate_count')->nullable();
			$table->enum('status', array('Pending','Importing','Imported','Generating','Generated','Sending','Completed','Importing Failed','Generating Failed','Sending Failed'))->default("Pending");
			$table->timestamps();
			$table->datetime('scheduled_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('events');
	}

}
