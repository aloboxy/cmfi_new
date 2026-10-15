<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data :class="theme === 'dark' ? 'dark' : ''" data-border="{{ $theme_config->border_mode ?? 'true' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="session-lifetime" content="{{ config('session.lifetime') }}">
    <title>@yield('title', translate('dashboard')) | {{ $global_config->institute_name ?? 'CMFI School Management System' }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <script>
        const dbTheme = '{{ ($theme_config->dark_skin ?? 'false') == 'true' ? 'dark' : 'light' }}';
        localStorage.setItem('theme', dbTheme);
        if (dbTheme === 'dark') document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
        if (localStorage.getItem('sidebar') === 'collapsed') document.documentElement.classList.add('sidebar-collapsed');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/modern-ui-updates.css') }}">
    @php
      $t = $theme_config ?? (object)[];
      $primary = $t->primary_color ?? '#1D4ED8';
      $sidebarBg = $t->sidebar_bg ?? '#0f2040';
      $sidebarBg2 = $t->sidebar_bg ?? '#0a1930';
      $sidebarText = $t->sidebar_text ?? '#C5D0E8';
      $sidebarIcon = $t->sidebar_icon ?? '#C5D0E8';
      $sidebarActive = $t->sidebar_active ?? '#F4A61F';
      $textDark = $t->text_dark ?? '#111827';
    @endphp
    <style>
      :root{
        --primary: {{ $primary }}; --primary-light: {{ $primary }};
        --sidebar-bg: {{ $sidebarBg }}; --sidebar-bg2: {{ $sidebarBg2 }};
        --sidebar-text: {{ $sidebarText }}; --sidebar-icon: {{ $sidebarIcon }};
        --sidebar-active: {{ $sidebarActive }}; --text-dark: {{ $textDark }};
        --card-radius: 1rem;
        --card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
      }
      .bg-primary{ background: var(--primary) !important; }
      .text-primary{ color: var(--primary) !important; }
      .border-primary{ border-color: var(--primary) !important; }
      .ring-primary{ --tw-ring-color: var(--primary) !important; }
      /* force sidebar to use theme color */
      aside.sidebar-theme{ background: var(--sidebar-bg) !important; border-color: color-mix(in srgb, var(--sidebar-bg) 85%, black) !important; }
      aside.sidebar-theme nav a, aside.sidebar-theme nav button{ color: var(--sidebar-text) !important; }
      aside.sidebar-theme nav .bg-slate-50{ background: color-mix(in srgb, var(--sidebar-bg) 92%, white) !important; }
      aside.sidebar-theme .bg-\[\#2563eb\]{ background: var(--sidebar-active) !important; }
      /* primary brand everywhere — buttons, tops, forms */
      .bg-blue-600, .bg-blue-700, .bg-indigo-600, .bg-\[\#1D4ED8\]{ background: var(--primary) !important; }
      .hover\:bg-blue-700:hover, .hover\:bg-blue-600:hover, .hover\:bg-indigo-600:hover{ background: color-mix(in srgb, var(--primary) 85%, black) !important; }
      .text-blue-600, .text-blue-500, .text-indigo-600, .text-\[\#2563eb\]{ color: var(--primary) !important; }
      .border-blue-600, .border-\[\#2563eb\], .border-blue-300{ border-color: var(--primary) !important; }
      .ring-blue-600, .ring-\[\#2563eb\], .ring-blue-500{ --tw-ring-color: var(--primary) !important; }
      .from-blue-600, .from-blue-700, .from-\[\#2563eb\]{ --tw-gradient-from: var(--primary) !important; }
      .to-indigo-600, .to-indigo-700{ --tw-gradient-to: var(--primary) !important; }
      input:focus, select:focus, textarea:focus{ border-color: var(--primary) !important; --tw-ring-color: color-mix(in srgb, var(--primary) 20%, transparent) !important; }
      header.topbar-theme{ background: rgba(255, 255, 255, 0.85) !important; backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); color: #0f172a !important; border-color: rgba(226, 232, 240, 0.8) !important; }
      header.topbar-theme .relative input{ background: #f1f5f9 !important; color: #0f172a !important; }
      header.topbar-theme .relative input::placeholder{ color: #94a3b8 !important; }
      footer.footer-theme{ background: white !important; color: #64748b !important; border-color: #e2e8f0 !important; }
      html[data-border="false"] .rounded-2xl{border-radius:0 !important}
      html[data-border="false"] .rounded-xl{border-radius:0 !important}
      html[data-border="false"] .rounded-lg{border-radius:4px !important}
      
      /* Global Modern UI Enhancements */
      .modern-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: var(--card-radius);
        box-shadow: var(--card-shadow);
        transition: all 0.25s ease;
      }
      .modern-card:hover {
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.06), 0 4px 10px -2px rgba(0, 0, 0, 0.03);
      }
      .modern-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.025em;
        text-transform: uppercase;
      }
      .modern-badge-success { background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25); }
      .modern-badge-danger { background: rgba(239, 68, 68, 0.12); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.25); }
      .modern-badge-warning { background: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.25); }
      .modern-badge-info { background: rgba(59, 130, 246, 0.12); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.25); }
      .modern-badge-purple { background: rgba(168, 85, 247, 0.12); color: #7e22ce; border: 1px solid rgba(168, 85, 247, 0.25); }

      /* Modern Button Effects */
      .modern-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.825rem;
        font-weight: 600;
        border-radius: 0.75rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
      }
      .modern-btn:active {
        transform: scale(0.97);
      }
      .modern-btn-primary {
        background: var(--primary);
        color: #ffffff;
        box-shadow: 0 4px 12px color-mix(in srgb, var(--primary) 30%, transparent);
      }
      .modern-btn-primary:hover {
        background: color-mix(in srgb, var(--primary) 88%, black);
        box-shadow: 0 6px 16px color-mix(in srgb, var(--primary) 40%, transparent);
        transform: translateY(-1px);
      }

      /* Modern Table Styles */
      .modern-table-wrapper {
        border-radius: var(--card-radius);
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        box-shadow: var(--card-shadow);
      }
      .modern-table {
        width: 100%;
        text-align: left;
        font-size: 0.8125rem;
        border-collapse: separate;
        border-spacing: 0;
      }
      .modern-table thead tr {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
      }
      .modern-table thead th {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #e2e8f0;
      }
      .modern-table tbody td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
      }
      .modern-table tbody tr:last-child td {
        border-bottom: none;
      }
      .modern-table tbody tr {
        transition: background-color 0.15s ease;
      }
      .modern-table tbody tr:hover {
        background-color: #f8fafc;
      }

      /* Modern Forms */
      .modern-input {
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 0.6rem 0.9rem;
        font-size: 0.875rem;
        color: #0f172a;
        transition: all 0.2s ease;
      }
      .modern-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 15%, transparent);
        outline: none;
      }

      aside.sidebar-theme nav span.bg-slate-100{ background: color-mix(in srgb, var(--sidebar-bg) 88%, white) !important; color: var(--sidebar-icon) !important; }
      aside.sidebar-theme nav span.bg-slate-100 svg{ color: var(--sidebar-icon) !important; }
      aside.sidebar-theme nav span.bg-white\/20{ background: color-mix(in srgb, var(--sidebar-active) 20%, white) !important; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#f8fafc] text-slate-800"
      x-data="{ sidebarOpen: false }">
<div class="min-h-screen">
    <x-layout.sidebar />
    <div class="flex min-h-screen flex-col transition-all duration-200 lg:pl-64 sidebar-collapsed:lg:pl-[4.5rem]">
        <x-layout.topbar />
        <main class="flex-1 p-4 lg:p-5">
            @yield('content')
        </main>
        <footer class="footer-theme px-6 py-3 text-center text-xs border-t" style="border-color: color-mix(in srgb, var(--primary) 85%, black)">
            {{ $global_config->institute_name ?? $global_config->footer_text ?? 'CMFI School' }} &middot; {{ date('Y') }}
        </footer>
    </div>
</div>
<x-layout.toast />
@auth
<form id="autoLogoutForm" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
<script>
(function(){
  const meta = document.querySelector('meta[name="session-lifetime"]');
  const minutes = parseInt(meta?.content || '120', 10);
  if (!minutes || minutes <= 0) return;
  const lifetime = minutes * 60 * 1000;
  const warningBefore = Math.min(60000, Math.floor(lifetime * 0.1)); // 1min or 10%
  let lastActivity = Date.now();
  let logoutTimer, warningTimer, warned = false;
  const logoutUrl = document.getElementById('autoLogoutForm')?.action;
  function doLogout(){
    const f = document.getElementById('autoLogoutForm');
    if (f) f.submit();
    else window.location.href = '{{ route('login') }}';
  }
  function schedule(){
    clearTimeout(logoutTimer); clearTimeout(warningTimer); warned = false;
    warningTimer = setTimeout(()=>{
      warned = true;
      if (confirm('{{ translate('session_expiring_extend') }}')) {
        // ping to refresh server last_activity
        fetch(window.location.href, {headers:{'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'}).catch(()=>{});
        reset();
      } else {
        doLogout();
      }
    }, Math.max(0, lifetime - warningBefore));
    logoutTimer = setTimeout(doLogout, lifetime);
  }
  function reset(){
    lastActivity = Date.now();
    schedule();
  }
  ['mousemove','keydown','click','scroll','touchstart'].forEach(ev=>{
    document.addEventListener(ev, ()=>{ if (Date.now() - lastActivity > 5000) reset(); }, {passive:true});
  });
  document.addEventListener('visibilitychange', ()=>{ if(document.visibilityState==='visible') reset(); });
  schedule();
  // also handle 401 from AJAX (session expired on server)
  const origFetch = window.fetch;
  window.fetch = function(...args){
    return origFetch.apply(this, args).then(r=>{
      if (r.status===401 && r.headers.get('content-type')?.includes('application/json')) {
        r.clone().json().then(j=>{ if(j.redirect) window.location.href=j.redirect; }).catch(()=>{});
      }
      return r;
    });
  };
})();
</script>
@endauth
@stack('scripts')
</body>
</html>
