@extends('layouts.app')
@section('title', translate('module_control'))
@section('content')
<div class="max-w-5xl mx-auto space-y-5">
  <div class="flex items-center gap-2 text-[13px] text-slate-500">
    <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
    <span class="text-slate-300">/</span><span class="font-semibold text-slate-800 dark:text-white">{{ translate('module_control') }}</span>
    <span class="ml-auto flex gap-2">
      <form method="POST" action="{{ route('settings.modules.bulk') }}" class="inline">@csrf<input type="hidden" name="is_enabled" value="1"><button class="rounded-xl bg-emerald-600 text-white px-3 py-1.5 text-xs font-semibold">Enable All</button></form>
      <form method="POST" action="{{ route('settings.modules.bulk') }}" class="inline">@csrf<input type="hidden" name="is_enabled" value="0"><button class="rounded-xl bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold">Disable All</button></form>
    </span>
  </div>

  <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
      <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2"><x-heroicon-o-adjustments-horizontal class="size-5 text-[var(--primary)]"/> {{ translate('module_control') }}</h3>
      <span class="text-xs text-slate-500">Super Admin can enable/disable any module. Disabled modules are hidden from sidebar and blocked.</span>
    </div>

    <div class="divide-y divide-slate-100 dark:divide-slate-800">
      @forelse($parents as $parent)
        @php $children = $grouped->get($parent->module_key, collect()); @endphp
        <div class="p-4 {{ $children->isNotEmpty() ? 'bg-slate-50/50 dark:bg-slate-800/20' : '' }}">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="grid size-8 place-items-center rounded-xl bg-[var(--primary)]/10 text-[var(--primary)]"><x-heroicon-o-{{ $parent->icon ?? 'squares-2x2' }} class="size-4" /></span>
              <div>
                <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $parent->display_name }}</p>
                <p class="text-xs text-slate-400 font-mono">{{ $parent->module_key }}</p>
              </div>
            </div>
            <form method="POST" action="{{ route('settings.modules.toggle') }}" class="module-toggle-form">@csrf<input type="hidden" name="module_key" value="{{ $parent->module_key }}">
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer module-toggle" data-key="{{ $parent->module_key }}" @checked($parent->is_enabled)>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-blue-500/20 rounded-full peer peer-checked:bg-blue-600 transition"></div>
                <div class="absolute left-0.5 top-0.5 bg-white size-5 rounded-full shadow transition peer-checked:translate-x-5"></div>
              </label>
            </form>
          </div>
          @if($children->isNotEmpty())
            <div class="mt-3 ml-11 grid grid-cols-1 sm:grid-cols-2 gap-2">
              @foreach($children as $child)
                <div class="flex items-center justify-between rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5">
                  <div>
                    <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $child->display_name }}</p>
                    <p class="text-xs text-slate-400 font-mono">{{ $child->module_key }}</p>
                  </div>
                  <form method="POST" action="{{ route('settings.modules.toggle') }}" class="module-toggle-form">@csrf<input type="hidden" name="module_key" value="{{ $child->module_key }}">
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" class="sr-only peer module-toggle" data-key="{{ $child->module_key }}" @checked($child->is_enabled) @disabled(!$parent->is_enabled)>
                      <div class="w-9 h-5 bg-slate-200 peer-focus:ring-2 peer-focus:ring-blue-500/20 rounded-full peer peer-checked:bg-blue-600 transition opacity-100 disabled:opacity-40"></div>
                      <div class="absolute left-0.5 top-0.5 bg-white size-4 rounded-full shadow transition peer-checked:translate-x-4"></div>
                    </label>
                  </form>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @empty
        <div class="p-8 text-center text-slate-500">No modules found. Navigate to trigger sync.</div>
      @endforelse

      {{-- Orphan children without parent (should not happen after sync) --}}
      @foreach($grouped->get('', collect()) as $orphan)
        @if($parents->contains('module_key', $orphan->module_key)) @continue @endif
      @endforeach
    </div>
  </div>
</div>
@push('scripts')
<script>
document.querySelectorAll('.module-toggle').forEach(chk => {
  chk.addEventListener('change', async function(){
    const key = this.dataset.key;
    const form = this.closest('form');
    const url = form.action;
    const token = form.querySelector('input[name="_token"]').value;
    // Optimistic UI is handled by peer-checked, but we need to persist
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({module_key: key})
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Failed');
      // Success — reload to refresh sidebar and toggle states (children disabled when parent off)
      window.location.reload();
    } catch (e) {
      console.error(e);
      alert('Toggle failed: ' + (e.message || e));
      // revert checkbox on failure
      this.checked = !this.checked;
    }
  });
});
</script>
@endpush
@endsection
