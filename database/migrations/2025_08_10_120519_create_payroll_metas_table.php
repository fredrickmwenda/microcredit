<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_metas', function (Blueprint $table) {
            $table->id();
                        $table->unsignedBigInteger('payroll_id'); // Link to payroll
            $table->unsignedBigInteger('payroll_template_meta_id')->nullable(); // Link to template meta
            $table->string('value')->nullable(); // Meta value
            $table->integer('position')->nullable(); // Optional ordering
            $table->timestamps();

            // Foreign key to payroll table
            $table->foreign('payroll_id')
                  ->references('id')
                  ->on('payrolls')
                  ->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_metas');
    }
}
