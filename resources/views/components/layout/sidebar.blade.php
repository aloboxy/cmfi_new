@php
    use Illuminate\Support\Facades\DB;

    $branches = is_superadmin_loggedin()
        ? DB::table('branch')->select('id', 'name')->orderBy('name')->get()
        : collect();
@endphp

<aside class="sidebar-theme fixed inset-y-0 left-0 z-30 flex w-64 flex-col border-r border-slate-200 dark:border-slate-800
              -translate-x-full lg:translate-x-0 sidebar-collapsed:lg:w-[4.5rem] sidebar-collapsed:lg:translate-x-0 transition-all duration-200"
       :class="sidebarOpen ? 'translate-x-0' : ''">
    {{-- School header — shows uploaded school image as logo --}}
    @php
      $sbBg = $theme_config->sidebar_bg ?? '#0f2040';
      $sbText = $theme_config->sidebar_text ?? '#C5D0E8';
      $sbIcon = $theme_config->sidebar_icon ?? '#C5D0E8';
      $schoolImg = $global_config->school_image ?? null;
      $hasSchoolImg = !empty($schoolImg) && file_exists(public_path($schoolImg));
      $logoSrc = $hasSchoolImg ? asset($schoolImg) : (file_exists(public_path('uploads/app_image/logo-small.png')) ? asset('uploads/app_image/logo-small.png') : null);
    @endphp
    <div class="flex h-[64px] items-center gap-3 px-4 shrink-0 border-b border-white/10" style="background: {{ $sbBg }}">
        @if($logoSrc)
          <img src="{{ $logoSrc }}" alt="School Logo" class="size-9 rounded-xl object-cover shrink-0 bg-white p-1 shadow-sm">
        @else
          <span class="grid size-9 place-items-center rounded-xl bg-white/10 text-white shrink-0" style="color: {{ $sbBg }}; background: color-mix(in srgb, {{ $sbBg }} 15%, white)">
              <x-heroicon-o-academic-cap class="size-5" style="color: {{ $sbBg }}" />
          </span>
        @endif
        <div class="leading-tight truncate sidebar-collapsed:hidden">
            <p class="text-[13.5px] font-bold tracking-tight truncate" style="color: {{ $sbText }}">{{ $global_config->institute_name ?? 'Lib Image School Of Test' }}</p>
            <p class="text-[10px] font-semibold uppercase tracking-wider truncate opacity-80" style="color: {{ $sbText }}">{{ translate('management_portal') }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-2.5 py-3 space-y-1.5 text-sm scrollbar-thin">
        @foreach (config('navigation', []) as $item)
            @continue(! nav_visible($item))
            @include('components.layout.nav-item', ['item' => $item])

            @if ($item['label'] === 'dashboard' && is_superadmin_loggedin() && $branches->isNotEmpty())
                <div x-data="{ open: true }" class="mt-1 rounded-xl bg-slate-50 dark:bg-slate-900/40 p-1.5 space-y-0.5 sidebar-collapsed:hidden">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-[13px] font-semibold transition {{ request()->routeIs('dashboard') && !request()->has('school_id') ? 'bg-[#2563eb] text-white shadow' : 'text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' }}">
                        <span class="size-1.5 rounded-full bg-current shrink-0"></span>
                        <span class="flex-1 truncate">{{ translate('all_branches') }}</span>
                    </a>
                    @foreach ($branches as $b)
                        @php $isActive = (string)request()->query('school_id') === (string)$b->id && request()->routeIs('dashboard'); @endphp
                        <a href="{{ route('dashboard', ['school_id' => $b->id]) }}"
                           class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-[13px] transition {{ $isActive ? 'bg-[#2563eb] text-white shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-800' }}">
                            <span class="size-1.5 rounded-full {{ $isActive ? 'bg-white' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                            <span class="flex-1 truncate">{{ $b->name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach
    </nav>


</aside>

<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/40 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-cloak></div>
<style>[x-cloak]{display:none!important}</style>
