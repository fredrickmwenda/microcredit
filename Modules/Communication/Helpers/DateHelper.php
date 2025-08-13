<!-- Dates Validation Helper - For a check of in the future and for check of 18 years- $20 -->
<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Validate date is not in the future and age is at least 18.
     *
     * @param  string|null $date
     * @return bool
     */
    public static function isValidAdultDate(?string $date): bool
    {
        if (!$date) {
            return false; // No date provided
        }

        try {
            $dob = Carbon::parse($date);
        } catch (\Exception $e) {
            return false; // Invalid format
        }

        $today = Carbon::today();

        // Check not future
        if ($dob->greaterThan($today)) {
            return false;
        }

        // Check at least 18 years old
        if ($dob->diffInYears($today) < 18) {
            return false;
        }

        return true;
    }
}


public function store(Request $request)
{
    if (!DateHelper::isValidAdultDate($request->birth_date)) {
        return back()->withErrors(['birth_date' => 'Date must not be in the future and must indicate 18+ age.'])
                     ->withInput();
    }

    // Continue saving...
}


$request->validate([
    'birth_date' => [
        'required',
        'date',
        'before_or_equal:today',
        'before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
    ]
], [
    'birth_date.before_or_equal' => 'Date must not be in the future and must indicate 18+ age.'
]);