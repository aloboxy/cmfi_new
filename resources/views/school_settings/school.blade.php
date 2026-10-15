@extends('layouts.app')
@section('title', translate('school_settings'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('school_settings') }}</span>
    </div>
    @if(is_superadmin_loggedin() && $branches->count())
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-3">
        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ translate('branch') }}</span>
        <select onchange="location.href='{{ route('settings.school') }}?branch_id='+this.value" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-850 px-3 py-2 text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
            @foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$branchID)>{{ $b->name }}</option>@endforeach
        </select>
    </div>
    @endif
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
            <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-building-office-2 class="size-4"/></span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">{{ translate('school_settings') }}</h3>
        </div>
        <form id="schoolForm" method="POST" action="{{ route('settings.school.update') }}?branch_id={{ $branchID }}" class="p-5">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ translate('branch_name') }} <span class="text-red-500">*</span></label>
                    <input name="branch_name" value="{{ old('branch_name', $school->name ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ translate('school_name') }} <span class="text-red-500">*</span></label>
                    <input name="school_name" value="{{ old('school_name', $school->school_name ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ translate('email') }} <span class="text-red-500">*</span></label>
                    <input name="email" type="email" value="{{ old('email', $school->email ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ translate('mobile_no') }}</label>
                    <input name="mobileno" value="{{ old('mobileno', $school->mobileno ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ translate('currency') }} <span class="text-red-500">*</span></label>
                    <input name="currency" value="{{ old('currency', $school->currency ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ translate('currency_symbol') }} <span class="text-red-500">*</span></label>
                    <input name="currency_symbol" value="{{ old('currency_symbol', $school->symbol ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ translate('city') }}</label>
                    <input name="city" value="{{ old('city', $school->city ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ translate('state') }}</label>
                    <input name="state" value="{{ old('state', $school->state ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition-all">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ translate('address') }}</label>
                    <textarea name="address" rows="3" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition-all">{{ old('address', $school->address ?? '') }}</textarea>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="checkbox" name="use_section" value="1" @checked(($school->use_section ?? 1)==1) class="rounded border-slate-300 dark:border-slate-700 text-[#2563eb] bg-white dark:bg-slate-800 focus:ring-offset-0">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ translate('use_section') }}</span>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-bold text-white shadow hover:bg-blue-700 transition">
                    <x-heroicon-o-check-circle class="size-4"/> {{ translate('save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('schoolForm').addEventListener('submit', function(e){
    e.preventDefault();
    const fd=new FormData(this);
    fetch(this.action,{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd})
    .then(r=>r.json()).then(j=>{
        if(j.status==='success'){ alert(j.message); location.reload(); }
        else if(j.error){ alert(Object.values(j.error).join('\n')); }
        else alert(JSON.stringify(j));
    });
});
</script>
@endpush
@endsection
