<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RedesignExamMarksGradingSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. exams table
        if (Schema::hasColumn('exams', 'marks_distribution_types')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropColumn(['marks_distribution_types', 'elective_subject_point_addition']);
            });
        }
        if (!Schema::hasColumn('exams', 'ca_weight')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->unsignedTinyInteger('ca_weight')->default(30)->after('name');
            });
        }

        // 2. exam_rules table
        Schema::table('exam_rules', function (Blueprint $table) {
            $table->dropColumn(['marks_distribution', 'passing_rule', 'combine_subject_id', 'total_exam_marks', 'over_all_pass']);
        });
        Schema::table('exam_rules', function (Blueprint $table) {
            $table->unsignedInteger('ca_total_marks')->default(100)->after('grade_id');
            $table->unsignedInteger('exam_total_marks')->default(100)->after('ca_total_marks');
            $table->unsignedTinyInteger('pass_mark')->default(40)->after('exam_total_marks');
        });

        // 3. marks table
        Schema::table('marks', function (Blueprint $table) {
            $table->dropColumn(['marks', 'point']);
        });
        Schema::table('marks', function (Blueprint $table) {
            $table->decimal('ca_marks', 5, 2)->nullable()->default(0)->after('subject_id');
            $table->decimal('exam_marks', 5, 2)->nullable()->default(0)->after('ca_marks');
        });
        Schema::table('marks', function (Blueprint $table) {
            $table->decimal('total_marks', 5, 2)->default(0)->change();
        });

        // 4. results table
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['point']);
        });
        Schema::table('results', function (Blueprint $table) {
            $table->decimal('total_marks', 5, 2)->default(0)->change();
        });

        // 5. drop result_combines table entirely
        Schema::dropIfExists('result_combines');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate result_combines
        Schema::create('result_combines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_id');
            $table->unsignedInteger('subject_id');
            $table->unsignedInteger('exam_id');
            $table->integer('total_marks');
            $table->string('grade');
            $table->decimal('point', 5, 2);

            $table->foreign('registration_id')->references('id')->on('registrations');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('exam_id')->references('id')->on('exams');
        });

        // results table
        Schema::table('results', function (Blueprint $table) {
            $table->integer('total_marks')->default(0)->change();
        });
        Schema::table('results', function (Blueprint $table) {
            $table->decimal('point', 5, 2)->default(0);
        });

        // marks table
        Schema::table('marks', function (Blueprint $table) {
            $table->integer('total_marks')->default(0)->change();
        });
        Schema::table('marks', function (Blueprint $table) {
            $table->dropColumn(['ca_marks', 'exam_marks']);
        });
        Schema::table('marks', function (Blueprint $table) {
            $table->text('marks')->nullable();
            $table->decimal('point', 5, 2)->default(0);
        });

        // exam_rules table
        Schema::table('exam_rules', function (Blueprint $table) {
            $table->dropColumn(['ca_total_marks', 'exam_total_marks', 'pass_mark']);
        });
        Schema::table('exam_rules', function (Blueprint $table) {
            $table->unsignedInteger('combine_subject_id')->nullable();
            $table->text('marks_distribution')->nullable();
            $table->enum('passing_rule', [1, 2, 3])->default(1);
            $table->integer('total_exam_marks')->default(0);
            $table->integer('over_all_pass')->default(0);
        });
        Schema::table('exam_rules', function (Blueprint $table) {
            $table->foreign('combine_subject_id')->references('id')->on('subjects');
        });

        // exams table
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['ca_weight']);
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->decimal('elective_subject_point_addition', 5, 2)->default(0.00);
            $table->text('marks_distribution_types')->nullable();
        });
    }
}
