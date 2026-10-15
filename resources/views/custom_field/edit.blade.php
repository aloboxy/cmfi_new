@extends('layouts.app')
@section('title', translate('custom_field'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><a href="{{ route('customfield.index') }}" class="hover:text-[#2563eb]">{{ translate('custom_field') }}</a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('edit') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5 max-w-2xl">
        <h3 class="font-bold text-sm mb-3">{{ translate('edit_custom_field') }}</h3>
        <form id="cfEdit" class="space-y-3">
            @csrf
            <input type="hidden" name="custom_field_id" value="{{ $customfield->id }}">
            <div><label class="block text-sm font-medium mb-1">{{ translate('field_label') }} <span class="text-red-500">*</span></label><input name="field_label" value="{{ $customfield->field_label }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required></div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('field_type') }}</label><input value="{{ $customfield->field_type }}" disabled class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50"><input type="hidden" name="field_type" value="{{ $customfield->field_type }}"></div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('default_value') }}</label><input name="com_default_value" value="{{ $customfield->default_value }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium mb-1">{{ translate('belongs_to') }}</label><input value="{{ $customfield->form_to }}" disabled class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('field_order') }}</label><input type="number" name="field_order" value="{{ $customfield->field_order }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('bs_column') }}</label><select name="bs_column" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"><option value="6" @selected($customfield->bs_column==6)>6</option><option value="4" @selected($customfield->bs_column==4)>4</option><option value="12" @selected($customfield->bs_column==12)>12</option></select></div>
                <div class="flex flex-col gap-1 justify-end"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_required" value="1" @checked($customfield->required==1)> {{ translate('required') }}</label><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_show_table" value="1" @checked($customfield->show_on_table==1)> {{ translate('show_on_table') }}</label><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_active" value="1" @checked($customfield->status==1)> {{ translate('active') }}</label></div>
            </div>
            <div class="flex justify-end gap-2"><a href="{{ route('customfield.index') }}" class="rounded-xl border border-slate-200 px-5 py-2 text-sm">Cancel</a><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2 text-sm font-semibold text-white">{{ translate('update') }}</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('cfEdit').addEventListener('submit', function(e){
    e.preventDefault(); const fd=new FormData(this);
    if(this.field_type.value==='dropdown') fd.set('dropdown_default_value', fd.get('com_default_value'));
    fetch('{{ route('customfield.save') }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') location.href=j.url; else alert(Object.values(j.error||{}).join('\n')); });
});
</script>
@endpush
@endsection
