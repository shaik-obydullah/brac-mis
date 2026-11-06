<?php

namespace App\Http\Controllers;

use App\Models\ImmediateSupport;
use App\Models\SupportFollowUp;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupportFollowUpController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $totals = [
            'records' => (clone $query)->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'scheduled' => (clone $query)->where('status', 'scheduled')->count(),
            'missed' => (clone $query)->where('status', 'missed')->count(),
            'due' => (clone $query)->where('status', 'scheduled')
                ->whereDate('follow_up_date', '<=', now()->toDateString())
                ->count(),
        ];

        $followUps = $query->with('support.beneficiary', 'support.returnee.beneficiary', 'followedBy')
            ->latest('follow_up_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('support-follow-ups.index', [
            'followUps' => $followUps,
            'totals' => $totals,
            'categories' => ImmediateSupport::CATEGORIES,
            'methods' => SupportFollowUp::METHODS,
            'statuses' => SupportFollowUp::STATUSES,
        ]);
    }

    public function create(Request $request)
    {
        $support = null;

        if ($request->filled('support_id')) {
            $support = ImmediateSupport::with('beneficiary:id,name,brac_id', 'returnee.beneficiary:id,name,brac_id')
                ->find($request->integer('support_id'));
        }

        $followUp = new SupportFollowUp([
            'immediate_support_id' => $support?->id,
            'follow_up_date' => now()->toDateString(),
            'method' => 'phone_call',
            'status' => 'scheduled',
        ]);

        return view('support-follow-ups.create', $this->formData($followUp, $support));
    }

    public function store(Request $request)
    {
        $validated = $this->validateFollowUp($request);
        $validated['followed_by'] = auth()->id();

        $followUp = SupportFollowUp::create($validated);
        $this->auditService->logCreate($followUp);

        return redirect()->route('support.show', [
            'category' => $this->categoryForSupport($followUp->support),
            'immediateSupport' => $followUp->immediate_support_id,
        ])->with('success', 'Follow-up recorded successfully.');
    }

    public function edit(SupportFollowUp $supportFollowUp)
    {
        $supportFollowUp->load('support.beneficiary', 'support.returnee.beneficiary');

        return view('support-follow-ups.edit', $this->formData($supportFollowUp, $supportFollowUp->support));
    }

    public function update(Request $request, SupportFollowUp $supportFollowUp)
    {
        $validated = $this->validateFollowUp($request);

        $old = $supportFollowUp->toArray();
        $supportFollowUp->update($validated);
        $this->auditService->logUpdate($supportFollowUp, $old);

        return redirect()->route('support.show', [
            'category' => $this->categoryForSupport($supportFollowUp->support),
            'immediateSupport' => $supportFollowUp->immediate_support_id,
        ])->with('success', 'Follow-up updated successfully.');
    }

    public function destroy(SupportFollowUp $supportFollowUp)
    {
        $category = $this->categoryForSupport($supportFollowUp->support);
        $supportId = $supportFollowUp->immediate_support_id;

        $this->auditService->logDelete($supportFollowUp);
        $supportFollowUp->delete();

        return redirect()->route('support.show', [
            'category' => $category,
            'immediateSupport' => $supportId,
        ])->with('success', 'Follow-up deleted successfully.');
    }

    private function filteredQuery(Request $request)
    {
        $query = SupportFollowUp::query();

        if ($request->filled('status') && array_key_exists($request->status, SupportFollowUp::STATUSES)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method') && array_key_exists($request->method, SupportFollowUp::METHODS)) {
            $query->where('method', $request->method);
        }

        if ($request->filled('category') && array_key_exists($request->category, ImmediateSupport::CATEGORIES)) {
            $query->whereHas('support', fn ($support) => $support
                ->whereIn('support_type', ImmediateSupport::CATEGORIES[$request->category]['types']));
        }

        if ($request->filled('from')) {
            $query->whereDate('follow_up_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('follow_up_date', '<=', $request->to);
        }

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('outcome', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('support', function ($support) use ($search) {
                        $support->where('recipient_name', 'like', "%{$search}%")
                            ->orWhere('recipient_phone', 'like', "%{$search}%")
                            ->orWhereHas('beneficiary', fn ($beneficiary) => $beneficiary
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('brac_id', 'like', "%{$search}%"))
                            ->orWhereHas('returnee.beneficiary', fn ($beneficiary) => $beneficiary
                                ->where('name', 'like', "%{$search}%"));
                    });
            });
        }

        return $query;
    }

    private function formData(SupportFollowUp $followUp, ?ImmediateSupport $support): array
    {
        return [
            'followUp' => $followUp,
            'support' => $support,
            'supports' => ImmediateSupport::with('beneficiary:id,name,brac_id', 'returnee.beneficiary:id,name,brac_id')
                ->latest('support_date')
                ->latest('id')
                ->get(),
            'methods' => SupportFollowUp::METHODS,
            'statuses' => SupportFollowUp::STATUSES,
        ];
    }

    private function validateFollowUp(Request $request): array
    {
        return $request->validate([
            'immediate_support_id' => 'required|exists:immediate_supports,id',
            'follow_up_date' => 'required|date',
            'method' => ['required', Rule::in(array_keys(SupportFollowUp::METHODS))],
            'status' => ['required', Rule::in(array_keys(SupportFollowUp::STATUSES))],
            'outcome' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'next_follow_up_date' => 'nullable|date|after_or_equal:follow_up_date',
        ]);
    }

    private function categoryForSupport(?ImmediateSupport $support): string
    {
        return $support ? ImmediateSupport::categoryForType($support->support_type) : 'immediate';
    }
}
