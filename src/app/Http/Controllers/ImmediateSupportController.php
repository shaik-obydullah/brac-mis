<?php

namespace App\Http\Controllers;

use App\Models\ImmediateSupport;
use App\Models\User;
use App\Services\AuditService;
use App\Services\SupportAccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ImmediateSupportController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected SupportAccountingService $supportAccounting,
    ) {}

    public function index(Request $request, string $category)
    {
        $typeValues = ImmediateSupport::categoryTypes($category);
        $types = array_intersect_key(ImmediateSupport::TYPES, array_flip($typeValues));

        $query = ImmediateSupport::with('user', 'providedBy')
            ->whereIn('support_type', $typeValues);

        if (count($typeValues) > 1 && $request->filled('type') && in_array($request->type, $typeValues, true)) {
            $query->where('support_type', $request->type);
        }

        if ($request->filled('status') && array_key_exists($request->status, ImmediateSupport::STATUSES)) {
            $query->where('status', $request->status);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('support_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('support_date', '<=', $request->to);
        }

        $supports = $query->latest('support_date')->latest('id')->paginate(15)->withQueryString();

        $typeCounts = ImmediateSupport::whereIn('support_type', $typeValues)
            ->select('support_type', DB::raw('count(*) as total'))
            ->groupBy('support_type')
            ->pluck('total', 'support_type');

        $statuses = ImmediateSupport::STATUSES;
        $categoryLabel = ImmediateSupport::CATEGORIES[$category]['label'];

        return view('immediate-supports.index', compact(
            'supports', 'typeCounts', 'types', 'statuses', 'category', 'categoryLabel',
        ));
    }

    public function create(Request $request, string $category)
    {
        $types = ImmediateSupport::categoryTypes($category);

        $type = $request->filled('type') && in_array($request->type, $types, true)
            ? $request->type
            : $types[0];

        $support = new ImmediateSupport([
            'support_type' => $type,
            'support_date' => now()->toDateString(),
            'paid_from' => 'cash',
            'status' => 'provided',
        ]);

        return view('immediate-supports.create', array_merge(
            [
                'support' => $support,
                'category' => $category,
                'categoryLabel' => ImmediateSupport::CATEGORIES[$category]['label'],
            ],
            $this->formData($types),
        ));
    }

    public function store(Request $request, string $category)
    {
        $types = ImmediateSupport::categoryTypes($category);
        $validated = $this->validateSupport($request, $types);
        $validated['provided_by'] = auth()->id();
        $validated['paid_from'] = $validated['paid_from'] ?? 'cash';

        $support = DB::transaction(function () use ($validated) {
            $support = ImmediateSupport::create($validated);
            $this->supportAccounting->sync($support);

            return $support;
        });

        $this->auditService->logCreate($support);

        return redirect()->route('support.show', ['category' => $category, 'immediateSupport' => $support])
            ->with('success', 'Support recorded successfully.');
    }

    public function show(string $category, ImmediateSupport $immediateSupport)
    {
        $this->ensureBelongsToCategory($immediateSupport, $category);
        $immediateSupport->load('user', 'providedBy', 'followUps.followedBy', 'journalEntry.lines.account');

        return view('immediate-supports.show', [
            'support' => $immediateSupport,
            'category' => $category,
            'categoryLabel' => ImmediateSupport::CATEGORIES[$category]['label'],
        ]);
    }

    public function edit(string $category, ImmediateSupport $immediateSupport)
    {
        $this->ensureBelongsToCategory($immediateSupport, $category);
        $types = ImmediateSupport::categoryTypes($category);

        return view('immediate-supports.edit', array_merge(
            [
                'support' => $immediateSupport,
                'category' => $category,
                'categoryLabel' => ImmediateSupport::CATEGORIES[$category]['label'],
            ],
            $this->formData($types),
        ));
    }

    public function update(Request $request, string $category, ImmediateSupport $immediateSupport)
    {
        $this->ensureBelongsToCategory($immediateSupport, $category);
        $types = ImmediateSupport::categoryTypes($category);
        $validated = $this->validateSupport($request, $types);

        DB::transaction(function () use ($validated, $immediateSupport) {
            $old = $immediateSupport->toArray();
            $immediateSupport->update($validated);
            $this->supportAccounting->sync($immediateSupport);
            $this->auditService->logUpdate($immediateSupport, $old);
        });

        return redirect()->route('support.show', ['category' => $category, 'immediateSupport' => $immediateSupport])
            ->with('success', 'Support updated successfully.');
    }

    public function destroy(string $category, ImmediateSupport $immediateSupport)
    {
        $this->ensureBelongsToCategory($immediateSupport, $category);

        DB::transaction(function () use ($immediateSupport) {
            $this->supportAccounting->remove($immediateSupport);
            $this->auditService->logDelete($immediateSupport);
            $immediateSupport->delete();
        });

        return redirect()->route('support.index', ['category' => $category])
            ->with('success', 'Support deleted successfully.');
    }

    private function ensureBelongsToCategory(ImmediateSupport $support, string $category): void
    {
        abort_unless(in_array($support->support_type, ImmediateSupport::categoryTypes($category), true), 404);
    }

    private function formData(array $types): array
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'phone', 'profile_type']);

        return [
            'users' => $users,
            'types' => array_intersect_key(ImmediateSupport::TYPES, array_flip($types)),
            'statuses' => ImmediateSupport::STATUSES,
            'paidFromOptions' => ImmediateSupport::PAID_FROM,
        ];
    }

    private function validateSupport(Request $request, array $types): array
    {
        return $request->validate([
            'support_type' => ['required', Rule::in($types)],
            'user_id' => 'nullable|exists:users,id',
            'recipient_name' => 'nullable|string|max:255|required_without:user_id',
            'recipient_phone' => 'nullable|string|max:20',
            'support_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'paid_from' => ['nullable', Rule::in(array_keys(ImmediateSupport::PAID_FROM))],
            'status' => ['required', Rule::in(array_keys(ImmediateSupport::STATUSES))],
            'description' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ]);
    }
}
