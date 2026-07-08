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
        Schema::create('bulk_savings_entry_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('bulk_savings_entry_id');
            $table->integer('savings_id');
            $table->integer('client_id');
            $table->enum('transaction_type', ['deposit', 'withdrawal']);
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->integer('savings_transaction_id')->nullable()->comment('Link to created savings transaction');
            $table->timestamps();
            
            // $table->foreign('bulk_savings_entry_id')->references('id')->on('bulk_savings_entries')->onDelete('cascade');
            // $table->foreign('savings_id')->references('id')->on('savings');
            // $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bulk_savings_entry_items');
    }
};
