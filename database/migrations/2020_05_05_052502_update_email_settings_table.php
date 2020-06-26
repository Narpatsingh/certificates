<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmailSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('email_settings');
				Schema::create('email_settings', function(Blueprint $table)
        {
            $table->integer('id', true);
            $table->integer('user_id');
            
						$table->integer('is_smtp')->default(0);
            $table->enum('smtp_encryption', array('tsl','ssl'))->nullable();
            $table->string('smtp_host_address')->nullable();
            $table->integer('smtp_host_port')->nullable();
            $table->string('smtp_server_username', 100)->nullable();
            $table->string('smtp_server_password')->nullable();
            
						$table->integer('is_mailgun')->default(0);
						$table->string('mailgun_secret')->nullable();
            $table->string('mailgun_domain')->nullable();
            $table->string('mailgun_endpoint')->nullable();
            
						$table->integer('is_ses')->default(0);
						$table->string('ses_key')->nullable();
            $table->string('ses_secret')->nullable();
            $table->string('ses_region')->nullable();
						
						$table->integer('is_postmark')->default(0);
            $table->string('postmark_token')->nullable();
						
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('email_settings');
    }
}
