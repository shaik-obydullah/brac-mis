<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@bracmis.org')->first();

        $certificates = [
            [
                'course' => 'BRAC-TRN-001',
                'email' => 'fatima@bracmis.org',
                'certificate_number' => 'BRAC-CERT-2026-00001',
                'issued_date' => '2026-03-25',
                'expiry_date' => null,
                'status' => 'valid',
                'remarks' => 'Issued after successful completion of the tailoring assessment.',
            ],
            [
                'course' => 'BRAC-TRN-001',
                'email' => 'shahida@bracmis.org',
                'certificate_number' => 'BRAC-CERT-2026-00002',
                'issued_date' => '2026-03-28',
                'expiry_date' => null,
                'status' => 'valid',
                'remarks' => null,
            ],
            [
                'course' => 'BRAC-TRN-002',
                'email' => 'rahim@bracmis.org',
                'certificate_number' => 'BRAC-CERT-2026-00003',
                'issued_date' => '2026-05-15',
                'expiry_date' => null,
                'status' => 'valid',
                'remarks' => 'Electrical safety and solar installation certification.',
            ],
            [
                'course' => 'BRAC-TRN-003',
                'email' => 'ayesha@bracmis.org',
                'certificate_number' => 'BRAC-CERT-2026-00004',
                'issued_date' => '2026-06-05',
                'expiry_date' => null,
                'status' => 'valid',
                'remarks' => 'Food safety and catering certification.',
            ],
            [
                'course' => 'BRAC-TRN-004',
                'email' => 'kabir@bracmis.org',
                'certificate_number' => 'BRAC-CERT-2026-00005',
                'issued_date' => '2026-05-02',
                'expiry_date' => null,
                'status' => 'valid',
                'remarks' => null,
            ],
            [
                'course' => 'BRAC-TRN-004',
                'email' => 'mizanur@bracmis.org',
                'certificate_number' => 'BRAC-CERT-2025-00001',
                'issued_date' => '2025-10-28',
                'expiry_date' => '2026-10-28',
                'status' => 'valid',
                'remarks' => 'Renewal required after the expiry date.',
            ],
            [
                'course' => 'BRAC-TRN-002',
                'email' => 'jamil@bracmis.org',
                'certificate_number' => 'BRAC-CERT-2026-00006',
                'issued_date' => '2026-05-15',
                'expiry_date' => null,
                'status' => 'revoked',
                'remarks' => 'Revoked after a verification discrepancy was reported.',
            ],
        ];

        foreach ($certificates as $row) {
            $course = Course::where('code', $row['course'])->first();
            $user = User::where('email', $row['email'])->first();

            if (! $course || ! $user) {
                continue;
            }

            $enrollment = CourseEnrollment::where('course_id', $course->id)
                ->where('participant_type', 'user')
                ->where('participant_id', $user->id)
                ->first();

            Certificate::firstOrCreate(
                ['certificate_number' => $row['certificate_number']],
                [
                    'course_id' => $course->id,
                    'course_enrollment_id' => $enrollment?->id,
                    'verification_code' => Str::upper(Str::random(10)),
                    'recipient_type' => 'user',
                    'recipient_id' => $user->id,
                    'recipient_name' => $user->name,
                    'issued_date' => $row['issued_date'],
                    'expiry_date' => $row['expiry_date'],
                    'status' => $row['status'],
                    'remarks' => $row['remarks'],
                    'issued_by' => $admin?->id,
                ],
            );
        }

        $this->command->info('Created '.count($certificates).' certificates.');
    }
}
