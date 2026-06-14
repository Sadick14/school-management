<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropClassIdFromSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('class_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedInteger('class_id')->nullable()->after('type');
        });

        // restore one class per subject (first match) so the FK below is satisfiable
        DB::table('class_subjects')->orderBy('subject_id')->orderBy('class_id')->get()
            ->groupBy('subject_id')
            ->each(function ($rows, $subjectId) {
                DB::table('subjects')->where('id', $subjectId)
                    ->update(['class_id' => $rows->first()->class_id]);
            });

        Schema::table('subjects', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('i_classes');
        });
    }
}
