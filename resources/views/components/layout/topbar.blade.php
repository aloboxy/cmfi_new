<header class="topbar-theme sticky top-0 z-20 flex h-[68px] items-center gap-3 border-b px-4 lg:px-6 transition-all"
        x-data="{ searchModalOpen: false, notifOpen: false, sessionOpen: false, userOpen: false }"
        @keydown.window.prevent.cmd.k="searchModalOpen = true"
        @keydown.window.prevent.ctrl.k="searchModalOpen = true">
    
    {{-- Sidebar toggle --}}
    <button type="button"
            class="hidden lg:grid size-10 place-items-center rounded-xl border border-slate-200/80 bg-white shadow-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all duration-200 shrink-0"
            @click="document.documentElement.classList.toggle('sidebar-collapsed'); localStorage.setItem('sidebar', document.documentElement.classList.contains('sidebar-collapsed') ? 'collapsed' : 'open')"
            aria-label="Toggle sidebar">
        <x-heroicon-o-bars-3 class="size-5" />
    </button>
    <button type="button" class="lg:hidden grid size-10 place-items-center rounded-xl border border-slate-200/80 bg-white shadow-sm text-slate-600 shrink-0"
            @click="sidebarOpen = true" aria-label="Open sidebar">
        <x-heroicon-o-bars-3 class="size-5" />
    </button>

    {{-- Search Trigger Pill --}}
    <div class="relative hidden sm:flex items-center flex-1 max-w-[340px] cursor-pointer" @click="searchModalOpen = true">
        <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3.5 size-4 text-slate-400" />
        <div class="w-full rounded-xl border border-slate-200/80 bg-slate-100/80 hover:bg-white hover:border-slate-300 py-2 pl-10 pr-12 text-[13px] text-slate-400 flex items-center justify-between shadow-inner transition">
            <span>{{ translate('search_here') }}...</span>
            <kbd class="hidden md:inline-flex items-center gap-0.5 rounded bg-white px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 border border-slate-200 shadow-xs">⌘K</kbd>
        </div>
    </div>

    <div class="flex-1 sm:hidden"></div>

    {{-- Right action buttons --}}
    <div class="flex items-center gap-2.5 ml-auto">
        {{-- Academic Session Switcher --}}
        <div class="relative">
            <button type="button" 
                    class="flex items-center gap-2 rounded-xl border border-slate-200/80 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition shrink-0" 
                    @click="sessionOpen = !sessionOpen" aria-label="Academic session">
                <x-heroicon-o-calendar-days class="size-4 text-blue-600" />
                <span class="hidden md:inline">{{ session('school_year') ?? '2025-2026' }}</span>
                <x-heroicon-o-chevron-down class="size-3 text-slate-400" />
            </button>
            <div x-show="sessionOpen" @click.outside="sessionOpen = false" x-transition.opacity.duration.100ms x-cloak
                 class="absolute right-0 mt-2 w-60 rounded-2xl border border-slate-200 bg-white shadow-xl py-2 text-sm z-50">
                <div class="px-4 py-1.5 border-b border-slate-100 mb-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ translate('academic_session') }}</p>
                </div>
                @foreach (DB::table('schoolyear')->orderByDesc('id')->get() as $year)
                    <a href="{{ route('sessions.set', $year->id) }}"
                       class="flex items-center justify-between px-4 py-2 hover:bg-slate-50 transition {{ (string)get_session_id() === (string)$year->id ? 'bg-blue-50/80 font-bold text-[#2563eb]' : 'text-slate-700 font-medium' }}">
                        <span>{{ $year->school_year }}</span>
                        @if ((string)get_session_id() === (string)$year->id)
                            <span class="flex size-2 rounded-full bg-blue-600"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Notifications Dropdown --}}
        <div class="relative">
            <button type="button" 
                    class="relative grid size-10 place-items-center rounded-xl border border-slate-200/80 bg-white shadow-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition shrink-0" 
                    @click="notifOpen = !notifOpen" aria-label="Notifications">
                <x-heroicon-o-bell class="size-5" />
                <span class="absolute top-2 right-2 flex size-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full size-2 bg-red-500"></span>
                </span>
            </button>
            <div x-show="notifOpen" @click.outside="notifOpen = false" x-transition.opacity.duration.150ms x-cloak
                 class="absolute right-0 mt-2 w-80 rounded-2xl border border-slate-200 bg-white shadow-2xl overflow-hidden z-50">
                <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Notifications</span>
                        <span class="rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-[10px] font-bold">3 New</span>
                    </div>
                    <button type="button" class="text-[11px] font-medium text-blue-600 hover:underline">Mark all read</button>
                </div>
                <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                    <div class="flex gap-3 px-4 py-3 hover:bg-slate-50/80 transition cursor-pointer">
                        <span class="grid size-8 place-items-center rounded-full bg-blue-100 text-blue-600 shrink-0"><x-heroicon-o-academic-cap class="size-4" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-800">New Student Registered</p>
                            <p class="text-[11px] text-slate-500 truncate">Admission #2026-089 submitted</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">10 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex gap-3 px-4 py-3 hover:bg-slate-50/80 transition cursor-pointer">
                        <span class="grid size-8 place-items-center rounded-full bg-emerald-100 text-emerald-600 shrink-0"><x-heroicon-o-banknotes class="size-4" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-800">Fee Payment Received</p>
                            <p class="text-[11px] text-slate-500 truncate">Term fee paid by Grade 5 student</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">1 hour ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fullscreen Toggle --}}
        <button type="button" 
                class="hidden sm:grid size-10 place-items-center rounded-xl border border-slate-200/80 bg-white shadow-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition shrink-0" 
                aria-label="Fullscreen"
                onclick="if(!document.fullscreenElement) document.documentElement.requestFullscreen(); else document.exitFullscreen()">
            <x-heroicon-o-arrows-pointing-out class="size-5" />
        </button>

        {{-- User Profile Chip --}}
        <div class="relative ml-1">
            <button type="button" 
                    class="flex items-center gap-2.5 rounded-2xl border border-slate-200/80 bg-white pl-1.5 pr-3 py-1.5 shadow-sm hover:bg-slate-50 hover:border-slate-300 transition" 
                    @click="userOpen = !userOpen">
                <img src="{{ get_image_url('staff', auth()->user()->profile?->photo ?? '') }}" alt=""
                     class="size-8 rounded-xl object-cover bg-slate-100 shadow-inner" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid'">
                <span class="hidden size-8 place-items-center rounded-xl bg-blue-600 text-xs font-bold text-white shadow-sm" style="display:none">
                    {{ strtoupper(substr(auth()->user()->profile?->name ?? auth()->user()->username ?? 'A', 0, 1)) }}
                </span>
                <span class="hidden sm:block text-left leading-tight">
                    <span class="block text-[13px] font-bold text-slate-800 leading-none">{{ auth()->user()->profile?->name ?? auth()->user()->username }}</span>
                    <span class="block text-[10px] font-semibold tracking-wider text-slate-400 uppercase mt-0.5">{{ translate(loggedin_role_name() ?? 'super_admin') }}</span>
                </span>
                <x-heroicon-o-chevron-down class="hidden sm:block size-3.5 text-slate-400" />
            </button>
            <div x-show="userOpen" @click.outside="userOpen = false" x-transition.opacity.duration.100ms x-cloak
                 class="absolute right-0 mt-2 w-52 rounded-2xl border border-slate-200 bg-white shadow-2xl py-2 text-sm z-50">
                <div class="px-4 py-2 border-b border-slate-100 mb-1">
                    <p class="text-xs font-bold text-slate-800">{{ auth()->user()->profile?->name ?? auth()->user()->username }}</p>
                    <p class="text-[11px] text-slate-500">{{ auth()->user()->email ?? 'admin@school.com' }}</p>
                </div>
                <a href="#" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 text-slate-700 font-medium">
                    <x-heroicon-o-user-circle class="size-4 text-slate-400" /> {{ translate('profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-red-600 font-medium hover:bg-red-50 transition">
                        <x-heroicon-o-arrow-right-on-rectangle class="size-4 text-red-500" /> {{ translate('logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Spotlight Command Palette Modal --}}
    <div x-show="searchModalOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-start justify-center pt-20 p-4" @click.self="searchModalOpen = false" x-cloak>
        <div class="w-full max-w-xl rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden" @click.outside="searchModalOpen = false">
            <div class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-100 bg-slate-50/50">
                <x-heroicon-o-magnifying-glass class="size-5 text-slate-400" />
                <input type="text" placeholder="Type a command or jump to page (Students, Attendance, Fees, Exams)..." 
                       class="w-full bg-transparent border-0 text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none" autofocus />
                <span class="text-xs text-slate-400 font-mono">ESC</span>
            </div>
            <div class="p-2 divide-y divide-slate-100 max-h-80 overflow-y-auto">
                <div class="p-2">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-2">Quick Navigation</p>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                        <x-heroicon-o-squares-2x2 class="size-4 text-blue-500" /> Dashboard Overview
                    </a>
                    <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                        <x-heroicon-o-academic-cap class="size-4 text-emerald-500" /> Student Directory & Admissions
                    </a>
                    <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                        <x-heroicon-o-banknotes class="size-4 text-amber-500" /> Accounting & Fees Management
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
