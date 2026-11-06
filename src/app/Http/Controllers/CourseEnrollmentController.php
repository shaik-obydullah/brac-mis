<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseEnrollmentController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'participant' => 'required|string',
            'enrolled_at' => 'required|date',
            'status' => ['required', Rule::in(array_keys(CourseEnrollment::STATUSES))],
            'score' => 'nullable|numeric|min:0|max:100',
            'remarks' => 'nullable|string|max:2000',
        ]);

        [$type, $id] = $this->parseParticipant($validated['participant']);

        $exists = $course->enrollments()
            ->where('participant_type', $type)
            ->where('participant_id', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'This participant is already enrolled in the course.');
        }

        $enrollment = $course->enrollments()->create([
            'participant_type' => $type,
            'participant_id' => $id,
            'enrolled_at' => $validated['enrolled_at'],
            'completed_at' => $validated['status'] === 'completed' ? now()->toDateString() : null,
            'status' => $validated['status'],
            'score' => $validated['score'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $this->auditService->logCreate($enrollment);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Participant enrolled successfully.');
    }

    public function edit(Course $course, CourseEnrollment $enrollment)
    {
        $this->ensureBelongsToCourse($course, $enrollment);

        return view('courses.enrollments.edit', [
            'course' => $course,
            'enrollment' => $enrollment,
            'statuses' => CourseEnrollment::STATUSES,
        ]);
    }

    public function update(Request $request, Course $course, CourseEnrollment $enrollment)
    {
        $this->ensureBelongsToCourse($course, $enrollment);

        $validated = $request->validate([
            'enrolled_at' => 'required|date',
            'completed_at' => 'nullable|date|after_or_equal:enrolled_at',
            'status' => ['required', Rule::in(array_keys(CourseEnrollment::STATUSES))],
            'score' => 'nullable|numeric|min:0|max:100',
            'remarks' => 'nullable|string|max:2000',
        ]);

        if ($validated['status'] === 'completed' && empty($validated['completed_at'])) {
            $validated['completed_at'] = now()->toDateString();
        }

        $old = $enrollment->toArray();
        $enrollment->update($validated);
        $this->auditService->logUpdate($enrollment, $old);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Enrollment updated successfully.');
    }

    public function destroy(Course $course, CourseEnrollment $enrollment)
    {
        $this->ensureBelongsToCourse($course, $enrollment);

        $this->auditService->logDelete($enrollment);
        $enrollment->delete();

        return redirect()->route('courses.show', $course)
            ->with('success', 'Enrollment removed successfully.');
    }

    private function ensureBelongsToCourse(Course $course, CourseEnrollment $enrollment): void
    {
        abort_unless($enrollment->course_id === $course->id, 404);
    }

    private function parseParticipant(string $value): array
    {
        [$type, $id] = array_pad(explode(':', $value, 2), 2, null);

        abort_unless($type === 'user' && $id !== null && ctype_digit((string) $id), 422);
        abort_unless(User::whereKey($id)->exists(), 422);

        return [$type, (int) $id];
    }
}
