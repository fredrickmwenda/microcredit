<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_savings_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('savings_officer_id')->comment('Officer whose clients are being assisted');
            $table->integer('created_by_user_id')->comment('User assisting with entries');
            $table->integer('verified_by_user_id')->nullable()->comment('Savings Operator who verified');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bulk_savings_entries');
    }
};
