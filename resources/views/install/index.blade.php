<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Install — CMFI School</title>
@vite(['resources/css/app.css'])
<style>[x-cloak]{display:none!important}</style>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="min-h-screen bg-[#f1f5f9] flex items-center justify-center p-4">
<div class="w-full max-w-3xl">
  <div class="text-center mb-6">
    <div class="mx-auto grid size-12 place-items-center rounded-2xl bg-[#1D4ED8] text-white text-xl font-bold">CM</div>
    <h1 class="mt-3 text-2xl font-bold text-slate-900">CMFI School Management — Installer</h1>
    <p class="text-sm text-slate-500">Flawless setup in 4 steps</p>
  </div>

  {{-- Progress --}}
  @php $s = (int) request('step', 1); @endphp
  <div class="flex items-center justify-between mb-6 bg-white rounded-2xl border p-4 shadow-sm">
    @foreach([1=>'Welcome',2=>'Requirements',3=>'Database',4=>'School & Admin',5=>'Finish'] as $i=>$label)
      <div class="flex items-center gap-2 flex-1 {{ $i<5 ? 'after:flex-1 after:h-0.5 after:bg-slate-200' : '' }} {{ $s>=$i ? 'after:bg-[#1D4ED8]' : '' }}">
        <span class="grid size-7 place-items-center rounded-full text-xs font-bold {{ $s>=$i ? 'bg-[#1D4ED8] text-white' : 'bg-slate-100 text-slate-500' }}">{{ $i }}</span>
        <span class="hidden sm:block text-xs font-semibold {{ $s>=$i ? 'text-[#1D4ED8]' : 'text-slate-500' }}">{{ $label }}</span>
      </div>
    @endforeach
  </div>

  @if($errors->any())
    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">
      @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
      @if($errors->has('db_error'))<p class="font-mono text-xs mt-1">{{ $errors->first('db_error') }}</p>@endif
      @if($errors->has('install_error'))<p class="font-mono text-xs mt-1">{{ $errors->first('install_error') }}</p>@endif
    </div>
  @endif
  @if(session('info'))<div class="mb-4 rounded-xl bg-blue-50 border border-blue-200 p-3 text-sm text-blue-700">{{ session('info') }}</div>@endif

  <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    {{-- Step 1 --}}
    @if($s===1)
      <div class="p-6 sm:p-8 text-center">
        <h2 class="text-lg font-bold">Welcome</h2>
        <p class="text-sm text-slate-500 mt-2">This wizard will guide you through the installation. It will check requirements, configure the database, and create your super admin.</p>
        <ul class="text-left text-sm text-slate-600 mt-6 space-y-2 max-w-md mx-auto list-disc pl-5">
          <li>PHP 8.2+ with required extensions</li>
          <li>Writable <code>storage/</code> and <code>bootstrap/cache/</code></li>
          <li>MySQL 5.7+/8 or SQLite</li>
          <li>Takes &lt; 2 minutes</li>
        </ul>
        <form method="POST" action="{{ route('install.index') }}" class="mt-6">@csrf<input type="hidden" name="step" value="2"><button class="rounded-xl bg-[#1D4ED8] text-white px-6 py-2.5 text-sm font-semibold">Start Installation — Check Requirements</button></form>
        @if($isInstalled)<p class="text-xs text-amber-600 mt-3">System appears already installed. <a href="{{ route('install.index', ['step'=>2,'force'=>1]) }}" class="underline">Force reinstall</a> or <a href="/" class="underline">go to login</a>.</p>@endif
      </div>
    @endif

    {{-- Step 2: Requirements --}}
    @if($s===2)
      <div class="p-6">
        <h2 class="text-lg font-bold">Requirements</h2>
        <div class="mt-4 divide-y border rounded-xl overflow-hidden">
          @foreach($requirements as $r)
            <div class="flex items-center justify-between px-4 py-2.5 text-sm {{ $r['ok'] ? 'bg-white' : 'bg-red-50' }}">
              <span class="font-medium">{{ $r['label'] }}</span>
              <span class="flex items-center gap-2"><span class="text-xs {{ $r['ok'] ? 'text-emerald-600' : 'text-red-600' }}">{{ $r['current'] }}</span><span class="grid size-5 place-items-center rounded-full {{ $r['ok'] ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">{{ $r['ok'] ? '✓' : '✕' }}</span></span>
            </div>
          @endforeach
        </div>
        <form method="POST" action="{{ route('install.index') }}" class="mt-6 flex justify-between">@csrf<input type="hidden" name="step" value="2"><a href="{{ route('install.index', ['step'=>1]) }}" class="rounded-xl border px-4 py-2 text-sm">Back</a><button class="rounded-xl bg-[#1D4ED8] text-white px-6 py-2.5 text-sm font-semibold {{ $allOk ? '' : 'opacity-50 pointer-events-none' }}">{{ $allOk ? 'Requirements OK — Configure Database' : 'Fix requirements to continue' }}</button></form>
      </div>
    @endif

    {{-- Step 3: Database --}}
    @if($s===3)
      <div class="p-6">
        <h2 class="text-lg font-bold">Database</h2>
        <p class="text-sm text-slate-500">Choose SQLite (zero-config, ideal for testing) or MySQL for production.</p>
        <form method="POST" action="{{ route('install.index') }}" class="mt-4 space-y-4" x-data="{ conn: '{{ old('db_connection', 'sqlite') }}' }">@csrf<input type="hidden" name="step" value="3">
          <div><label class="block text-xs font-semibold mb-1">Connection</label><select name="db_connection" x-model="conn" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"><option value="sqlite">SQLite (recommended for quick start)</option><option value="mysql">MySQL</option></select></div>
          <div x-show="conn==='mysql'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border">
            <div><label class="block text-xs font-semibold mb-1">Host</label><input name="db_host" value="{{ old('db_host','127.0.0.1') }}" placeholder="127.0.0.1" class="w-full rounded-xl border px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-semibold mb-1">Port</label><input name="db_port" value="{{ old('db_port','3306') }}" class="w-full rounded-xl border px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-semibold mb-1">Database *</label><input name="db_database" value="{{ old('db_database') }}" placeholder="cmfi_db" class="w-full rounded-xl border px-3 py-2 text-sm" :required="conn==='mysql'"></div>
            <div><label class="block text-xs font-semibold mb-1">Username *</label><input name="db_username" value="{{ old('db_username') }}" class="w-full rounded-xl border px-3 py-2 text-sm" :required="conn==='mysql'"></div>
            <div class="sm:col-span-2"><label class="block text-xs font-semibold mb-1">Password</label><input type="password" name="db_password" class="w-full rounded-xl border px-3 py-2 text-sm"></div>
          </div>
          <div x-show="conn==='sqlite'" class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">SQLite uses a file at <code>database/database.sqlite</code> — no credentials needed. Click Next to test and save.</div>
          <div class="flex justify-between"><a href="{{ route('install.index', ['step'=>2]) }}" class="rounded-xl border px-4 py-2 text-sm">Back</a><button class="rounded-xl bg-[#1D4ED8] text-white px-6 py-2.5 text-sm font-semibold">Test &amp; Save — Next</button></div>
        </form>
      </div>
    @endif

    {{-- Step 4: School & Admin --}}
    @if($s===4)
      <div class="p-6">
        <h2 class="text-lg font-bold">School &amp; Super Admin</h2>
        <p class="text-sm text-slate-500">This creates the first school/branch and the super admin who can manage everything.</p>
        <form method="POST" action="{{ route('install.index') }}" class="mt-4 space-y-4">@csrf<input type="hidden" name="step" value="4">
          <div><label class="block text-xs font-semibold mb-1">School Name *</label><input name="school_name" value="{{ old('school_name', 'CMFI School') }}" required class="w-full rounded-xl border px-3 py-2.5 text-sm"></div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold mb-1">Admin Name *</label><input name="admin_name" value="{{ old('admin_name','System Admin') }}" required class="w-full rounded-xl border px-3 py-2.5 text-sm"></div>
            <div><label class="block text-xs font-semibold mb-1">Timezone *</label><select name="timezone" class="w-full rounded-xl border px-3 py-2.5 text-sm" required><option value="">Select</option>@foreach($timezones as $k=>$v)<option value="{{ $k }}" @selected(old('timezone','UTC')==$k)>{{ $v }}</option>@endforeach</select></div>
          </div>
          <div><label class="block text-xs font-semibold mb-1">Admin Email *</label><input type="email" name="admin_email" value="{{ old('admin_email') }}" required class="w-full rounded-xl border px-3 py-2.5 text-sm"></div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold mb-1">Password *</label><input type="password" name="admin_password" required class="w-full rounded-xl border px-3 py-2.5 text-sm"></div>
            <div><label class="block text-xs font-semibold mb-1">Confirm *</label><input type="password" name="admin_password_confirmation" required class="w-full rounded-xl border px-3 py-2.5 text-sm"></div>
          </div>
          <div class="flex justify-between"><a href="{{ route('install.index', ['step'=>3]) }}" class="rounded-xl border px-4 py-2 text-sm">Back</a><button class="rounded-xl bg-[#1D4ED8] text-white px-6 py-2.5 text-sm font-semibold">Create School &amp; Admin — Install</button></div>
        </form>
      </div>
    @endif

    {{-- Step 5: Finish --}}
    @if($s===5)
      <div class="p-8 text-center">
        <div class="mx-auto grid size-14 place-items-center rounded-full bg-emerald-500 text-white text-xl">✓</div>
        <h2 class="mt-3 text-lg font-bold">Installed flawlessly!</h2>
        <p class="text-sm text-slate-500 mt-1">Your school is ready. Log in with the super admin you just created.</p>
        <div class="mt-6 flex justify-center gap-3"><a href="/" class="rounded-xl bg-[#1D4ED8] text-white px-6 py-2.5 text-sm font-semibold">Go to Login</a><a href="{{ route('install.index', ['step'=>1,'force'=>1]) }}" class="rounded-xl border px-4 py-2.5 text-sm">Reinstall</a></div>
        <p class="text-xs text-slate-400 mt-4">For security, you may delete <code>ramom.sql</code> from the project root.</p>
      </div>
    @endif
  </div>
  <p class="text-center text-xs text-slate-400 mt-4">CMFI • Laravel 12 • SQLite/MySQL • Flawless installer</p>
</div>
</body>
</html>
