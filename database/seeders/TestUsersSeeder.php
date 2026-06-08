<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // ── Test Accounts ─────────────────────────────────

        // Test Admin (separate from main admin)
        User::updateOrCreate(
            ['email' => 'admin@smstest.com'],
            [
                'name'      => 'Test Admin',
                'password'  => Hash::make('Admin@1234'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // Test Trainer
        User::updateOrCreate(
            ['email' => 'trainer@smstest.com'],
            [
                'name'        => 'Test Trainer',
                'password'    => Hash::make('Trainer@1234'),
                'role'        => 'trainer',
                'is_active'   => true,
                'designation' => 'Lead Trainer',
                'company'     => 'SMS',
            ]
        );

        // Test Participant
        User::updateOrCreate(
            ['email' => 'participant@smstest.com'],
            [
                'name'      => 'Test Participant',
                'password'  => Hash::make('Participant@1234'),
                'role'      => 'participant',
                'is_active' => true,
                'company'   => 'ABC Ltd',
            ]
        );

        $this->command->info('✅ Test users created:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',       'admin@smstest.com',       'Admin@1234'],
                ['Trainer',     'trainer@smstest.com',     'Trainer@1234'],
                ['Participant', 'participant@smstest.com', 'Participant@1234'],
            ]
        );

        // ── Default Settings ──────────────────────────────

        $defaults = [
            ['elearning.default_pass_mark',          '70',  'elearning', 'Default Pass Mark (%)'],
            ['elearning.min_attendance_pct',          '80',  'elearning', 'Min Attendance for Certificate (%)'],
            ['elearning.completion_requires_quiz',    '1',   'elearning', 'Completion Requires Quiz Pass'],
            ['elearning.completion_requires_payment', '1',   'elearning', 'Completion Requires Payment Cleared'],
            ['elearning.auto_eligible',               '1',   'elearning', 'Auto-Set Certificate Eligible'],
            ['elearning.admin_approval_required',     '1',   'elearning', 'Admin Approval Required to Issue'],
            ['elearning.allow_self_registration',     '1',   'elearning', 'Allow Self-Registration'],
            ['elearning.auto_link_enrollment',        '1',   'elearning', 'Auto-Link Enrollment by Email'],
        ];

        foreach ($defaults as [$key, $value, $group, $label]) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group, 'label' => $label]
            );
        }

        $this->command->info('✅ Default settings seeded.');
    }
}
