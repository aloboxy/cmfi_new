@extends('layouts.app')
@section('title', translate('award'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('award.index') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-left class="size-4" /></a><span class="font-semibold text-slate-800">{{ translate('edit') }} {{ translate('award') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form method="POST" action="{{ route('award.edit',$award['id']) }}" id="awardEditForm" class="space-y-3">
            @csrf
            @if(is_superadmin_loggedin())<div><label class="block text-sm font-medium mb-1">{{ translate('branch') }} <span class="text-red-500">*</span></label><select name="branch_id" id="branch_edit" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$award['branch_id'])>{{ $b->name }}</option>@endforeach</select></div>@endif
            <div><label class="block text-sm font-medium mb-1">{{ translate('role') }} <span class="text-red-500">*</span></label><select name="role_id" id="role_edit" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($roles as $id=>$name)<option value="{{ $id }}" @selected($id==$award['role_id'])>{{ $name }}</option>@endforeach</select></div>
            <div id="classDivEdit" style="display:none"><label class="block text-sm font-medium mb-1">{{ translate('class') }}</label><select name="class_id" id="class_edit" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option></select></div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('winner') }} <span class="text-red-500">*</span></label><select name="user_id" id="user_edit" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="{{ $award['user_id'] }}">Current: {{ $award['user_id'] }}</option></select></div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('award_name') }}</label><input type="text" name="award_name" value="{{ $award['name'] }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('gift_item') }}</label><input type="text" name="gift_item" value="{{ $award['gift_item'] }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('cash_price') }}</label><input type="number" step="0.01" name="cash_price" value="{{ $award['award_amount'] }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('award_reason') }}</label><input type="text" name="award_reason" value="{{ $award['award_reason'] }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('given_date') }}</label><input type="date" name="given_date" value="{{ $award['given_date'] }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
            <div class="flex justify-end gap-2"><a href="{{ route('award.index') }}" class="rounded-xl bg-slate-100 px-6 py-2.5 text-sm">{{ translate('cancel') }}</a><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('update') }}</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function loadUsersEdit(){
    const role=document.getElementById('role_edit').value; const branch=document.getElementById('branch_edit')?.value || '{{ get_loggedin_branch_id() }}';
    if(role==7){
        document.getElementById('classDivEdit').style.display='block';
        fetch('{{ route('ajax.getClassByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body:JSON.stringify({branch_id:branch})}).then(r=>r.text()).then(h=>document.getElementById('class_edit').innerHTML=h);
    } else {
        document.getElementById('classDivEdit').style.display='none';
        fetch('{{ route('ajax.getStaffListRole') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body:JSON.stringify({role_id:role, branch_id:branch})}).then(r=>r.text()).then(h=>document.getElementById('user_edit').innerHTML=h);
    }
}
document.getElementById('role_edit')?.addEventListener('change', loadUsersEdit);
document.getElementById('branch_edit')?.addEventListener('change', loadUsersEdit);
document.getElementById('class_edit')?.addEventListener('change', function(){
    const class_id=this.value; const branch=document.getElementById('branch_edit')?.value || '';
    fetch('{{ url('ajax/getStudentByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, body:new URLSearchParams({branch_id:branch, class_id})}).then(r=>r.text()).then(h=>document.getElementById('user_edit').innerHTML=h);
});
document.getElementById('awardEditForm')?.addEventListener('submit', function(e){ e.preventDefault(); const fd=new FormData(this); fetch(this.action,{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') window.location=j.url; else alert(Object.values(j.error||{}).join("\n")); });});
if(document.getElementById('role_edit').value==7) loadUsersEdit();
</script>
@endpush
@endsection
