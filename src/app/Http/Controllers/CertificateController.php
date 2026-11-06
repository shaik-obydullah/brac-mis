<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CertificateController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $query = Certificate::with('course', 'issuedBy');

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhere('verification_code', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status') && array_key_exists($request->status, Certificate::STATUSES)) {
            $query->where('status', $request->status);
        }

        $certificates = $query->latest('issued_date')->latest('id')->paginate(15)->withQueryString();
        $courses = Course::orderBy('title')->get(['id', 'title']);

        $totals = [
            'certificates' => Certificate::count(),
            'valid' => Certificate::where('status', 'valid')->count(),
            'revoked' => Certificate::where('status', 'revoked')->count(),
        ];

        return view('certificates.index', compact('certificates', 'courses', 'totals'));
    }

    public function create(Request $request)
    {
        $courses = Course::orderBy('title')->get(['id', 'title', 'code']);
        $enrollments = CourseEnrollment::with('course:id,title,code', 'participant')
            ->orderByDesc('id')
            ->get();

        $selectedEnrollment = $request->filled('enrollment')
            ? $enrollments->firstWhere('id', $request->integer('enrollment'))
            : null;

        $selectedCourseId = $selectedEnrollment?->course_id
            ?? ($request->filled('course_id') ? $request->integer('course_id') : null);

        return view('certificates.create', [
            'certificate' => new Certificate([
                'course_id' => $selectedCourseId,
                'course_enrollment_id' => $selectedEnrollment?->id,
                'issued_date' => now()->toDateString(),
                'status' => 'valid',
            ]),
            'courses' => $courses,
            'enrollments' => $enrollments,
            'statuses' => Certificate::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateStore($request);

        if (! empty($validated['course_enrollment_id'])) {
            $enrollment = CourseEnrollment::with('participant')
                ->findOrFail($validated['course_enrollment_id']);

            abort_unless($enrollment->course_id === (int) $validated['course_id'], 422);

            if ($enrollment->certificate) {
                return back()->withInput()->with('error', 'A certificate has already been issued for this enrollment.');
            }

            $validated['recipient_type'] = $enrollment->participant_type;
            $validated['recipient_id'] = $enrollment->participant_id;
            $validated['recipient_name'] = ($validated['recipient_name'] ?? null) ?: $enrollment->participant_name;

            if ($enrollment->status !== 'completed') {
                $enrollment->update([
                    'status' => 'completed',
                    'completed_at' => $enrollment->completed_at ?? now()->toDateString(),
                ]);
            }
        }

        $validated['certificate_number'] = ($validated['certificate_number'] ?? null) ?: $this->generateCertificateNumber();
        $validated['verification_code'] = $this->generateVerificationCode();
        $validated['issued_by'] = auth()->id();

        $certificate = Certificate::create($validated);
        $this->auditService->logCreate($certificate);

        return redirect()->route('certificates.show', $certificate)
            ->with('success', 'Certificate created successfully.');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load('course', 'enrollment', 'recipient', 'issuedBy');

        return view('certificates.show', compact('certificate'));
    }

    public function edit(Certificate $certificate)
    {
        return view('certificates.edit', [
            'certificate' => $certificate->load('course'),
            'statuses' => Certificate::STATUSES,
        ]);
    }

    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'certificate_number' => ['required', 'string', 'max:100', Rule::unique('certificates', 'certificate_number')->ignore($certificate->id)],
            'recipient_name' => 'required|string|max:255',
            'issued_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issued_date',
            'status' => ['required', Rule::in(array_keys(Certificate::STATUSES))],
            'remarks' => 'nullable|string|max:2000',
        ]);

        $old = $certificate->toArray();
        $certificate->update($validated);
        $this->auditService->logUpdate($certificate, $old);

        return redirect()->route('certificates.show', $certificate)
            ->with('success', 'Certificate updated successfully.');
    }

    public function destroy(Certificate $certificate)
    {
        $this->auditService->logDelete($certificate);
        $certificate->delete();

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }

    public function validateForm()
    {
        return view('certificates.validate', ['certificate' => null]);
    }

    public function validateCertificate(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|max:100',
        ]);

        $query = trim($validated['query']);

        $certificate = Certificate::where('certificate_number', $query)
            ->orWhere('verification_code', $query)
            ->first();

        if (! $certificate) {
            return back()->withInput()->with('error', 'No certificate found for that number or verification code.');
        }

        return redirect()->route('certificates.verify', $certificate->verification_code);
    }

    public function verify(string $code)
    {
        $certificate = Certificate::with('course', 'issuedBy')
            ->where('verification_code', $code)
            ->orWhere('certificate_number', $code)
            ->first();

        return view('certificates.validate', compact('certificate'));
    }

    private function validateStore(Request $request): array
    {
        return $request->validate([
            'course_id' => 'required|exists:courses,id',
            'course_enrollment_id' => 'nullable|exists:course_enrollments,id',
            'certificate_number' => ['nullable', 'string', 'max:100', Rule::unique('certificates', 'certificate_number')],
            'recipient_name' => 'nullable|string|max:255|required_without:course_enrollment_id',
            'issued_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issued_date',
            'status' => ['required', Rule::in(array_keys(Certificate::STATUSES))],
            'remarks' => 'nullable|string|max:2000',
        ]);
    }

    private function generateCertificateNumber(): string
    {
        $year = now()->year;
        $sequence = (int) Certificate::whereYear('created_at', $year)->count() + 1;

        do {
            $number = sprintf('BRAC-CERT-%d-%05d', $year, $sequence++);
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }

    private function generateVerificationCode(): string
    {
        do {
            $code = Str::upper(Str::random(10));
        } while (Certificate::where('verification_code', $code)->exists());

        return $code;
    }
}
