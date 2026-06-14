<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSelectiveAndElectiveSubjectsFromIclassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('i_classes', function (Blueprint $table) {
            $table->dropColumn(['have_selective_subject', 'max_selective_subject', 'have_elective_subject']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('i_classes', function (Blueprint $table) {
            $table->boolean('have_selective_subject')->default(false);
            $table->unsignedTinyInteger('max_selective_subject')->nullable();
            $table->boolean('have_elective_subject')->default(false);
        });
    }
}
