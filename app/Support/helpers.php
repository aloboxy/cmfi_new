<?php

/*
|--------------------------------------------------------------------------
| Global helpers ported from legacy_code/application/helpers/general_helper.php
|--------------------------------------------------------------------------
*/

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/* Role IDs (mirrors legacy roles table seed) */
if (! defined('ROLE_SUPERADMIN')) {
    define('ROLE_SUPERADMIN', 1);
    define('ROLE_ADMIN', 2);
    define('ROLE_TEACHER', 3);
    define('ROLE_ACCOUNTANT', 4);
    define('ROLE_LIBRARIAN', 5);
    define('ROLE_PARENT', 6);
    define('ROLE_STUDENT', 7);
}

if (! function_exists('translate')) {
    /* DB driven translation, falls back to english column / humanized word */
    function translate($word = '')
    {
        if ($word === '') {
            return '';
        }

        static $cache = [];

        $lang = get_global_setting('translation') ?: 'english';

        $key = $word.'|'.$lang;
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            $row = DB::table('languages')->where('word', $word)->first();
        } catch (Throwable $e) {
            return ucwords(str_replace('_', ' ', $word));
        }

        if ($row) {
            $value = ($lang !== 'english' && isset($row->$lang) && $row->$lang !== '')
                ? $row->$lang
                : $row->english;
        } else {
            $value = ucwords(str_replace('_', ' ', $word));
            try {
                DB::table('languages')->insert(['word' => $word, 'english' => $value]);
            } catch (Throwable $e) {
                // ignore duplicate inserts on concurrent requests
            }
        }

        return $cache[$key] = $value;
    }
}

if (! function_exists('get_global_setting')) {
    function get_global_setting($name = '')
    {
        static $settings = null;

        if ($settings === null) {
            try {
                $settings = Schema::hasTable('global_settings')
                    ? (array) DB::table('global_settings')->where('id', 1)->first()
                    : [];
            } catch (Throwable $e) {
                $settings = [];
            }
        }

        return $name === '' ? $settings : ($settings[$name] ?? null);
    }
}

if (! function_exists('get_theme_config')) {
    function get_theme_config($name = '')
    {
        static $config = null;

        if ($config === null) {
            try {
                $config = Schema::hasTable('theme_settings')
                    ? (array) DB::table('theme_settings')->where('id', 1)->first()
                    : [];
            } catch (Throwable $e) {
                $config = [];
            }
        }

        return $name === '' ? $config : ($config[$name] ?? null);
    }
}

if (! function_exists('auth_user')) {
    /* @return User|null currently authenticated login_credential row */
    function auth_user(): ?User
    {
        return Auth::user();
    }
}

if (! function_exists('loggedin_role_id')) {
    function loggedin_role_id()
    {
        return auth_user()->role ?? null;
    }
}

if (! function_exists('loggedin_role_name')) {
    function loggedin_role_name()
    {
        $user = auth_user();

        return $user ? Cache::remember('role_name_'.$user->role, 3600, fn () => DB::table('roles')->where('id', $user->role)->value('name')) : null;
    }
}

if (! function_exists('is_superadmin_loggedin')) {
    function is_superadmin_loggedin(): bool
    {
        return loggedin_role_id() == ROLE_SUPERADMIN;
    }
}

if (! function_exists('is_admin_loggedin')) {
    function is_admin_loggedin(): bool
    {
        return loggedin_role_id() == ROLE_ADMIN;
    }
}

if (! function_exists('is_teacher_loggedin')) {
    function is_teacher_loggedin(): bool
    {
        return loggedin_role_id() == ROLE_TEACHER;
    }
}

if (! function_exists('is_accountant_loggedin')) {
    function is_accountant_loggedin(): bool
    {
        return loggedin_role_id() == ROLE_ACCOUNTANT;
    }
}

if (! function_exists('is_librarian_loggedin')) {
    function is_librarian_loggedin(): bool
    {
        return loggedin_role_id() == ROLE_LIBRARIAN;
    }
}

if (! function_exists('is_parent_loggedin')) {
    function is_parent_loggedin(): bool
    {
        return loggedin_role_id() == ROLE_PARENT;
    }
}

if (! function_exists('is_student_loggedin')) {
    function is_student_loggedin(): bool
    {
        return loggedin_role_id() == ROLE_STUDENT;
    }
}

if (! function_exists('get_loggedin_id')) {
    /* login credential table id */
    function get_loggedin_id()
    {
        return auth_user()?->id;
    }
}

if (! function_exists('get_loggedin_user_id')) {
    /* profile table id (staff / student / parent) */
    function get_loggedin_user_id()
    {
        return auth_user()?->user_id;
    }
}

