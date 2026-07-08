<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_officer_change_audits', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_id');
            $table->integer('client_id')->nullable();
            $table->string('loan_account_number')->nullable();
            $table->integer('old_officer_id')->nullable();
            $table->integer('new_officer_id')->nullable();
            $table->string('old_officer_name')->nullable();
            $table->string('new_officer_name')->nullable();
            $table->string('changed_by_user_id')->nullable();
            $table->string('changed_by_user_name')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address')->nullable();
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
        Schema::dropIfExists('loan_officer_change_audits');
    }
};
