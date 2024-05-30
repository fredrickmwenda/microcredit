<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateStatusEnumInLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Raw SQL for updating the enum values
        DB::statement("ALTER TABLE loans MODIFY COLUMN status ENUM('pending', 'approved', 'active', 'withdrawn', 'rejected', 'closed', 'rescheduled', 'written_off', 'overpaid', 'submitted', 'pending_ceo_approval') NOT NULL");
    }

    public function down()
    {
        // Revert to the original enum values if needed
        DB::statement("ALTER TABLE loans MODIFY COLUMN status ENUM('pending', 'approved', 'active', 'withdrawn', 'rejected', 'closed', 'rescheduled', 'written_off', 'overpaid', 'submitted') NOT NULL");
    }
}
