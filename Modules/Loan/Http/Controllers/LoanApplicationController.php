<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Client\Entities\Client;
use Modules\Core\Entities\Country;
use Modules\Loan\Entities\LoanApplicationProcess;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Services\LoanApplicationService;

class LoanApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function __construct(
        private LoanApplicationService $service
    ) {}


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $countries = Country::all();
        $clients = Client::select('id', 'first_name', 'middle_name', 'last_name', 'gender', 'dob', 'mobile', 'phone', 'email', 'address', 'country_id', 'account_number','external_id')
            ->orderBy('first_name')
            ->get();
        
        // dd($clients->first()->getAttributes());
        $loan_products = LoanProduct::where('active', 1)->orderBy('name')->get();

        return theme_view('loan::applications.create', compact('countries', 'clients', 'loan_products'));
    }


    public function store(Request $request)
    {
        $clientMode = $request->input('client_mode', 'new');

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date|before:today',
            'gender' => 'required|in:Male,Female',
            'country_id' => 'required|string|max:100',
            'ghana_card_number' => 'required|string|unique:loan_application_processes',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'residential_address' => 'required|string',
            'digital_address' => 'nullable|string|max:100',
            'employment_status' => 'required|in:Employed,Self-Employed,Unemployed',
            'employer_business_name' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'monthly_net_income' => 'nullable|numeric|min:0',
            'work_address' => 'nullable|string',
            'length_of_employment' => 'nullable|string|max:100',
            'loan_amount_requested' => 'required|numeric|min:1',
            'loan_product_id' => 'required|exists:loan_products,id',
            'repayment_period' => 'required|in:4,6,12',
            'preferred_repayment_method' => 'required|in:Bank,Mobile Money,Payroll,Post-Dated Cheque,Standing Order',
        ];

        if ($clientMode === 'existing') {
            $rules['client_id'] = 'required|exists:clients,id';
            $rules['first_name'] = 'nullable|string|max:255';
            $rules['last_name'] = 'nullable|string|max:255';
            $rules['dob'] = 'nullable|date|before:today';
            $rules['gender'] = 'nullable|in:Male,Female';
            $rules['country_id'] = 'nullable|string|max:100';
            $rules['ghana_card_number'] = 'nullable|string|unique:loan_application_processes';
            $rules['phone_number'] = 'nullable|string|max:20';
            $rules['email'] = 'nullable|email|max:255';
            $rules['residential_address'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        if ($clientMode === 'existing' && $request->filled('client_id')) {
            $client = Client::find($request->input('client_id'));

            if ($client) {
                $validated['client_id'] = $client->id;
                $validated['first_name'] = $client->first_name;
                $validated['last_name'] = $client->last_name;
                $validated['gender'] = $this->normalizeGender($client->gender);
                $validated['dob'] = $client->dob;
                $validated['country_id'] = $client->country_id;
                $validated['ghana_card_number'] = $client->external_id;
                $validated['phone_number'] = $client->mobile ?: $client->phone ?: $validated['phone_number'];
                $validated['email'] = $client->email ?: $validated['email'];
                $validated['residential_address'] = $client->address ?: $validated['residential_address'];
            }
        }

        $validated['overall_status'] = 'Submitted';
        $validated['submitted_at'] = now();

        $application = LoanApplicationProcess::create($validated);

        return redirect()->route('loan.applications.show', $application)
            ->with('success', 'Application submitted! Reference: ' . $application->reference_number);
    }

    private function normalizeGender($value): string
    {
        if (empty($value)) {
            return 'Male';
        }

        $normalized = strtolower($value);

        return in_array($normalized, ['male', 'female']) ? ucfirst($normalized) : 'Male';
    }

    public function show($id)
{
    $application = LoanApplicationProcess::with(['client', 'loan', 'loanOfficer', 'manager'])->findOrFail($id);
    
    
    return theme_view('loan::applications.show', compact('application'));
}


    public function index()
    {
        $applications = LoanApplicationProcess::with(['client', 'loan'])->latest()->paginate(20);
        return theme_view('loan::applications.index', compact('applications'));
    }


     public function level1Review(Request $request, $id)
    {
        $application = LoanApplicationProcess::findOrFail($id);

        $validated = $request->validate([
            'income_stability_score' => 'required|integer|min:0|max:100',
            'debt_to_income_score' => 'required|integer|min:0|max:100',
            'credit_history_score' => 'required|integer|min:0|max:100',
            'employment_length_score' => 'required|integer|min:0|max:100',
            'guarantor_strength_score' => 'required|integer|min:0|max:100',
            'level1_status' => 'required|in:Approved,Declined',
            'recommended_amount' => 'required_if:level1_status,Approved|numeric|min:0',
            'level1_notes' => 'nullable|string|max:1000',
        ]);

        $totalScore = array_sum([
            $validated['income_stability_score'],
            $validated['debt_to_income_score'],
            $validated['credit_history_score'],
            $validated['employment_length_score'],
            $validated['guarantor_strength_score'],
        ]);

        if ($validated['level1_status'] === 'Declined') {
            $this->service->declineLevel1($application, $request->input('rejected_notes'));
            return back()->with('success', 'Application declined at Level 1.');
        }

        $application->update([
            'income_stability_score' => $validated['income_stability_score'],
            'debt_to_income_score' => $validated['debt_to_income_score'],
            'credit_history_score' => $validated['credit_history_score'],
            'employment_length_score' => $validated['employment_length_score'],
            'guarantor_strength_score' => $validated['guarantor_strength_score'],
            'total_score' => $totalScore,
            'risk_rating' => $totalScore >= 400 ? 'Low' : ($totalScore >= 250 ? 'Medium' : 'High'),
            'level1_status' => 'Approved',
            'recommended_amount' => $validated['recommended_amount'],
            'loan_officer_id' => auth()->id(),
            'level1_decision_at' => now(),
            'overall_status' => 'Under Review',
            'level1_notes' => $validated['level1_notes'] ?? null,
        ]);

        return back()->with('success', 'Level 1 review completed.');
    }

    public function level2Approve(Request $request, $id)
    {
        $application = LoanApplicationProcess::findOrFail($id);

        $validated = $request->validate([
            'level2_status' => 'required|in:Approved,Declined,Deferred',
            'approved_amount' => 'required_if:level2_status,Approved|numeric|min:0',
        ]);

        if ($validated['level2_status'] === 'Declined') {
            $this->service->declineLevel2($application, $request->input('reason'));
            return back()->with('success', 'Application declined at Level 2.');
        }

        if ($validated['level2_status'] === 'Deferred') {
            $this->service->deferLevel2($application, $request->input('reason'));
            return back()->with('success', 'Application deferred.');
        }

        // Approved
        $result = $this->service->approveApplication($application, [
            'approved_amount' => $validated['approved_amount'],
        ]);

        //redirect back to using loan/application/{id}/view  
        return redirect('loan/application/' . $application->id . '/view')
            ->with('success', 'Application approved! Client, Loan, and Credit Score created.');

        // return redirect()->url('loan/application/' . $application->id . '/credit_score')
        //     ->with('success', 'Application approved! Client, Loan, and Credit Score created.');
    }


}
