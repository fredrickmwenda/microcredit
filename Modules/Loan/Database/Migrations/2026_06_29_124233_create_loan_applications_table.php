<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_application_processes', function (Blueprint $table) {
            $table->bigIncrements('id');
                        
            // Raw application data (before client exists)
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('country_id');
            $table->string('ghana_card_number');
            $table->string('phone_number');
            $table->string('email');
            $table->text('residential_address');
            $table->string('digital_address')->nullable();
            $table->enum('employment_status', ['Employed', 'Self-Employed', 'Unemployed']);
            $table->string('employer_business_name')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('monthly_net_income', 12, 2)->nullable();
            $table->text('work_address')->nullable();
            $table->string('length_of_employment')->nullable();
            $table->decimal('loan_amount_requested', 12, 2);
            $table->text('purpose_of_loan');
            $table->enum('repayment_period', [4, 6, 12]);
            $table->enum('preferred_repayment_method', ['Bank', 'Mobile Money', 'Payroll', 'Post-Dated Cheque', 'Standing Order']);

            // Credit Scoring (Office Use)
            $table->integer('income_stability_score')->default(0);
            $table->integer('debt_to_income_score')->default(0);
            $table->integer('credit_history_score')->default(0);
            $table->integer('employment_length_score')->default(0);
            $table->integer('guarantor_strength_score')->default(0);
            $table->integer('total_score')->default(0);
            $table->enum('risk_rating', ['Low', 'Medium', 'High'])->nullable();

            // Approval Workflow
            $table->enum('level1_status', ['Pending', 'Approved', 'Declined'])->default('Pending');
            $table->decimal('recommended_amount', 12, 2)->nullable();
            $table->integer('loan_officer_id')->nullable();
            $table->timestamp('level1_decision_at')->nullable();

            $table->enum('level2_status', ['Pending', 'Approved', 'Declined', 'Deferred'])->default('Pending');
            $table->integer('manager_id')->nullable()->constrained('users');
            $table->timestamp('level2_decision_at')->nullable();

            // Links to created records (after approval)
            $table->integer('client_id')->nullable();
            $table->integer('loan_id')->nullable();

            $table->string('reference_number')->unique();
            $table->enum('overall_status', ['Submitted', 'Under Review', 'Approved', 'Declined', 'Converted'])->default('Submitted');
            $table->timestamp('submitted_at')->nullable();

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
        Schema::dropIfExists('loan_applications');
    }
}