if (! function_exists('get_loggedin_user_type')) {
    /* staff | parent | student */
    function get_loggedin_user_type(): string
    {
        return match ((int) loggedin_role_id()) {
            ROLE_PARENT => 'parent',
            ROLE_STUDENT => 'student',
            default => 'staff',
        };
    }
}

if (! function_exists('get_loggedin_branch_id')) {
    function get_loggedin_branch_id()
    {
        return auth_user()?->profile?->branch_id;
    }
}

if (! function_exists('get_activeChildren_id')) {
    /* the student a parent is currently browsing on behalf of */
    function get_activeChildren_id()
    {
        return session('myChildren_id');
    }
}

if (! function_exists('get_session_id')) {
    /* active academic session id */
    function get_session_id()
    {
        return session('set_session_id') ?: get_global_setting('session_id');
    }
}

if (! function_exists('set_alert')) {
    /* flash message rendered as toast by layouts.app */
    function set_alert($type, $message)
    {
        session()->flash('alert-message-'.$type, $message);
    }
}

if (! function_exists('get_permission')) {
    /* permission check for staff roles; superadmin always true */
    function get_permission($permission, $can = 'is_view'): bool
    {
        $roleID = (int) loggedin_role_id();
        if ($roleID === ROLE_SUPERADMIN) {
            return true;
        }
        // Students & parents: allowed to VIEW student-facing modules only
        if (in_array($roleID, [ROLE_PARENT, ROLE_STUDENT], true)) {
            $studentViewable = [
                'homework', 'exam_mark', 'exam_grade', 'exam_timetable', 'report_card',
                'live_class', 'book', 'book_issue', 'collect_fees', 'message', 'notice', 'event',
                'student_attendance', 'subject', 'class_timetable', 'attachments',
                'student', 'leave_request',
            ];
            return $can === 'is_view' && in_array($permission, $studentViewable, true);
        }

        $staffPerms = get_staff_permissions($roleID);
        if ($staffPerms->isNotEmpty()) {
            foreach ($staffPerms as $perm) {
                if (is_string($perm)) continue;
                $prefix = is_object($perm) ? ($perm->permission_prefix ?? null) : (is_array($perm) ? ($perm['permission_prefix'] ?? null) : null);
                $value = is_object($perm) ? ($perm->$can ?? null) : (is_array($perm) ? ($perm[$can] ?? null) : null);
                if ($prefix === $permission && (string)$value === '1') {
                    return true;
                }
            }
            return false;
        }

        // Sensible role-based defaults when staff_privileges table is unseeded
        return match ($roleID) {
            ROLE_ADMIN => true,
            ROLE_TEACHER => in_array($permission, [
                'student', 'id_card', 'classes', 'assign_class_teacher', 'subject', 'subject_class_assign',
                'class_timetable', 'live_class', 'attachments', 'attachment_type', 'homework', 'evaluation_report',
                'exam', 'exam_timetable', 'exam_mark', 'report_card', 'exam_grade', 'student_attendance',
                'staff_attendance', 'leave_request', 'message', 'notice', 'event', 'award', 'routes', 'vehicles', 'hostel', 'room'
            ], true),
            ROLE_ACCOUNTANT => in_array($permission, [
                'fees_type', 'fees_group', 'fees_master', 'fees_allocation', 'collect_fees',
                'fees_reminder', 'fine', 'accounting', 'voucher_head', 'expense_voucher',
                'deposit_voucher', 'income_report', 'expense_report', 'balance_sheet',
                'account_statement', 'student', 'payroll', 'salary_template', 'salary_assign', 'salary_payment',
                'advance_salary_request', 'advance_salary_manage', 'leave_request', 'message', 'notice', 'event'
            ], true),
            ROLE_LIBRARIAN => in_array($permission, [
                'book', 'book_issue', 'student', 'leave_request', 'advance_salary_request', 'message', 'notice', 'event'
            ], true),
            default => false,
        };
    }
}

if (! function_exists('get_staff_permissions')) {
    function get_staff_permissions($roleId)
    {
        if (in_array((int) $roleId, [ROLE_PARENT, ROLE_STUDENT], true)) {
            return collect();
        }
        if (empty($roleId)) {
            return collect();
        }
        return Cache::rememberForever('staff_permissions_'.$roleId, function () use ($roleId) {
            $rows = DB::table('staff_privileges')
                ->join('permission', 'permission.id', '=', 'staff_privileges.permission_id')
                ->where('staff_privileges.role_id', $roleId)
                ->select('staff_privileges.*', 'permission.prefix as permission_prefix')
                ->get();
            // Ensure we always return a collection of objects
            return $rows->filter(fn($r) => is_object($r) && isset($r->permission_prefix));
        });
    }
}

