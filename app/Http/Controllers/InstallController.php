<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class InstallController extends Controller
{
    public function index(Request $request)
    {
        if ($this->isInstalled() && !$request->has('force')) {
            return redirect('/')->with('info', 'System already installed');
        }

        $step = (int) $request->input('step', 1);
        $step = max(1, min(4, $step));

        // Handle POST for each step
        if ($request->isMethod('post')) {
            $postStep = (int) $request->input('step', 1);

            if ($postStep === 2) {
                // Requirements are just informational, proceed to DB
                return redirect()->route('install.index', ['step' => 3]);
            }

            if ($postStep === 3) {
                // Database step
                $request->validate([
                    'db_connection' => 'required|in:sqlite,mysql',
                    'db_host' => 'nullable|required_if:db_connection,mysql|string',
                    'db_port' => 'nullable|numeric',
                    'db_database' => 'nullable|required_if:db_connection,mysql|string',
                    'db_username' => 'nullable|required_if:db_connection,mysql|string',
                    'db_password' => 'nullable|string',
                ]);

                $connection = $request->input('db_connection');
                try {
                    if ($connection === 'mysql') {
                        $host = $request->input('db_host', '127.0.0.1');
                        $port = $request->input('db_port', '3306');
                        $database = $request->input('db_database');
                        $username = $request->input('db_username');
                        $password = $request->input('db_password', '');

                        // Test connection
                        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
                        $pdo = new \PDO($dsn, $username, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
                        $pdo = null;

                        $this->writeEnv([
                            'DB_CONNECTION' => 'mysql',
                            'DB_HOST' => $host,
                            'DB_PORT' => $port,
                            'DB_DATABASE' => $database,
                            'DB_USERNAME' => $username,
                            'DB_PASSWORD' => $password,
                        ]);
                    } else {
                        // SQLite
                        $dbPath = database_path('database.sqlite');
                        if (!File::exists($dbPath)) {
                            File::put($dbPath, '');
                        }
                        $this->writeEnv([
                            'DB_CONNECTION' => 'sqlite',
                            'DB_DATABASE' => $dbPath,
                        ]);
                        // Clear DB related env
                        $this->writeEnv([
                            'DB_HOST' => '127.0.0.1',
                            'DB_PORT' => '3306',
                            'DB_USERNAME' => '',
                            'DB_PASSWORD' => '',
                        ], false);
                    }

                    // Clear config cache
                    Artisan::call('config:clear');
                    // Test again with Laravel's config
                    DB::purge();
                    DB::connection()->getPdo();

                    return redirect()->route('install.index', ['step' => 4]);
                } catch (\Throwable $e) {
                    return back()->withInput()->withErrors(['db_error' => 'Database connection failed: ' . $e->getMessage()])->with('step', 3);
                }
            }

            if ($postStep === 4) {
                $request->validate([
                    'school_name' => 'required|string|max:255',
                    'admin_name' => 'required|string|max:255',
                    'admin_email' => 'required|email',
                    'admin_password' => 'required|string|min:6|confirmed',
                    'timezone' => 'required|string',
                ]);

                try {
                    // Ensure APP_KEY exists
                    if (empty(config('app.key'))) {
                        Artisan::call('key:generate', ['--force' => true]);
                    }

                    // Run migrations fresh
                    Artisan::call('migrate', ['--force' => true]);

                    // Seed is handled via DatabaseSeeder or manual
                    $schoolName = $request->input('school_name');
                    $timezone = $request->input('timezone');
                    $adminName = $request->input('admin_name');
                    $adminEmail = $request->input('admin_email');
                    $adminPassword = $request->input('admin_password');

                    // Update global_settings
                    if (Schema::hasTable('global_settings')) {
                        $exists = DB::table('global_settings')->where('id', 1)->exists();
                        $data = [
                            'institute_name' => $schoolName,
                            'timezone' => $timezone,
                            'updated_at' => now(),
                        ];
                        if ($exists) {
                            DB::table('global_settings')->where('id', 1)->update($data);
                        } else {
                            $data['id'] = 1;
                            $data['institution_code'] = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $schoolName), 0, 4)) ?: 'CMFI';
                            $data['reg_prefix'] = '';
                            $data['institute_email'] = $adminEmail;
                            $data['address'] = '';
                            $data['mobileno'] = '';
                            $data['currency'] = 'USD';
                            $data['currency_symbol'] = '$';
                            $data['sms_service_provider'] = 'none';
                            $data['session_id'] = 1;
                            $data['translation'] = 'english';
                            $data['footer_text'] = $schoolName . ' Management System';
                            $data['animations'] = '1';
                            $data['date_format'] = 'Y-m-d';
                            $data['facebook_url'] = '';
                            $data['twitter_url'] = '';
                            $data['linkedin_url'] = '';
                            $data['youtube_url'] = '';
                            $data['created_at'] = now();
                            DB::table('global_settings')->insert($data);
                        }
                    }

                    // Ensure theme_settings exists
                    if (Schema::hasTable('theme_settings') && !DB::table('theme_settings')->where('id', 1)->exists()) {
                        DB::table('theme_settings')->insert(['id' => 1, 'border_mode' => 'true', 'dark_skin' => 'false', 'created_at' => now(), 'updated_at' => now()]);
                    }

                    // Create branch for school
                    $branchId = 1;
                    if (Schema::hasTable('branch')) {
                        $branchExists = DB::table('branch')->where('id', 1)->exists();
                        if (!$branchExists) {
                            $branchId = DB::table('branch')->insertGetId([
                                'name' => 'Main Branch',
                                'school_name' => $schoolName,
                                'email' => $adminEmail,
                                'mobileno' => '',
                                'currency' => 'USD',
                                'symbol' => '$',
                                'city' => '',
                                'state' => '',
                                'address' => '',
                                'use_section' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    // Ensure schoolyear
                    if (Schema::hasTable('schoolyear') && !DB::table('schoolyear')->where('id', 1)->exists()) {
                        $year = date('Y') . '-' . (date('Y') + 1);
                        DB::table('schoolyear')->insert(['id' => 1, 'school_year' => $year, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()]);
                    }

                    // Ensure roles
                    if (Schema::hasTable('roles') && DB::table('roles')->count() === 0) {
                        $roles = ['System Admin','Admin','Teacher','Accountant','Librarian','Parent','Student'];
                        foreach ($roles as $i => $r) {
                            DB::table('roles')->insert(['id' => $i+1, 'name' => $r, 'prefix' => strtolower($r), 'is_system' => '1']);
                        }
                    }

                    // Create super admin staff + login
                    $staffId = null;
                    if (Schema::hasTable('staff')) {
                        // Check if admin email already exists
                        $existingStaff = DB::table('staff')->where('email', $adminEmail)->first();
                        if ($existingStaff) {
                            $staffId = $existingStaff->id;
                        } else {
                            // Ensure department/designation exist
                            if (Schema::hasTable('staff_department') && !DB::table('staff_department')->where('id', 1)->exists()) {
                                DB::table('staff_department')->insert(['id' => 1, 'name' => 'Administration', 'branch_id' => $branchId, 'created_at' => now(), 'updated_at' => now()]);
                            }
                            if (Schema::hasTable('staff_designation') && !DB::table('staff_designation')->where('id', 1)->exists()) {
                                DB::table('staff_designation')->insert(['id' => 1, 'name' => 'System Administrator', 'branch_id' => 1, 'created_at' => now(), 'updated_at' => now()]);
                            }
                            $staffId = DB::table('staff')->insertGetId([
                                'staff_id' => 'EMP-'.str_pad((string) rand(1,999), 3, '0', STR_PAD_LEFT),
                                'branch_id' => $branchId,
                                'name' => $adminName,
                                'department' => 1,
                                'qualification' => 'N/A',
                                'designation' => 1,
                                'joining_date' => date('Y-m-d'),
                                'birthday' => '1990-01-01',
                                'sex' => 'Male',
                                'religion' => 'N/A',
                                'blood_group' => 'N/A',
                                'photo' => 'defualt.png',
                                'mobileno' => '',
                                'email' => $adminEmail,
                                'present_address' => '-',
                                'permanent_address' => '-',
                                'hash' => hash('sha256', uniqid('', true)),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    if ($staffId && Schema::hasTable('login_credential')) {
                        $exists = DB::table('login_credential')->where('username', $adminEmail)->exists();
                        if (!$exists) {
                            DB::table('login_credential')->insert([
                                'user_id' => $staffId,
                                'username' => $adminEmail,
                                'password' => Hash::make($adminPassword),
                                'role' => 1,
                                'active' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            DB::table('login_credential')->where('username', $adminEmail)->update(['password' => Hash::make($adminPassword), 'active' => 1]);
                        }
                    }

                    // Mark as installed
                    File::put(storage_path('installed'), date('Y-m-d H:i:s'));
                    File::put(storage_path('framework/installed'), date('Y-m-d H:i:s'));

                    // Clear caches
                    Artisan::call('config:clear');
                    Artisan::call('view:clear');
                    Artisan::call('cache:clear');

                    return redirect()->route('install.index', ['step' => 5]);
                } catch (\Throwable $e) {
                    return back()->withInput()->withErrors(['install_error' => 'Installation failed: ' . $e->getMessage()])->with('step', 4);
                }
            }
        }

        $requirements = $this->checkRequirements();
        $allOk = collect($requirements)->every(fn($r) => $r['ok']);

        return view('install.index', [
            'step' => $step,
            'requirements' => $requirements,
            'allOk' => $allOk,
            'isInstalled' => $this->isInstalled(),
            'timezones' => $this->timezoneList(),
        ]);
    }

    public function isInstalled(): bool
    {
        if (File::exists(storage_path('installed')) || File::exists(storage_path('framework/installed'))) {
            return true;
        }
        try {
            if (Schema::hasTable('global_settings') && DB::table('global_settings')->where('id', 1)->exists()) {
                return true;
            }
        } catch (\Throwable $e) {}
        return false;
    }

    private function checkRequirements(): array
    {
        $checks = [];
        $checks[] = ['label' => 'PHP Version >= 8.2', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>='), 'current' => PHP_VERSION];
        $checks[] = ['label' => 'BCMath', 'ok' => extension_loaded('bcmath'), 'current' => extension_loaded('bcmath') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'Ctype', 'ok' => extension_loaded('ctype'), 'current' => extension_loaded('ctype') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'Fileinfo', 'ok' => extension_loaded('fileinfo'), 'current' => extension_loaded('fileinfo') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'JSON', 'ok' => extension_loaded('json'), 'current' => extension_loaded('json') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'Mbstring', 'ok' => extension_loaded('mbstring'), 'current' => extension_loaded('mbstring') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'OpenSSL', 'ok' => extension_loaded('openssl'), 'current' => extension_loaded('openssl') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'PDO', 'ok' => extension_loaded('pdo'), 'current' => extension_loaded('pdo') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'Tokenizer', 'ok' => extension_loaded('tokenizer'), 'current' => extension_loaded('tokenizer') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'XML', 'ok' => extension_loaded('xml'), 'current' => extension_loaded('xml') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'Curl', 'ok' => extension_loaded('curl'), 'current' => extension_loaded('curl') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'GD', 'ok' => extension_loaded('gd'), 'current' => extension_loaded('gd') ? 'enabled' : 'missing'];
        $checks[] = ['label' => 'Storage writable', 'ok' => is_writable(storage_path()), 'current' => is_writable(storage_path()) ? 'writable' : 'not writable'];
        $checks[] = ['label' => 'Bootstrap Cache writable', 'ok' => is_writable(base_path('bootstrap/cache')), 'current' => is_writable(base_path('bootstrap/cache')) ? 'writable' : 'not writable'];
        $checks[] = ['label' => '.env writable', 'ok' => is_writable(base_path('.env')) || !File::exists(base_path('.env')), 'current' => is_writable(base_path('.env')) ? 'writable' : 'not writable'];
        return $checks;
    }

    private function writeEnv(array $data, bool $replace = true): void
    {
        $envPath = base_path('.env');
        $content = File::exists($envPath) ? File::get($envPath) : '';
        foreach ($data as $key => $value) {
            // Escape value if contains spaces or #
            $quoted = preg_match('/\s|#/', $value) ? '"'.str_replace('"', '\"', $value).'"' : $value;
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$quoted}", $content);
            } else {
                $content .= PHP_EOL . "{$key}={$quoted}";
            }
        }
        File::put($envPath, $content);
    }

    private function timezoneList(): array
    {
        $list = [];
        $offsets = [];
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        foreach (\DateTimeZone::listIdentifiers() as $tz) {
            $now->setTimezone(new \DateTimeZone($tz));
            $offsets[] = $offset = $now->getOffset();
            $list[$tz] = '(' . $this->formatGmtOffset($offset) . ') ' . str_replace(['/', '_'], [', ', ' '], $tz);
        }
        array_multisort($offsets, $list);
        return $list;
    }

    private function formatGmtOffset($offset): string
    {
        $hours = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));
        return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }
}
