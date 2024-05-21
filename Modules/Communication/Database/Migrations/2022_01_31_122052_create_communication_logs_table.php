<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunicationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('sms_gateway_id')->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->enum('type', ['sms', 'email'])->default('sms');
            $table->text('sms_type')->nullable();
            $table->text('text_body')->nullable();
            $table->text('send_to')->nullable();
            $table->text('campaign_name')->nullable();
            $table->text('response')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
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
        Schema::dropIfExists('communication_logs');
    }
}