if (! function_exists('access_denied')) {
    function access_denied()
    {
        set_alert('error', translate('access_denied'));

        return redirect()->route('dashboard');
    }
}

if (! function_exists('_d')) {
    /* format date according to global_settings.date_format */
    function _d($date)
    {
        if ($date == '' || $date === null || $date == '0000-00-00') {
            return '';
        }

        $format = get_global_setting('date_format') ?: 'Y-m-d';

        return date($format, strtotime($date));
    }
}

if (! function_exists('get_type_name_by_id')) {
    function get_type_name_by_id($table, $typeId = '', $field = 'name')
    {
        if (empty($typeId)) {
            return '';
        }

        return DB::table($table)->where('id', $typeId)->value($field) ?? '';
    }
}

if (! function_exists('nav_visible')) {
    function nav_visible(array $item): bool
    {
        $role = (int) loggedin_role_id();

        // 1. Explicit role whitelist check
        if (isset($item['roles'])) {
            if (! in_array($role, $item['roles'], true)) {
                return false;
            }
        }

        // 2. Explicit excluded roles check
        if (isset($item['exclude_roles'])) {
            if (in_array($role, $item['exclude_roles'], true)) {
                return false;
            }
        }

        // Module control — hide disabled modules for everyone (super admin sees them as disabled but can still access via Module Control page)
        if (isset($item['label'])) {
            $moduleKey = strtolower(str_replace([' ', '-'], '_', trim($item['label'])));
            // Always show module_control itself even if disabled so super admin can re-enable
            if ($moduleKey !== 'module_control' && !is_module_enabled($moduleKey)) {
                return false;
            }
        }

        // 3. For leaf items (no children)
        if (! isset($item['children'])) {
            // If explicit role is set and matched, allow
            if (isset($item['roles']) && in_array($role, $item['roles'], true)) {
                return true;
            }

            // Student or Parent: if no explicit role whitelist allowed them, deny
            if (in_array($role, [ROLE_PARENT, ROLE_STUDENT], true)) {
                return false;
            }

            // Superadmin has full access to all staff items
            if ($role === ROLE_SUPERADMIN) {
                return true;
            }

            // If permission is required, verify permission
            if (isset($item['permission'])) {
                return get_permission($item['permission'][0], $item['permission'][1] ?? 'is_view');
            }

            return true;
        }

        // 4. For group items with children: visible if at least one child is visible
        foreach ($item['children'] as $child) {
            if (nav_visible($child)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('nav_is_active')) {
    function nav_is_active(array $item): bool
    {
        $routeName = $item['route'] ?? null;
        $currentName = request()->route()?->getName();
        $currentUri = request()->path();
        if ($routeName && Route::has($routeName) && !empty($currentName)) {
            if ($currentName === $routeName) return true;
            // Related suffix groups: create<->store, edit<->update, index<->view/show, delete<->destroy
            $groups = [['create','store'],['edit','update'],['index','view','show'],['delete','destroy'],['save','store']];
            $routeSuffix = substr(strrchr($routeName, '.'), 1) ?: '';
            $currentSuffix = substr(strrchr($currentName, '.'), 1) ?: '';
            $routeBase = preg_replace('/\.(create|store|edit|update|destroy|delete|save|index|show|view)$/', '', $routeName);
            $currentBase = preg_replace('/\.(create|store|edit|update|destroy|delete|save|index|show|view)$/', '', $currentName);
            if ($routeBase && $routeBase === $currentBase) {
                foreach ($groups as $g) {
                    if (in_array($routeSuffix, $g, true) && in_array($currentSuffix, $g, true)) return true;
                }
            }
            // Fallback for routes with same base but exact URI match (e.g. /student/add vs /student)
            if ($routeBase === $currentBase && $routeSuffix === $currentSuffix) return true;
        }
        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if (nav_is_active($child)) return true;
            }
        }
        return false;
    }
}

if (! function_exists('nav_icon')) {
    function nav_icon(string $name): string
    {
        return match ($name) {
            'chalkboard' => 'book-open',
            'video-camera' => 'film',
            'calendar-check' => 'calendar-days',
            default => $name,
        };
    }
}

if (! function_exists('is_module_enabled')) {
    function is_module_enabled(string $moduleKey): bool
    {
        $key = strtolower(str_replace([' ', '-'], '_', trim($moduleKey)));
        // If table doesn't exist or no rows, consider enabled (fresh install)
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('module_controls')) return true;
            $map = \Illuminate\Support\Facades\Cache::remember('module_controls_map', 3600, function () {
                return \App\Models\ModuleControl::pluck('is_enabled', 'module_key')->toArray();
            });
            if (empty($map)) return true;
            // If key not in map, check parent prefix (e.g. student_list -> student_details)
            if (!array_key_exists($key, $map)) return true;
            return (bool) $map[$key];
        } catch (\Throwable $e) {
            return true;
        }
    }
}

