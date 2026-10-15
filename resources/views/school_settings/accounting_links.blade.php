@extends('layouts.app')
@section('title', translate('accounting_links'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('accounting_links') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form id="accForm" class="space-y-4">
            @csrf
            <label class="flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="status" value="1" @checked(($transactions->status ?? 0)==1) id="statusChk" class="rounded text-[#2563eb]"> {{ translate('status') }}</label>
            <div id="accFields" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">{{ translate('deposit') }}</label><select name="deposit" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">@foreach(DB::table('voucher_head')->where('type','income')->get() as $v)<option value="{{ $v->id }}" @selected(($transactions->deposit ?? '')==$v->id)>{{ $v->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('expense') }}</label><select name="expense" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">@foreach(DB::table('voucher_head')->where('type','expense')->get() as $v)<option value="{{ $v->id }}" @selected(($transactions->expense ?? '')==$v->id)>{{ $v->name }}</option>@endforeach</select></div>
            </div>
            <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function toggleAcc(){ const on=document.getElementById('statusChk').checked; document.getElementById('accFields').style.display = on ? 'grid' : 'none';}
document.getElementById('statusChk').addEventListener('change', toggleAcc); toggleAcc();
document.getElementById('accForm').addEventListener('submit', function(e){
    e.preventDefault(); const fd=new FormData(this);
    fetch('{{ route('settings.accounting_links_save') }}?branch_id={{ $branch_id }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') alert(j.message); else alert(Object.values(j.error||{}).join('\n')); });
});
</script>
@endpush
@endsection
