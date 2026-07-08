<?php

namespace Modules\Savings\Observers;

use Modules\Savings\Entities\Savings;
use Modules\Savings\Entities\SavingsOfficerChangeAudit;

class SavingsOfficerChangeObserver
{
    /**
     * Handle the Savings "updated" event.
     *
     * @param  \Modules\Savings\Entities\Savings  $savings
     * @return void
     */
    public function updated(Savings $savings)
    {
        // Check if savings_officer_id was changed
        if ($savings->isDirty('savings_officer_id')) {
            $oldOfficerId = $savings->getOriginal('savings_officer_id');
            $newOfficerId = $savings->getAttribute('savings_officer_id');

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
                SavingsOfficerChangeAudit::create([
                    'savings_id' => $savings->id,
                    'client_id' => $savings->client_id ?? null,
                    'savings_account_number' => $savings->savings_account_number ?? null,
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