if (! function_exists('get_school_image_url')) {
    function get_school_image_url(): string
    {
        $path = get_global_setting('school_image');
        if (!empty($path) && file_exists(public_path($path))) {
            return asset($path);
        }
        // fallback to legacy default
        return asset('uploads/app_image/defualt.png');
    }
}

if (! function_exists('get_image_url')) {
    function get_image_url($role = '', $fileName = '')
    {
        if ($fileName === 'defualt.png' || empty($fileName)) {
            return get_school_image_url();
        }
        $path = 'uploads/images/'.$role.'/'.$fileName;
        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return get_school_image_url();
    }
}

if (! function_exists('slugify')) {
    function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '_', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '_');
        $text = preg_replace('~-+~', '_', $text);

        return strtolower($text);
    }
}

if (! function_exists('get_grade_info')) {
    /**
     * Compute letter grade, GPA, pass/fail status, and color code (Blue = Pass, Red = Fail)
     */
    function get_grade_info($obtainedMark, float $fullMark = 100, ?int $branchId = null, bool $isAbsent = false): array
    {
        if ($isAbsent || $obtainedMark === null || $obtainedMark === '') {
            return [
                'name' => 'ABS',
                'grade_point' => 0.0,
                'status' => 'failed',
                'color' => 'red',
                'is_pass' => false,
                'percentage' => 0.0,
                'remark' => 'Absent',
            ];
        }

        $obtained = (float) $obtainedMark;
        $percentage = $fullMark > 0 ? ($obtained / $fullMark) * 100 : $obtained;
        $percentage = round($percentage, 2);

        // Check database grade configuration
        try {
            $grades = DB::table('grade')
                ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->where('branch_id', $branchId)->orWhereNull('branch_id')))
                ->orderBy('lower_mark', 'desc')
                ->get();

            if ($grades->isNotEmpty()) {
                foreach ($grades as $g) {
                    if ($percentage >= (float)$g->lower_mark && $percentage <= (float)$g->upper_mark) {
                        $isPass = ((float)$g->grade_point > 0 && !in_array(strtoupper(trim($g->name)), ['F', 'FAIL', 'FAILED'], true));
                        return [
                            'name' => $g->name,
                            'grade_point' => (float)$g->grade_point,
                            'status' => $isPass ? 'passed' : 'failed',
                            'color' => $isPass ? 'blue' : 'red',
                            'is_pass' => $isPass,
                            'percentage' => $percentage,
                            'remark' => $g->remark ?? ($isPass ? 'Passed' : 'Failed'),
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {}

        // Standard grading scale fallback (Pass >= 50% in Blue, Fail < 50% in Red)
        if ($percentage >= 90) {
            return ['name' => 'A+', 'grade_point' => 4.0, 'status' => 'passed', 'color' => 'blue', 'is_pass' => true, 'percentage' => $percentage, 'remark' => 'Outstanding'];
        } elseif ($percentage >= 80) {
            return ['name' => 'A', 'grade_point' => 3.7, 'status' => 'passed', 'color' => 'blue', 'is_pass' => true, 'percentage' => $percentage, 'remark' => 'Excellent'];
        } elseif ($percentage >= 70) {
            return ['name' => 'B', 'grade_point' => 3.0, 'status' => 'passed', 'color' => 'blue', 'is_pass' => true, 'percentage' => $percentage, 'remark' => 'Very Good'];
        } elseif ($percentage >= 60) {
            return ['name' => 'C', 'grade_point' => 2.0, 'status' => 'passed', 'color' => 'blue', 'is_pass' => true, 'percentage' => $percentage, 'remark' => 'Good'];
        } elseif ($percentage >= 50) {
            return ['name' => 'D', 'grade_point' => 1.0, 'status' => 'passed', 'color' => 'blue', 'is_pass' => true, 'percentage' => $percentage, 'remark' => 'Satisfactory'];
        } else {
            return ['name' => 'F', 'grade_point' => 0.0, 'status' => 'failed', 'color' => 'red', 'is_pass' => false, 'percentage' => $percentage, 'remark' => 'Failed'];
        }
    }
}
