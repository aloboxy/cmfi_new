@extends('layouts.app')
@section('title', translate('live_class'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('live_class') }} {{ translate('settings') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form id="liveForm" class="space-y-4">
            @csrf
            <div><label class="block text-sm font-medium mb-1.5">Zoom API Key <span class="text-red-500">*</span></label><input name="zoom_api_key" value="{{ $config->zoom_api_key ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
            <div><label class="block text-sm font-medium mb-1.5">Zoom API Secret <span class="text-red-500">*</span></label><input name="zoom_api_secret" value="{{ $config->zoom_api_secret ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
            <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('liveForm').addEventListener('submit', function(e){
    e.preventDefault(); const fd=new FormData(this);
    fetch('{{ route('settings.live_class_save') }}?branch_id={{ $branch_id }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') alert(j.message); else alert(Object.values(j.error||{}).join('\n')); });
});
</script>
@endpush
@endsection
