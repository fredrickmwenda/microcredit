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
        Schema::create('savings_officer_change_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('savings_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('savings_account_number')->nullable();
            $table->unsignedBigInteger('old_officer_id')->nullable();
            $table->unsignedBigInteger('new_officer_id')->nullable();
            $table->string('old_officer_name')->nullable();
            $table->string('new_officer_name')->nullable();
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
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
        Schema::dropIfExists('savings_officer_change_audits');
    }
};
