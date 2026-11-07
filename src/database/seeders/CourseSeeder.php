<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@bracmis.org')->first();

        $courses = [
            [
                'code' => 'BRAC-TRN-001',
                'title' => 'Tailoring & Garment Production',
                'category' => 'Skills Development',
                'description' => 'Practical tailoring, pattern making and garment finishing skills for reintegration into the ready-made garment sector.',
                'duration_hours' => 120,
                'level' => 'intermediate',
                'status' => 'active',
            ],
            [
                'code' => 'BRAC-TRN-002',
                'title' => 'Electrical Wiring & Solar Installation',
                'category' => 'Technical Skills',
                'description' => 'Safe electrical wiring, troubleshooting and rooftop solar panel installation for technicians and returning migrants.',
                'duration_hours' => 80,
                'level' => 'advanced',
                'status' => 'active',
            ],
            [
                'code' => 'BRAC-TRN-003',
                'title' => 'Food Safety & Catering',
                'category' => 'Hospitality',
                'description' => 'Food hygiene, kitchen safety and small-scale catering operations for aspiring food entrepreneurs.',
                'duration_hours' => 60,
                'level' => 'beginner',
                'status' => 'active',
            ],
            [
                'code' => 'BRAC-TRN-004',
                'title' => 'Basic Financial Literacy',
                'category' => 'Financial Literacy',
                'description' => 'Budgeting, savings, responsible borrowing and record keeping for returnees starting small businesses.',
                'duration_hours' => 24,
                'level' => 'beginner',
                'status' => 'active',
            ],
            [
                'code' => 'BRAC-TRN-005',
                'title' => 'Returnee Reintegration Orientation',
                'category' => 'Reintegration',
                'description' => 'Orientation on services, rights and local opportunities available to returning migrants and their families.',
                'duration_hours' => 12,
                'level' => 'beginner',
                'status' => 'active',
            ],
            [
                'code' => 'BRAC-TRN-006',
                'title' => 'Advanced Welding & Metal Fabrication',
                'category' => 'Technical Skills',
                'description' => 'Advanced welding techniques and metal fabrication for participants pursuing skilled industrial work.',
                'duration_hours' => 100,
                'level' => 'advanced',
                'status' => 'inactive',
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['code' => $course['code']],
                $course + ['created_by' => $admin?->id],
            );
        }

        $enrollments = [
            ['course' => 'BRAC-TRN-001', 'email' => 'fatima@bracmis.org', 'status' => 'completed', 'enrolled_at' => '2026-01-15', 'completed_at' => '2026-03-20', 'score' => 92.50, 'remarks' => 'Completed the garment finishing assessment with distinction.'],
            ['course' => 'BRAC-TRN-001', 'email' => 'shahida@bracmis.org', 'status' => 'completed', 'enrolled_at' => '2026-01-15', 'completed_at' => '2026-03-20', 'score' => 87.00, 'remarks' => 'Strong practical skills in sewing machine operation.'],
            ['course' => 'BRAC-TRN-001', 'email' => 'nazma@bracmis.org', 'status' => 'in_progress', 'enrolled_at' => '2026-09-01', 'completed_at' => null, 'score' => null, 'remarks' => null],
            ['course' => 'BRAC-TRN-002', 'email' => 'rahim@bracmis.org', 'status' => 'completed', 'enrolled_at' => '2026-02-10', 'completed_at' => '2026-05-08', 'score' => 94.00, 'remarks' => 'Passed the electrical safety and solar installation practical exam.'],
            ['course' => 'BRAC-TRN-002', 'email' => 'jamil@bracmis.org', 'status' => 'completed', 'enrolled_at' => '2026-02-10', 'completed_at' => '2026-05-08', 'score' => 81.50, 'remarks' => 'Completed all practical modules.'],
            ['course' => 'BRAC-TRN-003', 'email' => 'ayesha@bracmis.org', 'status' => 'completed', 'enrolled_at' => '2026-03-01', 'completed_at' => '2026-05-30', 'score' => 96.00, 'remarks' => 'Excellent food hygiene and catering assessment.'],
            ['course' => 'BRAC-TRN-004', 'email' => 'kabir@bracmis.org', 'status' => 'completed', 'enrolled_at' => '2026-04-05', 'completed_at' => '2026-04-28', 'score' => 89.00, 'remarks' => 'Developed a workable business budget plan.'],
            ['course' => 'BRAC-TRN-004', 'email' => 'mizanur@bracmis.org', 'status' => 'completed', 'enrolled_at' => '2025-10-01', 'completed_at' => '2025-10-25', 'score' => 84.00, 'remarks' => 'Completed financial literacy and record keeping training.'],
            ['course' => 'BRAC-TRN-005', 'email' => 'fatima@bracmis.org', 'status' => 'completed', 'enrolled_at' => '2026-08-01', 'completed_at' => '2026-08-03', 'score' => 90.00, 'remarks' => null],
            ['course' => 'BRAC-TRN-005', 'email' => 'kabir@bracmis.org', 'status' => 'enrolled', 'enrolled_at' => '2026-11-01', 'completed_at' => null, 'score' => null, 'remarks' => null],
        ];

        foreach ($enrollments as $row) {
            $course = Course::where('code', $row['course'])->first();
            $user = User::where('email', $row['email'])->first();

            if (! $course || ! $user) {
                continue;
            }

            CourseEnrollment::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'participant_type' => 'user',
                    'participant_id' => $user->id,
                ],
                [
                    'enrolled_at' => $row['enrolled_at'],
                    'completed_at' => $row['completed_at'],
                    'status' => $row['status'],
                    'score' => $row['score'],
                    'remarks' => $row['remarks'],
                    'created_by' => $admin?->id,
                ],
            );
        }

        $this->command->info('Created '.count($courses).' courses and '.count($enrollments).' enrollments.');
    }
}
