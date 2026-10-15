@php
    $alerts = collect([
        'success' => session('alert-message-success'),
        'error' => session('alert-message-error'),
        'info' => session('alert-message-info'),
    ])->filter();
@endphp

@if ($alerts->isNotEmpty())
    <div class="fixed bottom-4 right-4 z-50 space-y-2 w-80 max-w-[calc(100vw-2rem)]" x-data="{ shown: true }">
        @foreach ($alerts as $type => $message)
            <div x-show="shown" x-transition.opacity.duration.300ms
                 x-init="setTimeout(() => shown = false, 6000)"
                 class="flex items-start gap-3 rounded-xl px-4 py-3 text-sm shadow-lg ring-1
                        {{ $type === 'success'
                            ? 'bg-green-50 text-green-800 ring-green-200 dark:bg-green-950 dark:text-green-200 dark:ring-green-900'
                            : ($type === 'error'
                                ? 'bg-red-50 text-red-800 ring-red-200 dark:bg-red-950 dark:text-red-200 dark:ring-red-900'
                                : 'bg-blue-50 text-blue-800 ring-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:ring-blue-900') }}">
                @if ($type === 'success')
                    <x-heroicon-o-check-circle class="size-5 shrink-0" />
                @elseif ($type === 'error')
                    <x-heroicon-o-exclamation-triangle class="size-5 shrink-0" />
                @else
                    <x-heroicon-o-information-circle class="size-5 shrink-0" />
                @endif
                <span class="flex-1">{{ $message }}</span>
                <button type="button" @click="shown = false" class="opacity-50 hover:opacity-100">&times;</button>
            </div>
        @endforeach
    </div>
@endif
