<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeSectionIdNullableInRegistrationsAndMarksTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable()->change();
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable(false)->change();
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable(false)->change();
        });
    }
}
