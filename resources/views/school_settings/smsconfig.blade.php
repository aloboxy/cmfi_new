@extends('layouts.app')
@section('title', translate('sms_settings'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('sms_settings') }}</span><a href="{{ route('settings.smstemplate') }}" class="ml-auto text-sm text-[#2563eb]">{{ translate('sms_template') }}</a></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <h3 class="font-bold text-sm mb-3">{{ translate('active_sms_gateway') }}</h3>
        <form id="activeSms" class="flex items-center gap-3">
            @csrf
            <select name="sms_service_provider" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                @php $apis = DB::table('sms_api')->get(); @endphp
                @foreach($apis as $a)<option value="{{ $a->id }}" @selected(($api[$a->name]->is_active ?? 0)==1)>{{ $a->name }}</option>@endforeach
            </select>
            <button class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white">{{ translate('save') }}</button>
        </form>
    </div>
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">Twilio</h3>
            <form class="smsForm space-y-2" data-url="{{ route('settings.twilio') }}?branch_id={{ $branch_id }}">@csrf<input name="twilio_sid" value="{{ $api['twilio']->field_one ?? $api['Twilio']->field_one ?? '' }}" placeholder="Account SID" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input name="twilio_auth_token" value="{{ $api['twilio']->field_two ?? '' }}" placeholder="Auth Token" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input name="sender_number" value="{{ $api['twilio']->field_three ?? '' }}" placeholder="Sender Number" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><button class="w-full rounded-xl bg-[#2563eb] py-2 text-sm font-semibold text-white">{{ translate('save') }}</button></form>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">Clickatell</h3>
            <form class="smsForm space-y-2" data-url="{{ route('settings.clickatell') }}?branch_id={{ $branch_id }}">@csrf<input name="clickatell_user" value="{{ $api['clickatell']->field_one ?? '' }}" placeholder="Username" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input name="clickatell_password" value="{{ $api['clickatell']->field_two ?? '' }}" placeholder="Password" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input name="clickatell_api" value="{{ $api['clickatell']->field_three ?? '' }}" placeholder="API Key" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input name="sender_number" value="{{ $api['clickatell']->field_four ?? '' }}" placeholder="Sender Number" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><button class="w-full rounded-xl bg-[#2563eb] py-2 text-sm font-semibold text-white">{{ translate('save') }}</button></form>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">MSG91</h3>
            <form class="smsForm space-y-2" data-url="{{ route('settings.msg91') }}?branch_id={{ $branch_id }}">@csrf<input name="msg91_auth_key" value="{{ $api['msg91']->field_one ?? '' }}" placeholder="Auth Key" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input name="sender_id" value="{{ $api['msg91']->field_two ?? '' }}" placeholder="Sender ID" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><button class="w-full rounded-xl bg-[#2563eb] py-2 text-sm font-semibold text-white">{{ translate('save') }}</button></form>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">Bulk SMS</h3>
            <form class="smsForm space-y-2" data-url="{{ route('settings.bulksms') }}?branch_id={{ $branch_id }}">@csrf<input name="bulk_sms_username" value="{{ $api['bulksms']->field_one ?? $api['bulk_sms']->field_one ?? '' }}" placeholder="Username" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input name="bulk_sms_password" value="{{ $api['bulksms']->field_two ?? '' }}" placeholder="Password" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><button class="w-full rounded-xl bg-[#2563eb] py-2 text-sm font-semibold text-white">{{ translate('save') }}</button></form>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">TextLocal</h3>
            <form class="smsForm space-y-2" data-url="{{ route('settings.textlocal') }}?branch_id={{ $branch_id }}">@csrf<input name="textlocal_username" value="{{ $api['textlocal']->field_one ?? '' }}" placeholder="Username" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input name="textlocal_sender_id" value="{{ $api['textlocal']->field_two ?? '' }}" placeholder="Sender Name" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input name="textlocal_hash_key" value="{{ $api['textlocal']->field_three ?? '' }}" placeholder="Hash Key" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><button class="w-full rounded-xl bg-[#2563eb] py-2 text-sm font-semibold text-white">{{ translate('save') }}</button></form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('activeSms').addEventListener('submit', function(e){ e.preventDefault(); const fd=new FormData(this); fetch('{{ route('settings.sms_active') }}?branch_id={{ $branch_id }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>alert(j.message)); });
document.querySelectorAll('.smsForm').forEach(f=>{ f.addEventListener('submit', function(e){ e.preventDefault(); const fd=new FormData(this); fetch(this.dataset.url,{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') alert(j.message); else alert(Object.values(j.error||{}).join('\n')); }); });});
</script>
@endpush
@endsection
