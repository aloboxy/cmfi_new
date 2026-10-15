@extends('layouts.app')

@section('title', translate('control_classes'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('classes.index') }}" class="hover:text-[#2563eb]">{{ translate('control_classes') }}</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('edit_class') }}</span>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('classes.index') }}" class="rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50 flex items-center gap-2"><x-heroicon-o-academic-cap class="size-4" /> {{ translate('class') }}</a>
        @if(get_permission('section', 'is_view'))
        <a href="{{ route('sections.index') }}" class="rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50 flex items-center gap-2"><x-heroicon-o-rectangle-group class="size-4" /> {{ translate('section') }}</a>
        @endif
    </div>

    <div class="max-w-3xl rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
            <span class="grid size-7 place-items-center rounded-lg bg-amber-500/10 text-amber-600"><x-heroicon-o-pencil-square class="size-4" /></span>
            <h3 class="text-sm font-bold text-slate-800">{{ translate('edit_class') }}</h3>
        </div>
        <form method="POST" action="{{ route('classes.update', $class['id']) }}" class="p-5 space-y-4">
            @csrf @method('PUT')
            @if(is_superadmin_loggedin())
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                <select name="branch_id" id="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                    <option value="">{{ translate('select') }}</option>
                    @foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$class['branch_id'])>{{ $b->name }}</option>@endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('name') }} <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $class['name']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class_numeric') }}</label>
                <input type="text" name="name_numeric" value="{{ old('name_numeric', $class['name_numeric']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('promotion_class') }}</label>
                <select name="next_class_id" id="next_class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                    <option value="">{{ translate('select') }}</option>
                    @foreach($promotionClasses as $pc)<option value="{{ $pc->id }}" @selected($pc->id==$class['next_class_id'])>{{ $pc->name }}</option>@endforeach
                </select>
            </div>
            @if($useSections)
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }}</label>
                <select name="sections[]" id="section_id" multiple class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white min-h-[120px]">
                    @foreach($sections as $sec)<option value="{{ $sec->id }}" @selected(in_array($sec->id, $allocations))>{{ $sec->name }}</option>@endforeach
                </select>
            </div>
            @endif
            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('classes.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ translate('cancel') }}</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition"><x-heroicon-o-check-circle class="size-4" /> {{ translate('update') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid=this.value;
    if(!bid) return;
    fetch('{{ route('ajax.getClassByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})}).then(r=>r.text()).then(html=>{document.getElementById('next_class_id').innerHTML=html;});
    fetch('{{ route('ajax.getSectionByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})}).then(r=>r.text()).then(html=>{const el=document.getElementById('section_id'); if(el) el.innerHTML=html;});
});
</script>
@endpush
@endsection
