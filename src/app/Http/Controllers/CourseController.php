<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $query = Course::withCount('enrollments', 'certificates');

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && array_key_exists($request->status, Course::STATUSES)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('level') && array_key_exists($request->level, Course::LEVELS)) {
            $query->where('level', $request->level);
        }

        $courses = $query->latest('id')->paginate(15)->withQueryString();

        $totals = [
            'courses' => Course::count(),
            'active' => Course::where('status', 'active')->count(),
            'enrollments' => CourseEnrollment::count(),
            'certificates' => Certificate::count(),
        ];

        return view('courses.index', compact('courses', 'totals'));
    }

    public function create()
    {
        return view('courses.create', [
            'course' => new Course(['status' => 'active', 'level' => 'beginner']),
            'statuses' => Course::STATUSES,
            'levels' => Course::LEVELS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCourse($request);
        $validated['created_by'] = auth()->id();

        $course = Course::create($validated);
        $this->auditService->logCreate($course);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load('createdBy', 'enrollments.participant', 'enrollments.certificate', 'certificates');

        $participants = $this->participantOptions();

        return view('courses.show', compact('course', 'participants'));
    }

    public function edit(Course $course)
    {
        return view('courses.edit', [
            'course' => $course,
            'statuses' => Course::STATUSES,
            'levels' => Course::LEVELS,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $this->validateCourse($request, $course);

        $old = $course->toArray();
        $course->update($validated);
        $this->auditService->logUpdate($course, $old);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $this->auditService->logDelete($course);
        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    private function validateCourse(Request $request, ?Course $course = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('courses', 'code')->ignore($course?->id)],
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:3000',
            'duration_hours' => 'nullable|integer|min:1|max:1000',
            'level' => ['required', Rule::in(array_keys(Course::LEVELS))],
            'status' => ['required', Rule::in(array_keys(Course::STATUSES))],
        ]);
    }

    private function participantOptions(): array
    {
        return [
            'user' => User::orderBy('name')->get(['id', 'name']),
        ];
    }
}
