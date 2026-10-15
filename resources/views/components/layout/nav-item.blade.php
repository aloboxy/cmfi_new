@php
    $routeName = $item['route'] ?? null;
    $routeParams = $item['route_params'] ?? [];
    $href = $routeName && Route::has($routeName) ? route($routeName, $routeParams) : '#';
    $isPlaceholder = ! ($routeName && Route::has($routeName));
    $currentName = request()->route()?->getName();
    $active = false;
    if ($routeName && ! $isPlaceholder && $currentName) {
        if ($currentName === $routeName) {
            $active = true;
        } else {
            // Only treat related CRUD pairs as same (create<->store, edit<->update, index<->view)
            $groups = [['create','store'],['edit','update'],['index','view','show'],['delete','destroy'],['save','store']];
            $routeSuffix = substr(strrchr($routeName, '.'), 1) ?: '';
            $currentSuffix = substr(strrchr($currentName, '.'), 1) ?: '';
            $routeBase = preg_replace('/\.(create|store|edit|update|destroy|delete|save|index|show|view)$/', '', $routeName);
            $currentBase = preg_replace('/\.(create|store|edit|update|destroy|delete|save|index|show|view)$/', '', $currentName);
            if ($routeBase && $routeBase === $currentBase) {
                foreach ($groups as $g) {
                    if (in_array($routeSuffix, $g, true) && in_array($currentSuffix, $g, true)) { $active = true; break; }
                }
            }
        }
    }
@endphp

@if (isset($item['children']))
    @php $isActive = nav_is_active($item); $openDefault = $isActive; @endphp
    <div x-data="{ open: {{ $openDefault ? 'true' : 'false' }} }" class="space-y-1">
        <button @click="open = !open" type="button"
                class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-semibold transition-all duration-200
                       {{ $isActive ? 'bg-white/15 text-white shadow-sm backdrop-blur' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <span class="grid size-7 place-items-center rounded-lg shrink-0 {{ $isActive ? 'bg-white/25 text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:text-white group-hover:bg-white/10' }} transition">
                <x-dynamic-component :component="'heroicon-o-'.nav_icon($item['icon'] ?? 'rectangle-group')" class="size-4" />
            </span>
            <span class="flex-1 text-left truncate sidebar-collapsed:hidden">{{ translate($item['label']) }}</span>
            <span class="grid size-5 place-items-center rounded-md text-slate-400 group-hover:text-white sidebar-collapsed:hidden transition">
                <x-heroicon-o-chevron-right class="size-3.5 transition-transform duration-200" x-bind:class="open ? 'rotate-90' : ''" />
            </span>
        </button>
        <div x-show="open" x-collapse x-cloak class="mt-1 space-y-0.5 rounded-xl bg-black/20 p-1.5 sidebar-collapsed:hidden">
            @foreach ($item['children'] as $child)
                @if (nav_visible($child))
                    @include('components.layout.nav-item', ['item' => $child])
                @endif
            @endforeach
        </div>
    </div>
@else
    <a href="{{ $href }}" @if($isPlaceholder) title="Coming soon" @endif
       class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-semibold transition-all duration-200
              {{ $active
                  ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-md'
                  : ($isPlaceholder ? 'text-slate-400 hover:bg-white/5 hover:text-slate-300' : 'text-slate-300 hover:bg-white/10 hover:text-white') }}">
        @if(isset($item['icon']))
            <span class="grid size-7 place-items-center rounded-lg shrink-0 {{ $active ? 'bg-white/20 text-white' : 'bg-white/5 text-slate-400 group-hover:text-white' }} transition">
                <x-dynamic-component :component="'heroicon-o-'.nav_icon($item['icon'])" class="size-4" />
            </span>
        @endif
        <span class="flex-1 truncate sidebar-collapsed:hidden">{{ translate($item['label']) }}</span>
        @if($active)
            <span class="size-1.5 rounded-full bg-white shadow-xs shrink-0 sidebar-collapsed:hidden"></span>
        @endif
    </a>
@endif
