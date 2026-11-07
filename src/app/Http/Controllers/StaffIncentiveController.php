<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffIncentive;
use App\Services\AuditService;
use App\Services\StaffIncentiveAccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StaffIncentiveController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected StaffIncentiveAccountingService $incentiveAccounting,
    ) {}

    public function index(Request $request)
    {
        $query = StaffIncentive::with('staff.user', 'staff.branch');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('incentive_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('incentive_date', '<=', $request->to);
        }

        $totals = [
            'total' => (clone $query)->sum('amount'),
            'paid' => (clone $query)->where('status', StaffIncentive::STATUS_PAID)->sum('amount'),
            'pending' => (clone $query)->where('status', StaffIncentive::STATUS_PENDING)->sum('amount'),
        ];

        $incentives = $query->latest('incentive_date')->latest('id')->paginate(15);
        $staff = $this->staffOptions();

        return view('staff-incentives.index', compact('incentives', 'staff', 'totals'));
    }

    public function create()
    {
        $staff = $this->staffOptions();
        $paidFromOptions = StaffIncentive::PAID_FROM;

        return view('staff-incentives.create', compact('staff', 'paidFromOptions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateIncentive($request);
        $validated['created_by'] = auth()->id();
        $validated['paid_from'] = $validated['paid_from'] ?? 'cash';

        $incentive = DB::transaction(function () use ($validated) {
            $incentive = StaffIncentive::create($validated);
            $this->incentiveAccounting->sync($incentive);

            return $incentive;
        });

        $this->auditService->logCreate($incentive);

        return redirect('/staff-incentives')->with('success', 'Staff incentive created successfully.');
    }

    public function show($id)
    {
        $incentive = StaffIncentive::with('staff.user', 'staff.branch', 'createdBy', 'journalEntry.lines.account')->findOrFail($id);

        return view('staff-incentives.show', compact('incentive'));
    }

    public function edit($id)
    {
        $incentive = StaffIncentive::findOrFail($id);
        $staff = $this->staffOptions();
        $paidFromOptions = StaffIncentive::PAID_FROM;

        return view('staff-incentives.edit', compact('incentive', 'staff', 'paidFromOptions'));
    }

    public function update(Request $request, $id)
    {
        $incentive = StaffIncentive::findOrFail($id);
        $validated = $this->validateIncentive($request);

        DB::transaction(function () use ($incentive, $validated) {
            $old = $incentive->toArray();
            $incentive->update($validated);
            $this->incentiveAccounting->sync($incentive);
            $this->auditService->logUpdate($incentive, $old);
        });

        return redirect('/staff-incentives')->with('success', 'Staff incentive updated successfully.');
    }

    public function destroy($id)
    {
        $incentive = StaffIncentive::findOrFail($id);

        DB::transaction(function () use ($incentive) {
            $this->incentiveAccounting->remove($incentive);
            $this->auditService->logDelete($incentive);
            $incentive->delete();
        });

        return redirect('/staff-incentives')->with('success', 'Staff incentive deleted successfully.');
    }

    private function validateIncentive(Request $request): array
    {
        return $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'paid_from' => ['nullable', Rule::in(array_keys(StaffIncentive::PAID_FROM))],
            'incentive_date' => 'required|date',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);
    }

    private function staffOptions()
    {
        return Staff::with('user', 'branch')->orderBy('employee_id')->get();
    }
}
