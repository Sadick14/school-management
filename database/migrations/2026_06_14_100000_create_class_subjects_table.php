<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('class_id');
            $table->unsignedInteger('subject_id');
            $table->timestamps();

            $table->unique(['class_id', 'subject_id']);
            $table->foreign('class_id')->references('id')->on('i_classes')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });

        // backfill from existing subjects.class_id
        $now = now();
        DB::table('subjects')->whereNotNull('class_id')->select('id', 'class_id')->get()
            ->each(function ($subject) use ($now) {
                DB::table('class_subjects')->insert([
                    'class_id' => $subject->class_id,
                    'subject_id' => $subject->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_subjects');
    }
}
