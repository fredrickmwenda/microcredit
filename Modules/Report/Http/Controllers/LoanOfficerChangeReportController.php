<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Loan\Entities\LoanOfficerChangeAudit;

class LoanOfficerChangeReportController extends Controller
{
    /**
     * LoanOfficerChangeReportController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:loan.officer.change.report.index'])->only(['index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $query = LoanOfficerChangeAudit::with('loan', 'old_officer', 'new_officer', 'changed_by_user')
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter by old officer
        if ($request->has('old_officer_id') && $request->old_officer_id) {
            $query->where('old_officer_id', $request->old_officer_id);
        }

        // Filter by new officer
        if ($request->has('new_officer_id') && $request->new_officer_id) {
            $query->where('new_officer_id', $request->new_officer_id);
        }

        // Filter by changed by user
        if ($request->has('changed_by_user_id') && $request->changed_by_user_id) {
            $query->where('changed_by_user_id', $request->changed_by_user_id);
        }

        // Paginate results
        $audits = $query->paginate(50);

        // Get users for dropdown filters
        $officers = \Modules\User\Entities\User::orderBy('first_name')->get();
        $changedByUsers = \Modules\User\Entities\User::orderBy('first_name')->get();

        return theme_view('report::loan_officer_change_report.index', compact('audits', 'officers', 'changedByUsers'));
    }
}
