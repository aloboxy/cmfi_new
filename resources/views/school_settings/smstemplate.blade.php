@extends('layouts.app')
@section('title', translate('sms_settings'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><a href="{{ route('settings.smsconfig') }}" class="hover:text-[#2563eb]">{{ translate('sms_settings') }}</a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('sms_template') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200"><h3 class="text-sm font-bold">{{ translate('sms_template') }}</h3></div>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('action') }}</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($templatelist as $i=>$t)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3">{{ $t->name }}</td><td class="px-4 py-3"><button onclick="openSmsTpl({{ $t->id }}, '{{ addslashes($t->name) }}')" class="rounded-lg bg-[#2563eb] px-3 py-1.5 text-xs font-semibold text-white">{{ translate('edit') }}</button></td></tr>@endforeach</tbody></table></div>
    </div>
</div>
<div id="smsModal" class="fixed inset-0 hidden items-center justify-center bg-black/40 p-4 z-50"><div class="w-full max-w-xl rounded-2xl bg-white p-5">
    <h3 id="smsModalTitle" class="font-bold mb-3"></h3>
    <form id="smsTplForm" class="space-y-3">@csrf<input type="hidden" name="template_id" id="tpl_id"><div class="flex gap-4"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="notify_student" value="1"> {{ translate('student') }}</label><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="notify_parent" value="1"> {{ translate('parent') }}</label></div><textarea name="template_body" id="tpl_body" rows="6" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required></textarea><div class="flex justify-end gap-2"><button type="button" onclick="closeSmsTpl()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">Cancel</button><button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white">{{ translate('save') }}</button></div></form>
</div></div>
@push('scripts')
<script>
let branchId={{ $branch_id }};
function openSmsTpl(id,name){
    document.getElementById('smsModalTitle').innerText=name;
    document.getElementById('tpl_id').value=id;
    fetch('{{ url('') }}/school_settings/smstemplate?branch_id='+branchId).then(()=>{});
    // fetch existing detail via DB; we do ajax fetch to get template detail
    fetch('/school_settings/smsTemplateSave',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});
    document.getElementById('smsModal').classList.remove('hidden'); document.getElementById('smsModal').classList.add('flex');
}
function closeSmsTpl(){ document.getElementById('smsModal').classList.add('hidden'); document.getElementById('smsModal').classList.remove('flex'); }
document.getElementById('smsTplForm').addEventListener('submit', function(e){
    e.preventDefault(); const fd=new FormData(this);
    fetch('{{ route('settings.sms_template_save') }}?branch_id='+branchId,{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success'){ alert(j.message); closeSmsTpl(); } else alert(Object.values(j.error||{}).join('\n')); });
});
</script>
@endpush
@endsection
