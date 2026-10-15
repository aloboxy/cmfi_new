@extends('layouts.app')
@section('title', translate('designation'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('designation') }}</span>
    </div>
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">
        @if(get_permission('designation','is_add'))
        <div class="lg:col-span-2">
            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                    <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-plus-circle class="size-4"/></span>
                    <h3 class="text-sm font-bold text-slate-800">{{ translate('add_designation') }}</h3>
                </div>
                <form method="POST" action="{{ route('employee.designation') }}" class="p-5 space-y-4">
                    @csrf
                    @if(is_superadmin_loggedin())
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                        <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>
                        @error('branch_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('designation_name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="designation_name" value="{{ old('designation_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                        @error('designation_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button>
                </form>
            </div>
        </div>
        @endif
        <div class="{{ get_permission('designation','is_add') ? 'lg:col-span-3' : 'lg:col-span-5' }}">
            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                    <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-list-bullet class="size-4"/></span>
                    <h3 class="text-sm font-bold text-slate-800">{{ translate('designation_list') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('branch') }}</th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($designations as $i=>$row)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $row->branch_name }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $row->name }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-1.5">
                                        @if(get_permission('designation','is_edit'))
                                        <button onclick="editDes({{ $row->id }})" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-[#2563eb]"><x-heroicon-o-pencil-square class="size-4"/></button>
                                        @endif
                                        @if(get_permission('designation','is_delete'))
                                        <form method="POST" action="{{ route('employee.designation_delete',$row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-red-600"><x-heroicon-o-trash class="size-4"/></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('editModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between"><h3 class="text-sm font-bold text-slate-800">{{ translate('edit') }}</h3><button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-slate-400">✕</button></div>
        <form id="editForm" method="POST" action="{{ route('employee.designation_edit') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="designation_id" id="edesignation_id">
            @if(is_superadmin_loggedin())
            <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }}</label><select name="branch_id" id="ebranch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
            @endif
            <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('designation_name') }}</label><input type="text" name="designation_name" id="edesignation_name" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><p id="e_error" class="text-xs text-red-600 mt-1 hidden"></p></div>
            <div class="flex justify-end gap-2"><button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm">{{ translate('cancel') }}</button><button type="submit" class="rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-semibold text-white">{{ translate('update') }}</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function editDes(id){
    fetch('{{ route('employee.designationDetails') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({id})})
    .then(r=>r.json()).then(d=>{
        document.getElementById('edesignation_id').value=d.id;
        document.getElementById('edesignation_name').value=d.name;
        const eb=document.getElementById('ebranch_id'); if(eb) eb.value=d.branch_id;
        document.getElementById('editModal').classList.remove('hidden');
    });
}
document.getElementById('editForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const fd=new FormData(this);
    fetch(this.action,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:fd}).then(r=>r.json()).then(d=>{
        if(d.status=='success') location.reload();
        else {document.getElementById('e_error').textContent=d.error?.designation_name ?? 'Error'; document.getElementById('e_error').classList.remove('hidden');}
    });
});
</script>
@endpush
@endsection
