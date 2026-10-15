@extends('layouts.app')
@section('title', translate('payment_control'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('payment_control') }}</span></div>
    @if(is_superadmin_loggedin() && $branches->count())
    <div class="rounded-2xl bg-white border border-slate-200 p-4 flex items-center gap-3"><span class="text-sm font-medium">{{ translate('branch') }}</span><select onchange="location.href='{{ route('settings.payment_gateway') }}?branch_id='+this.value" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">@foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$branch_id)>{{ $b->name }}</option>@endforeach</select></div>
    @endif
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <h3 class="text-sm font-bold mb-3">{{ translate('payment_gateway_activation') }}</h3>
        <form id="activeForm" class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            @csrf
            @foreach(['paypal'=>'Paypal','stripe'=>'Stripe','payumoney'=>'PayUmoney','paystack'=>'Paystack','razorpay'=>'Razorpay'] as $k=>$label)
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="{{ $k }}_status" value="1" @checked(($config->$k.'_status' ?? 2)==1) class="rounded text-[#2563eb]"> {{ $label }}</label>
            @endforeach
            <div class="col-span-2 sm:col-span-5 flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
        </form>
    </div>
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">Paypal</h3>
            <form class="payForm space-y-3" data-url="{{ route('settings.paypal_save') }}?branch_id={{ $branch_id }}">
                @csrf
                <input name="paypal_username" placeholder="Username" value="{{ $config->paypal_username ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <input name="paypal_password" placeholder="Password" value="{{ $config->paypal_password ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <input name="paypal_signature" placeholder="Signature" value="{{ $config->paypal_signature ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <input name="paypal_email" placeholder="Email" value="{{ $config->paypal_email ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="paypal_sandbox" value="1" @checked(($config->paypal_sandbox ?? 2)==1)> Sandbox</label>
                <button class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white w-full">{{ translate('save') }}</button>
            </form>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">Stripe</h3>
            <form class="payForm space-y-3" data-url="{{ route('settings.stripe_save') }}?branch_id={{ $branch_id }}">
                @csrf
                <input name="stripe_secret" placeholder="Secret Key" value="{{ $config->stripe_secret ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="stripe_demo" value="1" @checked(($config->stripe_demo ?? 2)==1)> Demo</label>
                <button class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white w-full">{{ translate('save') }}</button>
            </form>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">PayUmoney</h3>
            <form class="payForm space-y-3" data-url="{{ route('settings.payumoney_save') }}?branch_id={{ $branch_id }}">
                @csrf
                <input name="payumoney_key" placeholder="Key" value="{{ $config->payumoney_key ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <input name="payumoney_salt" placeholder="Salt" value="{{ $config->payumoney_salt ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="payumoney_demo" value="1" @checked(($config->payumoney_demo ?? 2)==1)> Demo</label>
                <button class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white w-full">{{ translate('save') }}</button>
            </form>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">Paystack</h3>
            <form class="payForm space-y-3" data-url="{{ route('settings.paystack_save') }}?branch_id={{ $branch_id }}">
                @csrf
                <input name="paystack_secret_key" placeholder="Secret Key" value="{{ $config->paystack_secret_key ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <button class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white w-full">{{ translate('save') }}</button>
            </form>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">Razorpay</h3>
            <form class="payForm space-y-3" data-url="{{ route('settings.razorpay_save') }}?branch_id={{ $branch_id }}">
                @csrf
                <input name="razorpay_key_id" placeholder="Key Id" value="{{ $config->razorpay_key_id ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <input name="razorpay_key_secret" placeholder="Key Secret" value="{{ $config->razorpay_key_secret ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <button class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white w-full">{{ translate('save') }}</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('activeForm').addEventListener('submit', function(e){
    e.preventDefault(); const fd=new FormData(this);
    fetch('{{ route('settings.payment_active') }}?branch_id={{ $branch_id }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>alert(j.message));
});
document.querySelectorAll('.payForm').forEach(f=>{
    f.addEventListener('submit', function(e){
        e.preventDefault(); const fd=new FormData(this);
        fetch(this.dataset.url,{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{
            if(j.status==='success') alert(j.message); else alert(Object.values(j.error||{}).join('\n'));
        });
    });
});
</script>
@endpush
@endsection
