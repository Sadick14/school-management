<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMultiClassSupportToExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_iclass', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('exam_id');
            $table->unsignedInteger('class_id');
            $table->timestamps();

            $table->unique(['exam_id', 'class_id']);
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('i_classes')->onDelete('cascade');
        });

        // migrate existing exams.class_id into the new pivot table
        $now = now();
        $rows = DB::table('exams')->select('id', 'class_id')->get();
        foreach ($rows as $row) {
            if ($row->class_id) {
                DB::table('exam_iclass')->insert([
                    'exam_id' => $row->id,
                    'class_id' => $row->class_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        Schema::table('exams', function (Blueprint $table) {
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
        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedInteger('class_id')->nullable()->after('id');
        });

        // backfill from pivot table (first associated class per exam)
        $pivots = DB::table('exam_iclass')->orderBy('id')->get();
        $seen = [];
        foreach ($pivots as $pivot) {
            if (!isset($seen[$pivot->exam_id])) {
                DB::table('exams')->where('id', $pivot->exam_id)->update(['class_id' => $pivot->class_id]);
                $seen[$pivot->exam_id] = true;
            }
        }

        Schema::dropIfExists('exam_iclass');
    }
}
