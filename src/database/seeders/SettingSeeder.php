<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['system_name', 'BRAC MIS', 'general'],
            ['organization_name', 'BRAC', 'general'],
            ['organization_slogan', 'Empowering People and Communities in Bangladesh', 'general'],
            ['currency', 'BDT', 'general'],
            ['currency_symbol', '৳', 'general'],
            ['contact_email', 'info@bracmis.org', 'general'],
            ['contact_phone', '+880-9611222222', 'general'],
            ['default_timezone', 'Asia/Dhaka', 'general'],
            ['pagination_size', '15', 'general'],
            ['auto_generate_brac_id', 'true', 'system'],
            ['minimum_age_beneficiary', '18', 'system'],
            ['document_max_size_mb', '10', 'system'],
            ['returnee_follow_up_interval_days', '30', 'system'],
            ['send_follow_up_reminders', 'true', 'notifications'],
            ['reminder_email_enabled', 'true', 'notifications'],
            ['audit_log_enabled', 'true', 'security'],
            ['session_lifetime_minutes', '120', 'security'],
            ['default_language', 'en', 'general'],
            ['report_export_format', 'pdf', 'reports'],
            ['currency_conversion_bdt', '{\"SAR\": 31.5, \"AED\": 30.8, \"QAR\": 31.0, \"KWD\": 368.5, \"OMR\": 293.2, \"BHD\": 299.4, \"MYR\": 24.1, \"SGD\": 84.3, \"EUR\": 121.9, \"GBP\": 142.6}', 'reports'],
        ];

        foreach ($settings as [$key, $value, $group]) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        }

        $this->command->info('Created ' . count($settings) . ' settings.');
    }
}
