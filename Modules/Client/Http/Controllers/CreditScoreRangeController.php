<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Client\Entities\CreditScoreRange;

class CreditScoreRangeController extends Controller
{
    public function index()
    {
        $ranges = CreditScoreRange::orderBy('sort_order')->get();
        return theme_view('client::credit-score-ranges.index', compact('ranges'));
    }

    public function get_credit_score_ranges(Request $request)
    {
        $query = CreditScoreRange::query();

        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('rating_label', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $totalRecords = $query->count();
        $ranges = $query->orderBy('sort_order')->get();

        return response()->json([
            'draw' => intval($request->draw ?? 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $ranges,
        ]);
    }

    public function create()
    {
        return theme_view('client::credit-score-ranges.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'min_score' => 'required|integer|min:0|max:1000',
            'max_score' => 'required|integer|gte:min_score',
            'rating_label' => 'required|string|max:100',
            'color_code' => 'required|string|max:7',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
        ]);

        CreditScoreRange::create($validated);

        return redirect()->route('client.credit_score_range.index')->with('success', 'Credit score range added successfully.');
    }

    public function show($id)
    {
        $range = CreditScoreRange::findOrFail($id);
        return theme_view('client::credit-score-ranges.show', compact('range'));
    }

    public function edit($id)
    {
        $range = CreditScoreRange::findOrFail($id);
        return theme_view('client::credit-score-ranges.edit', compact('range'));
    }

    public function update(Request $request, $id)
    {
        $range = CreditScoreRange::findOrFail($id);

        $validated = $request->validate([
            'min_score' => 'required|integer|min:0|max:1000',
            'max_score' => 'required|integer|gte:min_score',
            'rating_label' => 'required|string|max:100',
            'color_code' => 'required|string|max:7',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
        ]);

        $range->update($validated);

        return redirect()->route('client.credit_score_range.index')->with('success', 'Credit score range updated successfully.');
    }

    public function destroy($id)
    {
        $range = CreditScoreRange::findOrFail($id);
        $range->delete();

        return redirect()->route('client.credit_score_range.index')->with('success', 'Credit score range deleted successfully.');
    }
}