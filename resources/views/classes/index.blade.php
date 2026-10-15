@extends('layouts.app')

@section('title', translate('control_classes'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('control_classes') }}</span>
    </div>

    {{-- Tabs like legacy --}}
    <div class="flex gap-2">
        <a href="{{ route('classes.index') }}" class="rounded-xl bg-[#2563eb] text-white px-4 py-2 text-sm font-medium shadow flex items-center gap-2">
            <x-heroicon-o-academic-cap class="size-4" /> {{ translate('class') }}
        </a>
        @if(get_permission('section', 'is_view'))
        <a href="{{ route('sections.index') }}" class="rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50 flex items-center gap-2">
            <x-heroicon-o-rectangle-group class="size-4" /> {{ translate('section') }}
        </a>
        @endif
        <a href="{{ route('classes.teacher_allocation') }}" class="rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50 flex items-center gap-2">
            <x-heroicon-o-user-group class="size-4" /> {{ translate('assign_class_teacher') }}
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">
        @if(get_permission('classes', 'is_add'))
        <div class="lg:col-span-2">
            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                    <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-plus-circle class="size-4" /></span>
                    <h3 class="text-sm font-bold text-slate-800">{{ translate('create_class') }}</h3>
                </div>
                <form method="POST" action="{{ route('classes.store') }}" id="create-class-form" class="p-5 space-y-4">
                    @csrf
                    @if(is_superadmin_loggedin())
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                        <select name="branch_id" id="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                            <option value="">{{ translate('select') }}</option>
                            @foreach($branches as $b)<option value="{{ $b->id }}" @selected(old('branch_id')==$b->id)>{{ $b->name }}</option>@endforeach
                        </select>
                        @error('branch_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class_numeric') }}</label>
                        <input type="text" name="name_numeric" value="{{ old('name_numeric') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('promotion_class') }}</label>
                        <select name="next_class_id" id="next_class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                            <option value="">{{ translate('select') }}</option>
                            @foreach($promotionClasses as $pc)<option value="{{ $pc->id }}">{{ $pc->name }}</option>@endforeach
                        </select>
                    </div>
                    @if($useSections)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }}</label>
                        <select name="sections[]" id="section_id" multiple class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white min-h-[90px]">
                            @foreach($sections as $sec)<option value="{{ $sec->id }}">{{ $sec->name }}</option>@endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-400">{{ translate('select_multiple') ?? 'Hold Ctrl to select multiple' }}</p>
                    </div>
                    @endif
                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                            <x-heroicon-o-check-circle class="size-4" /> {{ translate('save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <div class="{{ get_permission('classes', 'is_add') ? 'lg:col-span-3' : 'lg:col-span-5' }}">
            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                    <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-list-bullet class="size-4" /></span>
                    <h3 class="text-sm font-bold text-slate-800">{{ translate('class_list') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">{{ translate('branch') }}</th>
                                <th class="px-4 py-3">{{ translate('class_name') }}</th>
                                <th class="px-4 py-3">{{ translate('class_numeric') }}</th>
                                <th class="px-4 py-3">{{ translate('promotion_class') }}</th>
                                <th class="px-4 py-3">{{ translate('section') }}</th>
                                <th class="px-4 py-3 text-right">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($classlist as $i => $row)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $row->branch_name }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $row->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $row->name_numeric }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $row->next_class_id ? get_type_name_by_id('class', $row->next_class_id) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    @php $secs = DB::table('sections_allocation')->where('class_id', $row->id)->get(); @endphp
                                    @forelse($secs as $s) {{ get_type_name_by_id('section', $s->section_id) }}<br> @empty - @endforelse
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(get_permission('classes', 'is_edit'))
                                        <a href="{{ route('classes.edit', $row->id) }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#2563eb] transition"><x-heroicon-o-pencil-square class="size-4" /></a>
                                        @endif
                                        @if(get_permission('classes', 'is_delete'))
                                        <form method="POST" action="{{ route('classes.destroy', $row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-600 transition"><x-heroicon-o-trash class="size-4" /></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple{border-color:#e2e8f0 !important; border-radius:0.75rem !important; min-height:42px; padding:4px}
.select2-container--default .select2-selection--single .select2-selection__rendered{line-height:32px}
.select2-container--default .select2-selection--multiple .select2-selection__choice{background:#2563eb; color:#fff; border:none; border-radius:9999px; padding:2px 8px}
</style>
@endpush
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
  function initSelect2(){
    $('select').each(function(){
      if($(this).hasClass('select2-hidden-accessible')) return;
      $(this).select2({width:'100%', placeholder:'{{ translate('select') }}', allowClear:true, dropdownParent: $(this).parent()});
    });
  }
  initSelect2();
  // Re-init after ajax updates
  const origFetch = window.fetch;
  document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid = this.value;
    if(!bid) return;
    fetch('{{ route('ajax.getClassByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})})
        .then(r=>r.text()).then(html=>{ const el=document.getElementById('next_class_id'); if(el){ el.innerHTML = html; $(el).trigger('change.select2'); }});
    fetch('{{ route('ajax.getSectionByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})})
        .then(r=>r.text()).then(html=>{ const el=document.getElementById('section_id'); if(el){ el.innerHTML = html; $(el).trigger('change'); }});
  });
});
</script>
@endpush
@endsection
