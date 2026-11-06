<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\ImmediateSupport;
use App\Models\Returnee;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ImmediateSupportController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index(Request $request, string $category)
    {
        $types = ImmediateSupport::categoryTypes($category);

        $query = ImmediateSupport::with('beneficiary', 'returnee.beneficiary', 'providedBy')
            ->whereIn('support_type', $types);

        if (count($types) > 1 && $request->filled('type') && in_array($request->type, $types, true)) {
            $query->where('support_type', $request->type);
        }

        if ($request->filled('status') && array_key_exists($request->status, ImmediateSupport::STATUSES)) {
            $query->where('status', $request->status);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_phone', 'like', "%{$search}%")
                    ->orWhereHas('beneficiary', function ($beneficiary) use ($search) {
                        $beneficiary->where('name', 'like', "%{$search}%")
                            ->orWhere('brac_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('returnee.beneficiary', fn ($beneficiary) => $beneficiary->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('support_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('support_date', '<=', $request->to);
        }

        $supports = $query->latest('support_date')->latest('id')->paginate(15)->withQueryString();

        $typeCounts = ImmediateSupport::whereIn('support_type', $types)
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

        $support = ImmediateSupport::create($validated);
        $this->auditService->logCreate($support);

        return redirect()->route('support.show', ['category' => $category, 'immediateSupport' => $support])
            ->with('success', 'Support recorded successfully.');
    }

    public function show(string $category, ImmediateSupport $immediateSupport)
    {
        $this->ensureBelongsToCategory($immediateSupport, $category);
        $immediateSupport->load('beneficiary', 'returnee.beneficiary', 'providedBy');

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

        $old = $immediateSupport->toArray();
        $immediateSupport->update($validated);
        $this->auditService->logUpdate($immediateSupport, $old);

        return redirect()->route('support.show', ['category' => $category, 'immediateSupport' => $immediateSupport])
            ->with('success', 'Support updated successfully.');
    }

    public function destroy(string $category, ImmediateSupport $immediateSupport)
    {
        $this->ensureBelongsToCategory($immediateSupport, $category);

        $this->auditService->logDelete($immediateSupport);
        $immediateSupport->delete();

        return redirect()->route('support.index', ['category' => $category])
            ->with('success', 'Support deleted successfully.');
    }

    private function ensureBelongsToCategory(ImmediateSupport $support, string $category): void
    {
        abort_unless(in_array($support->support_type, ImmediateSupport::categoryTypes($category), true), 404);
    }

    private function formData(array $types): array
    {
        $beneficiaries = Beneficiary::orderBy('name')->get(['id', 'brac_id', 'name', 'phone']);

        $returnees = Returnee::with('beneficiary:id,name,brac_id')
            ->orderByDesc('return_date')
            ->get(['id', 'beneficiary_id', 'return_date']);

        return [
            'beneficiaries' => $beneficiaries,
            'returnees' => $returnees,
            'types' => array_intersect_key(ImmediateSupport::TYPES, array_flip($types)),
            'statuses' => ImmediateSupport::STATUSES,
        ];
    }

    private function validateSupport(Request $request, array $types): array
    {
        return $request->validate([
            'support_type' => ['required', Rule::in($types)],
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'returnee_id' => 'nullable|exists:returnees,id',
            'recipient_name' => 'nullable|string|max:255|required_without_all:beneficiary_id,returnee_id',
            'recipient_phone' => 'nullable|string|max:20',
            'support_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(array_keys(ImmediateSupport::STATUSES))],
            'description' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ]);
    }
}
