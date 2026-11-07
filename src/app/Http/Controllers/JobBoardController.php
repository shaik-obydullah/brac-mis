<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\JobBoardInquiry;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobBoardController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));
        $district = (string) $request->get('district');
        $skill = trim((string) $request->get('skill'));

        $seekers = User::query()
            ->potentialCustomers()
            ->with(['branch', 'skills', 'migrationHistories.country'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('skills', fn ($skills) => $skills->where('skill_name', 'like', "%{$search}%"));
                });
            })
            ->when($district !== '', fn ($query) => $query->whereHas('branch', fn ($branch) => $branch->where('district', $district)))
            ->when($skill !== '', fn ($query) => $query->whereHas('skills', fn ($skills) => $skills->where('skill_name', 'like', "%{$skill}%")))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $districts = Branch::query()->whereNotNull('district')->pluck('district')->unique()->sort()->values();

        return view('job-board.index', [
            'seekers' => $seekers,
            'districts' => $districts,
        ]);
    }

    public function show(User $user)
    {
        abort_unless($user->isPotentialCustomer(), 404);

        $user->load([
            'branch', 'skills', 'migrationHistories.country',
        ]);

        return view('job-board.show', compact('user'));
    }

    public function submitInquiry(Request $request, User $user)
    {
        abort_unless($user->isPotentialCustomer(), 404);

        // Honeypot: real visitors never fill this hidden field.
        if ($request->filled('company_website')) {
            return redirect()->route('job-board.show', $user)
                ->with('success', 'Thanks — your request has been received. BRAC will follow up with you.');
        }

        $validated = $request->validate([
            'employer_name' => ['required', 'string', 'max:255'],
            'employer_organization' => ['nullable', 'string', 'max:255'],
            'employer_email' => ['required', 'email', 'max:255'],
            'employer_phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
            'consent' => ['accepted'],
        ]);

        $inquiry = JobBoardInquiry::create([
            'user_id' => $user->id,
            'employer_name' => $validated['employer_name'],
            'employer_organization' => $validated['employer_organization'] ?? null,
            'employer_email' => $validated['employer_email'],
            'employer_phone' => $validated['employer_phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        $this->auditService->logCreate($inquiry);

        return redirect()->route('job-board.show', $user)
            ->with('success', 'Your contact request has been sent to BRAC. A staff member will follow up with you.');
    }

    public function inquiries(Request $request)
    {
        $search = trim((string) $request->get('search'));
        $status = (string) $request->get('status');

        $query = JobBoardInquiry::query()
            ->with(['candidate', 'handledBy'])
            ->when($status !== '' && array_key_exists($status, JobBoardInquiry::STATUSES), fn ($q) => $q->where('status', $status))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($nested) use ($search) {
                    $nested->where('employer_name', 'like', "%{$search}%")
                        ->orWhere('employer_organization', 'like', "%{$search}%")
                        ->orWhere('employer_email', 'like', "%{$search}%")
                        ->orWhere('employer_phone', 'like', "%{$search}%")
                        ->orWhere('job_title', 'like', "%{$search}%")
                        ->orWhereHas('candidate', fn ($candidate) => $candidate->where('name', 'like', "%{$search}%"));
                });
            });

        $inquiries = $query->latest()->paginate(15)->withQueryString();

        $totals = [
            'all' => JobBoardInquiry::count(),
            'new' => JobBoardInquiry::where('status', 'new')->count(),
            'contacted' => JobBoardInquiry::where('status', 'contacted')->count(),
            'shared' => JobBoardInquiry::where('status', 'shared')->count(),
            'closed' => JobBoardInquiry::where('status', 'closed')->count(),
        ];

        return view('job-board.inquiries.index', [
            'inquiries' => $inquiries,
            'totals' => $totals,
            'statuses' => JobBoardInquiry::STATUSES,
        ]);
    }

    public function inquiry(JobBoardInquiry $inquiry)
    {
        $inquiry->load([
            'candidate.branch',
            'candidate.skills',
            'candidate.migrationHistories.country',
            'handledBy',
        ]);

        return view('job-board.inquiries.show', [
            'inquiry' => $inquiry,
            'statuses' => JobBoardInquiry::STATUSES,
        ]);
    }

    public function updateInquiry(Request $request, JobBoardInquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(JobBoardInquiry::STATUSES))],
        ]);

        $old = $inquiry->toArray();

        $data = ['status' => $validated['status']];

        if ($validated['status'] === 'new') {
            $data['handled_by'] = null;
            $data['handled_at'] = null;
        } elseif ($validated['status'] !== $inquiry->status) {
            $data['handled_by'] = auth()->id();
            $data['handled_at'] = now();
        }

        $inquiry->update($data);
        $this->auditService->logUpdate($inquiry, $old);

        return redirect()->route('job-board.inquiries.show', $inquiry)
            ->with('success', 'Inquiry status updated successfully.');
    }
}
