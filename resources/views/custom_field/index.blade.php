@extends('layouts.app')
@section('title', translate('custom_field'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('custom_field') }}</span></div>
    <div x-data="{ tab: 'list' }" class="space-y-4">
        <div class="flex gap-2"><button @click="tab='list'" :class="tab==='list' ? 'bg-[#2563eb] text-white' : 'bg-white border border-slate-200'" class="rounded-xl px-4 py-2 text-sm font-medium">{{ translate('custom_field_list') }}</button><button @click="tab='create'" :class="tab==='create' ? 'bg-[#2563eb] text-white' : 'bg-white border border-slate-200'" class="rounded-xl px-4 py-2 text-sm font-medium">{{ translate('add_custom_field') }}</button></div>
        <div x-show="tab==='list'" x-cloak class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('belongs_to') }}</th><th class="px-4 py-3">{{ translate('field_label') }}</th><th class="px-4 py-3">{{ translate('field_type') }}</th><th class="px-4 py-3">{{ translate('required') }}</th><th class="px-4 py-3">{{ translate('status') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($customfield as $i=>$f)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3">{{ $f->form_to }}</td><td class="px-4 py-3">{{ $f->field_label }}</td><td class="px-4 py-3">{{ $f->field_type }}</td><td class="px-4 py-3">{{ $f->required ? 'Yes' : 'No' }}</td><td class="px-4 py-3"><button onclick="toggleStatus({{ $f->id }}, this.checked)" class="relative inline-flex h-5 w-9 items-center rounded-full transition {{ $f->status ? 'bg-[#2563eb]' : 'bg-slate-200' }}"><span class="inline-block h-3 w-3 transform rounded-full bg-white transition {{ $f->status ? 'translate-x-5' : 'translate-x-1' }}"></span></button></td><td class="px-4 py-3"><div class="flex justify-end gap-1.5"><a href="{{ route('customfield.edit',$f->id) }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-pencil-square class="size-4"/></a><form method="POST" action="{{ route('customfield.destroy',$f->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">@csrf @method('DELETE')<button class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-red-600"><x-heroicon-o-trash class="size-4"/></button></form></div></td></tr>@empty<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse</tbody></table></div>
        </div>
        <div x-show="tab==='create'" x-cloak class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">{{ translate('add_custom_field') }}</h3>
            <form id="cfCreate" class="space-y-3">
                @csrf
                @if(is_superadmin_loggedin())<div><label class="block text-sm font-medium mb-1">{{ translate('branch') }} <span class="text-red-500">*</span></label><select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><option value="">{{ translate('select') }}</option>@foreach(App\Models\Branch::orderBy('name')->get() as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>@endif
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div><label class="block text-sm font-medium mb-1">{{ translate('belongs_to') }} <span class="text-red-500">*</span></label><select name="belongs_to" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><option value="student">{{ translate('student') }}</option><option value="employee">{{ translate('employee') }}</option><option value="parent">{{ translate('parent') }}</option></select></div>
                    <div><label class="block text-sm font-medium mb-1">{{ translate('field_type') }} <span class="text-red-500">*</span></label><select name="field_type" id="field_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><option value="text">Text</option><option value="textarea">Textarea</option><option value="dropdown">Dropdown</option><option value="date">Date</option><option value="checkbox">Checkbox</option><option value="number">Number</option><option value="url">URL</option><option value="email">Email</option></select></div>
                </div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('field_label') }} <span class="text-red-500">*</span></label><input name="field_label" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required></div>
                <div id="defaultText"><label class="block text-sm font-medium mb-1">{{ translate('default_value') }}</label><input name="com_default_value" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></div>
                <div id="defaultDropdown" style="display:none"><label class="block text-sm font-medium mb-1">{{ translate('default_value') }} (comma separated)</label><input name="dropdown_default_value" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div><label class="block text-sm font-medium mb-1">{{ translate('bs_column') }} <span class="text-red-500">*</span></label><select name="bs_column" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><option value="6">6</option><option value="4">4</option><option value="12">12</option></select></div>
                    <div><label class="block text-sm font-medium mb-1">{{ translate('field_order') }} <span class="text-red-500">*</span></label><input type="number" name="field_order" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required></div>
                    <div class="flex flex-col gap-1 justify-end"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_required" value="1"> {{ translate('required') }}</label><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_show_table" value="1"> {{ translate('show_on_table') }}</label><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_active" value="1" checked> {{ translate('active') }}</label></div>
                </div>
                <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('field_type').addEventListener('change', function(){
    const v=this.value;
    document.getElementById('defaultDropdown').style.display = v==='dropdown' ? 'block' : 'none';
    document.getElementById('defaultText').style.display = v==='dropdown' ? 'none' : 'block';
});
document.getElementById('cfCreate').addEventListener('submit', function(e){
    e.preventDefault(); const fd=new FormData(this);
    fetch('{{ route('customfield.save') }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') location.href=j.url; else alert(Object.values(j.error||{}).join('\n')); });
});
function toggleStatus(id, checked){
    fetch('{{ route('customfield.status') }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams({id, status:checked})}).then(r=>r.json()).then(j=>{});
}
</script>
@endpush
@endsection
