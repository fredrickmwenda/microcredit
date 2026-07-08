<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditScoreRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_score_ranges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('min_score');
            $table->integer('max_score');
            $table->string('rating_label'); // e.g., "Excellent", "Good", "Fair", "Poor"
            $table->string('color_code')->default('#22c55e'); // hex color for gauge
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
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
        Schema::dropIfExists('credit_score_ranges');
    }
}
