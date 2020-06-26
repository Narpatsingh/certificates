<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmailSettingIdInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
              $table->dropColumn('email_setting_id');
              $table->enum('email_setting', array('SMTP','MAILGUN','SES','POSTMARK'))->nullable()->after('remember_token');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
              $table->integer('email_setting_id')->nullable();
              $table->dropColumn('email_setting');
        });
    }
}
