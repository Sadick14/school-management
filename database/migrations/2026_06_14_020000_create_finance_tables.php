<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceTables extends Migration
{
    public function up()
    {
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('academic_year_id');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['0', '1'])->default('1');
            $table->timestamps();
            $table->softDeletes();
            $table->userstamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years');
        });

        Schema::create('fee_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->enum('billing_cycle', ['term', 'daily', 'once_per_year', 'once_per_student', 'ad_hoc']);
            $table->enum('applies_to', ['all', 'new_students_only', 'continuing_only'])->default('all');
            $table->boolean('is_optional')->default(false);
            $table->enum('status', ['0', '1'])->default('1');
            $table->timestamps();
            $table->softDeletes();
            $table->userstamps();
        });

        Schema::create('fee_structures', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('academic_year_id');
            $table->unsignedInteger('fee_type_id');
            $table->unsignedInteger('class_id')->nullable();
            $table->unsignedInteger('term_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['0', '1'])->default('1');
            $table->timestamps();
            $table->softDeletes();
            $table->userstamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years');
            $table->foreign('fee_type_id')->references('id')->on('fee_types');
            $table->foreign('class_id')->references('id')->on('i_classes');
            $table->foreign('term_id')->references('id')->on('academic_terms');
        });

        Schema::create('student_ledgers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('academic_year_id');
            $table->unsignedInteger('fee_type_id');
            $table->unsignedInteger('term_id')->nullable();
            $table->date('billing_date')->nullable();
            $table->string('description')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->enum('source', ['auto', 'manual', 'opening_balance', 'adjustment'])->default('auto');
            $table->enum('status', ['0', '1'])->default('1');
            $table->timestamps();
            $table->softDeletes();
            $table->userstamps();

            $table->foreign('registration_id')->references('id')->on('registrations');
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('academic_year_id')->references('id')->on('academic_years');
            $table->foreign('fee_type_id')->references('id')->on('fee_types');
            $table->foreign('term_id')->references('id')->on('academic_terms');
            $table->index(['registration_id', 'fee_type_id', 'term_id', 'billing_date'], 'ledger_lookup_idx');
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('receipt_no', 50)->unique();
            $table->date('payment_date');
            $table->unsignedInteger('academic_year_id');
            $table->unsignedInteger('registration_id')->nullable();
            $table->unsignedInteger('student_id');
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method', 30);
            $table->string('paid_by')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->userstamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years');
            $table->foreign('registration_id')->references('id')->on('registrations');
            $table->foreign('student_id')->references('id')->on('students');
        });

        Schema::create('fee_payment_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fee_payment_id');
            $table->unsignedInteger('student_ledger_id');
            $table->decimal('amount_applied', 12, 2);
            $table->timestamps();

            $table->foreign('fee_payment_id')->references('id')->on('fee_payments');
            $table->foreign('student_ledger_id')->references('id')->on('student_ledgers');
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('status', ['0', '1'])->default('1');
            $table->timestamps();
            $table->softDeletes();
            $table->userstamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('expense_category_id');
            $table->date('expense_date');
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('payment_method', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->userstamps();

            $table->foreign('expense_category_id')->references('id')->on('expense_categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_payment_items');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('student_ledgers');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_types');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('academic_terms');
    }
}
