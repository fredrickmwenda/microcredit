<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\CreditScoreRange;
use Modules\Client\Services\CreditScoreService;

class CreditScoreController extends Controller
{
   public function __construct(
        private CreditScoreService $service
    ) {}

    public function dashboard($id)
    {
        $client = Client::findOrFail($id);
       
        $creditScore = $client->creditScore;
        $history = $client->creditScoreHistory()->latest()->get();
        $ranges = CreditScoreRange::orderBy('sort_order')->get();

        return theme_view('client::credit-scores.dashboard', compact('client', 'creditScore', 'history', 'ranges'));
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'new_score' => 'required|integer|min:300|max:850',
            'status' => 'required|in:Pending,Confirmed,Rejected',
            'reason' => 'nullable|string',
        ]);

        $this->service->updateScore($client, $validated['new_score'], $validated['status'], $validated['reason']);

        return back()->with('success', 'Credit score updated.');
    }

}
