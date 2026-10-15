@extends('layouts.app')
@section('title', translate('award'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4" /></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('award') }}</span></div>
    <div x-data="{ tab: 'list' }" class="space-y-4">
        <div class="flex items-center gap-2"><button @click="tab='list'" :class="tab==='list' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200'" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition">{{ translate('award_list') }}</button>@if(get_permission('award','is_add'))<button @click="tab='create'" :class="tab==='create' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200'" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition">{{ translate('give_award') }}</button>@endif</div>
        <div x-show="tab==='list'" x-cloak class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200"><h3 class="text-sm font-bold">{{ translate('award_list') }}</h3></div>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3">#</th>@if(is_superadmin_loggedin())<th class="px-4 py-3">{{ translate('branch') }}</th>@endif<th class="px-4 py-3">{{ translate('winner') }}</th><th class="px-4 py-3">{{ translate('role') }}</th><th class="px-4 py-3">{{ translate('award_name') }}</th><th class="px-4 py-3">{{ translate('gift_item') }}</th><th class="px-4 py-3">{{ translate('cash_price') }}</th><th class="px-4 py-3">{{ translate('given_date') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($awardlist as $i=>$row)<tr><td class="px-4 py-3">{{ $i+1 }}</td>@if(is_superadmin_loggedin())<td class="px-4 py-3">{{ get_type_name_by_id('branch',$row->branch_id) }}</td>@endif<td class="px-4 py-3">@php if($row->role_id==7){ $s=DB::table('student')->where('id',$row->user_id)->first(); echo $s ? $s->first_name.' '.$s->last_name : $row->user_id; } else { $st=DB::table('staff')->where('id',$row->user_id)->first(); echo $st ? $st->name.' ('.$st->staff_id.')' : $row->user_id; } @endphp</td><td class="px-4 py-3">{{ $row->role_name }}</td><td class="px-4 py-3">{{ $row->name }}</td><td class="px-4 py-3">{{ $row->gift_item }}</td><td class="px-4 py-3">{{ $row->award_amount }}</td><td class="px-4 py-3">{{ _d($row->given_date) }}</td><td class="px-4 py-3"><div class="flex justify-end gap-1.5">@if(get_permission('award','is_edit'))<a href="{{ route('award.edit',$row->id) }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-pencil-square class="size-4" /></a>@endif @if(get_permission('award','is_delete'))<form method="POST" action="{{ route('award.delete',$row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">@csrf @method('DELETE')<button class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-red-600"><x-heroicon-o-trash class="size-4" /></button></form>@endif</div></td></tr>@empty<tr><td colspan="9" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse</tbody></table></div>
        </div>
        @if(get_permission('award','is_add'))
        <div x-show="tab==='create'" x-cloak class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="text-sm font-bold mb-4">{{ translate('give_award') }}</h3>
            <form method="POST" action="{{ route('award.index') }}" id="awardCreateForm" class="space-y-3">
                @csrf
                @if(is_superadmin_loggedin())<div><label class="block text-sm font-medium mb-1">{{ translate('branch') }} <span class="text-red-500">*</span></label><select name="branch_id" id="branch_award" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>@endif
                <div><label class="block text-sm font-medium mb-1">{{ translate('role') }} <span class="text-red-500">*</span></label><select name="role_id" id="role_award" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($rolesAll as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div>
                <div id="classDiv" style="display:none"><label class="block text-sm font-medium mb-1">{{ translate('class') }} <span class="text-red-500">*</span></label><select name="class_id" id="class_award" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option></select></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('winner') }} <span class="text-red-500">*</span></label><select name="user_id" id="user_award" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option></select></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('award_name') }} <span class="text-red-500">*</span></label><input type="text" name="award_name" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('gift_item') }} <span class="text-red-500">*</span></label><input type="text" name="gift_item" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('cash_price') }}</label><input type="number" step="0.01" name="cash_price" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('award_reason') }} <span class="text-red-500">*</span></label><input type="text" name="award_reason" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('given_date') }} <span class="text-red-500">*</span></label><input type="date" name="given_date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
                <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
            </form>
        </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
document.getElementById('role_award')?.addEventListener('change', function(){
    const role=this.value; const branch=document.getElementById('branch_award')?.value || '{{ get_loggedin_branch_id() }}';
    if(role==7){
        document.getElementById('classDiv').style.display='block';
        fetch('{{ route('ajax.getClassByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body:JSON.stringify({branch_id:branch})}).then(r=>r.text()).then(h=>document.getElementById('class_award').innerHTML=h);
    } else {
        document.getElementById('classDiv').style.display='none';
        fetch('{{ route('ajax.getStaffListRole') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body:JSON.stringify({role_id:role, branch_id:branch})}).then(r=>r.text()).then(h=>document.getElementById('user_award').innerHTML=h);
    }
});
document.getElementById('class_award')?.addEventListener('change', function(){
    const class_id=this.value; const branch=document.getElementById('branch_award')?.value || '';
    fetch('{{ url('ajax/getStudentByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, body:new URLSearchParams({branch_id:branch, class_id})}).then(r=>r.text()).then(h=>document.getElementById('user_award').innerHTML=h);
});
document.getElementById('branch_award')?.addEventListener('change', function(){ document.getElementById('role_award').dispatchEvent(new Event('change')); });
document.getElementById('awardCreateForm')?.addEventListener('submit', function(e){ e.preventDefault(); const fd=new FormData(this); fetch(this.action,{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') window.location=j.url; else alert(Object.values(j.error||{}).join("\n")); });});
</script>
@endpush
@endsection
