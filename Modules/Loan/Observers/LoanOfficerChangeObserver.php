<?php

namespace Modules\Loan\Observers;

use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanOfficerChangeAudit;

class LoanOfficerChangeObserver
{
    /**
     * Handle the Loan "updated" event.
     *
     * @param  \Modules\Loan\Entities\Loan  $loan
     * @return void
     */
    public function updated(Loan $loan)
    {
        // Check if loan_officer_id was changed
        if ($loan->isDirty('loan_officer_id')) {
            $oldOfficerId = $loan->getOriginal('loan_officer_id');
            $newOfficerId = $loan->getAttribute('loan_officer_id');

            // Only record if there's actually a change
            if ($oldOfficerId !== $newOfficerId) {
                $oldOfficer = null;
                $newOfficer = null;
                $oldOfficerName = 'N/A';
                $newOfficerName = 'N/A';

                if ($oldOfficerId) {
                    $oldOfficer = \Modules\User\Entities\User::find($oldOfficerId);
                    $oldOfficerName = $oldOfficer ? $oldOfficer->first_name . ' ' . $oldOfficer->last_name : 'N/A';
                }

                if ($newOfficerId) {
                    $newOfficer = \Modules\User\Entities\User::find($newOfficerId);
                    $newOfficerName = $newOfficer ? $newOfficer->first_name . ' ' . $newOfficer->last_name : 'N/A';
                }

                // Get current authenticated user
                $user = auth()->user();
                $changedByUserId = $user ? $user->id : null;
                $changedByUserName = $user ? $user->first_name . ' ' . $user->last_name : 'System';

                // Create audit record
                LoanOfficerChangeAudit::create([
                    'loan_id' => $loan->id,
                    'client_id' => $loan->client_id ?? null,
                    'loan_account_number' => $loan->loan_account_number ?? null,
                    'old_officer_id' => $oldOfficerId,
                    'new_officer_id' => $newOfficerId,
                    'old_officer_name' => $oldOfficerName,
                    'new_officer_name' => $newOfficerName,
                    'changed_by_user_id' => $changedByUserId,
                    'changed_by_user_name' => $changedByUserName,
                    'ip_address' => request()->ip(),
                ]);
            }
        }
    }
}
