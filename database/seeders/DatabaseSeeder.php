<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's core system data:
     * roles, global/theme settings, default branch, academic year
     * and the superadmin account.
     */
    public function run(): void
    {
        $now = now();

        /* ---- roles (legacy role IDs 1-7) ---- */
        DB::table('roles')->insert(array_map(fn ($role) => [
            'id' => $role[0],
            'name' => $role[1],
            'prefix' => null,
            'is_system' => 1,
        ], [
            [1, 'System Admin'],
            [2, 'Admin'],
            [3, 'Teacher'],
            [4, 'Accountant'],
            [5, 'Librarian'],
            [6, 'Parent'],
            [7, 'Student'],
        ]));

        /* ---- global settings ---- */
        DB::table('global_settings')->insert([
            'id' => 1,
            'institute_name' => 'CMFI School',
            'institution_code' => 'CMFI',
            'reg_prefix' => 'CMFI',
            'institute_email' => 'admin@example.com',
            'address' => 'Head Office',
            'mobileno' => '0000000000',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'sms_service_provider' => 'none',
            'session_id' => 1,
            'translation' => 'english',
            'footer_text' => 'CMFI School Management System',
            'animations' => '1',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'facebook_url' => '',
            'twitter_url' => '',
            'linkedin_url' => '',
            'youtube_url' => '',
            'cron_secret_key' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        /* ---- theme settings ---- */
        DB::table('theme_settings')->insert([
            'id' => 1,
            'border_mode' => 'rounded',
            'dark_skin' => 'false',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        /* ---- academic session ---- */
        DB::table('schoolyear')->insert([
            'id' => 1,
            'school_year' => date('Y').'-'.(date('Y') + 1),
            'created_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        /* ---- default branch ---- */
        DB::table('branch')->insert([
            'id' => 1,
            'name' => 'Main Branch',
            'school_name' => 'CMFI School',
            'email' => 'admin@example.com',
            'mobileno' => '0000000000',
            'currency' => 'USD',
            'symbol' => '$',
            'city' => 'Main City',
            'state' => 'Main State',
            'address' => 'Main Branch Address',
            'use_section' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        /* ---- department & designation for the admin profile ---- */
        DB::table('staff_department')->insert(['id' => 1, 'name' => 'Administration', 'branch_id' => 1, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('staff_designation')->insert(['id' => 1, 'name' => 'System Administrator', 'branch_id' => 1, 'created_at' => $now, 'updated_at' => $now]);

        /* ---- superadmin staff profile ---- */
        DB::table('staff')->insert([
            'id' => 1,
            'staff_id' => 'EMP-001',
            'branch_id' => 1,
            'name' => 'System Admin',
            'department' => 1,
            'qualification' => 'N/A',
            'designation' => 1,
            'joining_date' => date('Y-m-d'),
            'birthday' => '1990-01-01',
            'sex' => 'Male',
            'religion' => 'N/A',
            'blood_group' => 'N/A',
            'photo' => 'defualt.png',
            'mobileno' => '0000000000',
            'email' => 'admin@example.com',
            'present_address' => '-',
            'permanent_address' => '-',
            'hash' => hash('sha256', uniqid('', true)),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        /* ---- login credential: admin@example.com / password ---- */
        DB::table('login_credential')->insert([
            'user_id' => 1,
            'username' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
