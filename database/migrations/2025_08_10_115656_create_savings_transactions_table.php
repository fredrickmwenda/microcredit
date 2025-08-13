<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavingsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->id();

            
            // Foreign key linking to savings account
            $table->unsignedBigInteger('savings_id');

            // Type of transaction: deposit, withdrawal, penalty, fee, etc.
            $table->string('transaction_type');

            // Transaction date
            $table->date('date');

            // For easy filtering in reports
            $table->string('year', 4);
            $table->string('month', 2);

            // Amount fields (either debit or credit)
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);

            // Whether this transaction can be reversed
            $table->boolean('reversible')->default(true);

            $table->timestamps();

            // Foreign key constraint (assuming a savings table exists)
            $table->foreign('savings_id')
                ->references('id')
                ->on('savings')
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
        Schema::dropIfExists('savings_transactions');
    }
}
