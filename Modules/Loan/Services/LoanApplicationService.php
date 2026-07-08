<?php

namespace Modules\Loan\Services;

use Illuminate\Support\Facades\DB;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\CreditScore;
use Modules\Client\Entities\CreditScoreHistory;
use Modules\Client\Entities\CreditScoreRange;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanApplicationProcess;

class LoanApplicationService
{
    public function approveApplication(LoanApplicationProcess $application, array $data): array
    {
        return DB::transaction(function () use ($application, $data) {
            if ($application->client_id) {
                $client = Client::find($application->client_id);
                if (!$client) {
                    throw new \Exception('Existing client not found: ' . $application->client_id);
                }
            } else {
                $client = $this->createClientFromApplication($application);
                $this->createClientIdentificationFiles($client, $application);
            }

            $loan = $this->createLoanFromApplication($application, $client, $data['approved_amount']);
            $creditScore = $this->createInitialCreditScore($application, $client);

                        // Debug: log the IDs before update
            \Log::info('Before update', [
                'client_id' => $client->id,
                'loan_id' => $loan->id,
                'application_id' => $application->id,
            ]);

            $updated = $application->update([
                'client_id' => $client->id,
                'loan_id' => $loan->id,
                'level2_status' => 'Approved',
                'approved_amount' => $data['approved_amount'],
                'manager_id' => auth()->id(),
                'level2_decision_at' => now(),
                'overall_status' => 'Converted',
            ]);

                        // Debug: log if update succeeded
            \Log::info('After update', [
                'updated' => $updated,
                'application_client_id' => $application->fresh()->client_id,
                'application_loan_id' => $application->fresh()->loan_id,
            ]);

            return [
                'client' => $client,
                'loan' => $loan,
                'credit_score' => $creditScore,
            ];
        });
    }

    public function declineLevel1(LoanApplicationProcess $application, ?string $reason = null): void
    {
        $application->update([
            'level1_status' => 'Declined',
            'loan_officer_id' => auth()->id(),
            'level1_decision_at' => now(),
            'overall_status' => 'Declined',
            'level1_notes' => $reason,
        ]);
    }

    public function declineLevel2(LoanApplicationProcess $application, ?string $reason = null): void
    {
        $application->update([
            'level2_status' => 'Declined',
            'manager_id' => auth()->id(),
            'level2_decision_at' => now(),
            'overall_status' => 'Declined',
            'level2_notes' => $reason,
        ]);
    }

    public function deferLevel2(LoanApplicationProcess $application, ?string $reason = null): void
    {
        $application->update([
            'level2_status' => 'Deferred',
            'manager_id' => auth()->id(),
            'level2_decision_at' => now(),
            'overall_status' => 'Under Review',
        ]);
    }

    private function createClientFromApplication(LoanApplicationProcess $application): Client
    {
        $clientId = DB::table('clients')->insertGetId([
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'created_by_id' => $application->loan_officer_id,
            'dob' => $application->dob,
            'gender' => $application->gender,
            'country_id' => $application->country_id,
            'phone' => $application->phone_number,
            'mobile' => $application->phone_number,
            'email' => $application->email,
            'address' => $application->residential_address,
            'loan_officer_id' => $application->loan_officer_id,
            'employer' => $application->employer_business_name,
            'loan_application_process_id' => $application->id,
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $client = Client::find($clientId);

        if (!$client) {
            throw new \Exception('Failed to create client. insertGetId returned: ' . $clientId);
        }

        return $client;
    }

    private function createClientIdentificationFiles(Client $client, LoanApplicationProcess $application): void
    {
        $exists = DB::table('client_identification')
            ->where('client_id', $client->id)
            ->where('client_identification_type_id', 1)
            ->where('identification_value', $application->ghana_card_number)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('client_identification')->insert([
            'created_by_id' => $application->loan_officer_id,
            'client_id' => $client->id,
            'client_identification_type_id' => 1,
            'identification_value' => $application->ghana_card_number,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createLoanFromApplication(LoanApplicationProcess $application, Client $client, float $approvedAmount): Loan
    {
        $loanId = DB::table('loans')->insertGetId([
            'loan_pre_application_id' => $application->id,
            'reference' => $application->reference_number,
            'client_id' => $client->id,
            'created_by_id' => $application->loan_officer_id,
            'client_type' => 'client',
            'applied_amount' => $application->loan_amount_requested,
            'approved_amount' => $approvedAmount,
            'loan_purpose_id' => 3,
            'loan_product_id' => $application->loan_product_id,
            'currency_id' => 1,
            'repayment_frequency' => $application->repayment_period,
            'repayment_frequency_type' => 'months',
            'loan_term' => $application->repayment_period,
            'principal_disbursed_derived' => $approvedAmount,
            'principal' => $approvedAmount,
            'interest_rate' => $this->calculateInterestRate($application->risk_rating),
            'interest_rate_type' => 'month',
            'interest_methodology' => 'flat',
            'interest_recalculation' => $this->calculateInterestRate($application->risk_rating) > 20 ? 'monthly' : 'daily',
            'amortization_method' => 'equal_installments',
            'status' => 'approved',
            'loan_officer_id' => $application->loan_officer_id,
            'submitted_on_date' => $application->created_at,
            'approved_on_date' => now(),
            'approved_by_user_id' => $application->manager_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $loan = Loan::find($loanId);

        if (!$loan) {
            throw new \Exception('Failed to create loan. insertGetId returned: ' . $loanId);
        }

        return $loan;
    }

    private function createInitialCreditScore(LoanApplicationProcess $application, Client $client): CreditScore
    {
        $score = $application->total_score;
        $score = max(0, min(500, $score));

        $range = CreditScoreRange::getRatingForScore($score);

        $creditScoreId = DB::table('credit_scores')->insertGetId([
            'client_id' => $client->id,
            'score' => $score,
            'range_id' => $range?->id,
            'rating_label' => $range?->rating_label ?? 'Unknown',
            'assessed_at' => now(),
            'notes' => 'Initial assessment from loan application ' . $application->reference_number,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('credit_score_histories')->insert([
            'client_id' => $client->id,
            'previous_score' => 0,
            'new_score' => $score,
            'status' => 'Confirmed',
            'change_date' => now(),
            'reason' => 'Loan approval and initial credit assessment',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $raw = DB::table('credit_scores')->where('id', $creditScoreId)->first();
        $creditScore = new CreditScore;
        $creditScore->setRawAttributes((array) $raw, true);
        $creditScore->exists = true;

        return $creditScore;
    }

    private function calculateInterestRate(?string $riskRating): float
    {
        return match($riskRating) {
            'Low' => 12.00,
            'Medium' => 18.00,
            'High' => 25.00,
            default => 20.00,
        };
    }
}