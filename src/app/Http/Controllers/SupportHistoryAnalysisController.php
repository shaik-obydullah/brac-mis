<?php

namespace App\Http\Controllers;

use App\Models\ImmediateSupport;
use Illuminate\Http\Request;

class SupportHistoryAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $categories = ImmediateSupport::CATEGORIES;
        $statuses = ImmediateSupport::STATUSES;

        $selectedCategory = $request->filled('category') && array_key_exists($request->category, $categories)
            ? $request->category
            : null;

        $selectedType = $request->filled('type') && array_key_exists($request->type, ImmediateSupport::TYPES)
            ? $request->type
            : null;

        $selectedStatus = $request->filled('status') && array_key_exists($request->status, $statuses)
            ? $request->status
            : null;

        if ($selectedCategory) {
            $categoryTypes = $categories[$selectedCategory]['types'];

            if ($selectedType && ! in_array($selectedType, $categoryTypes, true)) {
                $selectedType = null;
            }
        }

        $base = ImmediateSupport::query();
        $this->applyDateFilters($base, $request);

        if ($selectedStatus) {
            $base->where('status', $selectedStatus);
        }

        $categoryScope = clone $base;

        if ($selectedCategory) {
            $categoryScope->whereIn('support_type', $categories[$selectedCategory]['types']);
        }

        $listScope = clone $categoryScope;

        if ($selectedType) {
            $listScope->where('support_type', $selectedType);
        }

        $totals = [
            'records' => (clone $listScope)->count(),
            'amount' => (float) (clone $listScope)->sum('amount'),
            'provided' => (clone $listScope)->where('status', 'provided')->count(),
            'pending' => (clone $listScope)->where('status', 'pending')->count(),
            'cancelled' => (clone $listScope)->where('status', 'cancelled')->count(),
        ];

        $categorySummary = collect($categories)->map(function ($config, $key) use ($base) {
            $rows = (clone $base)
                ->whereIn('support_type', $config['types'])
                ->selectRaw('status, COUNT(*) as total, COALESCE(SUM(amount), 0) as amount')
                ->groupBy('status')
                ->get();

            return [
                'label' => $config['label'],
                'records' => (int) $rows->sum('total'),
                'amount' => (float) $rows->sum('amount'),
                'provided' => (int) $rows->where('status', 'provided')->sum('total'),
                'pending' => (int) $rows->where('status', 'pending')->sum('total'),
            ];
        });

        $typeRows = (clone $categoryScope)
            ->selectRaw('support_type, COUNT(*) as total, COALESCE(SUM(amount), 0) as amount')
            ->groupBy('support_type')
            ->get()
            ->keyBy('support_type');

        $typeValues = $selectedCategory ? $categories[$selectedCategory]['types'] : array_keys(ImmediateSupport::TYPES);
        $typeOptions = array_intersect_key(ImmediateSupport::TYPES, array_flip($typeValues));

        $statusRows = (clone $listScope)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthlyRows = (clone $base)
            ->whereDate('support_date', '>=', now()->subMonths(11)->startOfMonth()->toDateString())
            ->selectRaw('YEAR(support_date) as year, MONTH(support_date) as month, support_type, COUNT(*) as total')
            ->groupBy('year', 'month', 'support_type')
            ->get();

        $months = collect(range(11, 0))->map(function ($monthsAgo) use ($monthlyRows, $categories) {
            $date = now()->subMonths($monthsAgo);
            $rows = $monthlyRows->where('year', (int) $date->year)->where('month', (int) $date->month);

            $counts = collect($categories)->mapWithKeys(function ($config, $key) use ($rows) {
                return [$key => (int) $rows->whereIn('support_type', $config['types'])->sum('total')];
            });

            return [
                'label' => $date->format('M Y'),
                'counts' => $counts,
                'total' => $counts->sum(),
            ];
        });

        $yearlyHistory = (clone $base)
            ->selectRaw('YEAR(support_date) as year, COUNT(*) as total, COALESCE(SUM(amount), 0) as amount')
            ->groupBy('year')
            ->orderByDesc('year')
            ->get();

        $supports = (clone $listScope)
            ->with('user', 'providedBy')
            ->latest('support_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('support-history.index', compact(
            'categories',
            'statuses',
            'categorySummary',
            'typeOptions',
            'typeRows',
            'statusRows',
            'months',
            'yearlyHistory',
            'totals',
            'supports',
            'selectedCategory',
            'selectedType',
            'selectedStatus',
        ));
    }

    private function applyDateFilters($query, Request $request): void
    {
        if ($request->filled('from')) {
            $query->whereDate('support_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('support_date', '<=', $request->to);
        }
    }
}
