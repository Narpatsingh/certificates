<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleslidepdfidInCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `certificates` ADD `google_certificate_pdf_id` VARCHAR(255) NULL DEFAULT NULL AFTER `certificate_url`, ADD `google_certificate_slide_id` VARCHAR(255) NULL DEFAULT NULL AFTER `google_certificate_pdf_id`, ADD `google_certificate_pdf_link` VARCHAR(255) NULL DEFAULT NULL AFTER `google_certificate_slide_id`, ADD `certificate_pdf_file` VARCHAR(150) NULL DEFAULT NULL AFTER `google_certificate_pdf_link`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `certificates` DROP `google_certificate_pdf_id`, DROP `google_certificate_slide_id`, DROP `google_certificate_pdf_link`, DROP `certificate_pdf_file`;");
    }
}
