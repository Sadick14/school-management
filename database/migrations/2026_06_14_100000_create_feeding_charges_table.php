<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedingChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeding_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_id');
            $table->unsignedInteger('student_ledger_id');
            $table->date('charge_date');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['registration_id', 'charge_date']);
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('student_ledger_id')->references('id')->on('student_ledgers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feeding_charges');
    }
}
