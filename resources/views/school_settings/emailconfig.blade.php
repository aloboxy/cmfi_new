@extends('layouts.app')
@section('title', translate('email_settings'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('email_settings') }}</span><a href="{{ route('settings.emailtemplate') }}" class="ml-auto text-sm text-[#2563eb]">{{ translate('email_template') }}</a></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form id="emailForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1.5">System Email <span class="text-red-500">*</span></label><input name="email" value="{{ $config->email ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
                <div><label class="block text-sm font-medium mb-1.5">Protocol <span class="text-red-500">*</span></label><select name="protocol" id="protocol" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="smtp" @selected(($config->protocol ?? 'smtp')=='smtp')>SMTP</option><option value="mail" @selected(($config->protocol ?? '')=='mail')>Mail</option><option value="sendmail" @selected(($config->protocol ?? '')=='sendmail')>Sendmail</option></select></div>
                <div class="smtp-field"><label class="block text-sm font-medium mb-1.5">SMTP Host</label><input name="smtp_host" value="{{ $config->smtp_host ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div class="smtp-field"><label class="block text-sm font-medium mb-1.5">SMTP User</label><input name="smtp_user" value="{{ $config->smtp_user ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div class="smtp-field"><label class="block text-sm font-medium mb-1.5">SMTP Pass</label><input name="smtp_pass" value="{{ $config->smtp_pass ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div class="smtp-field"><label class="block text-sm font-medium mb-1.5">SMTP Port</label><input name="smtp_port" value="{{ $config->smtp_port ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div class="smtp-field"><label class="block text-sm font-medium mb-1.5">Encryption</label><select name="smtp_encryption" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">None</option><option value="ssl" @selected(($config->smtp_encryption ?? '')=='ssl')>SSL</option><option value="tls" @selected(($config->smtp_encryption ?? '')=='tls')>TLS</option></select></div>
            </div>
            <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function toggleSmtp(){ const v=document.getElementById('protocol').value; document.querySelectorAll('.smtp-field').forEach(el=> el.style.display = v==='smtp' ? 'block' : 'none');}
document.getElementById('protocol').addEventListener('change', toggleSmtp); toggleSmtp();
document.getElementById('emailForm').addEventListener('submit', function(e){
    e.preventDefault(); const fd=new FormData(this);
    fetch('{{ route('settings.email_config_save') }}?branch_id={{ $branch_id }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') alert(j.message); else alert(Object.values(j.error||{}).join('\n')); });
});
</script>
@endpush
@endsection
