@props(['label' => '', 'value' => '0', 'sublabel' => '', 'icon' => 'academic-cap', 'gradient' => 'from-blue-700 to-blue-600', 'delta' => ''])

<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $gradient }} p-5 text-white shadow-sm">
    <div class="relative z-10 flex gap-4">
        <div class="grid size-12 shrink-0 place-items-center rounded-xl bg-white/20 backdrop-blur text-white">
            <x-dynamic-component :component="'heroicon-o-'.nav_icon($icon)" class="size-6" />
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-2xl font-extrabold leading-none tracking-tight">{{ $value }}</p>
            <p class="mt-1 text-[11px] font-bold tracking-widest uppercase text-white/90">{{ $label }}</p>
            <p class="text-[11px] text-white/70">{{ $sublabel }}</p>
        </div>
    </div>
    {{-- watermark --}}
    <x-dynamic-component :component="'heroicon-o-'.nav_icon($icon)" class="pointer-events-none absolute -bottom-3 -right-3 size-28 text-white/10" />
</div>
