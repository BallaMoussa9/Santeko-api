<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilePathToMedicalReportsTable extends Migration
{
    public function up()
    {
        Schema::table('medical_reports', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('content');
        });
    }

    public function down()
    {
        Schema::table('medical_reports', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
}