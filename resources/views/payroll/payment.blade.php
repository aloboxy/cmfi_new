@extends('layouts.app')
@section('title', translate('salary_payment'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4" /></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('salary_payment') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-4">{{ translate('select_ground') }}</h3>
        <form method="GET" action="{{ route('payroll.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if(is_superadmin_loggedin())
            <div><label class="block text-sm font-medium mb-1.5">{{ translate('branch') }}</label><select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected(request('branch_id')==$b->id)>{{ $b->name }}</option>@endforeach</select></div>
            @endif
            <div><label class="block text-sm font-medium mb-1.5">{{ translate('role') }} <span class="text-red-500">*</span></label><select name="staff_role" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required><option value="">{{ translate('select') }}</option>@foreach($roles as $id=>$name)<option value="{{ $id }}" @selected(request('staff_role')==$id)>{{ $name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium mb-1.5">{{ translate('month') }} <span class="text-red-500">*</span></label><input type="month" name="month_year" value="{{ request('month_year', date('Y-m')) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
            <div class="flex items-end"><button type="submit" name="search" value="1" class="w-full rounded-xl bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white">{{ translate('filter') }}</button></div>
        </form>
    </div>
    @if(isset($stafflist))
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200"><h3 class="text-sm font-bold">{{ translate('staff_list') }}</h3></div>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3">{{ translate('staff_id') }}</th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('designation') }}</th><th class="px-4 py-3">{{ translate('salary_grade') }}</th><th class="px-4 py-3">{{ translate('basic_salary') }}</th><th class="px-4 py-3">{{ translate('status') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y divide-slate-100">
            @forelse($stafflist as $row)<tr class="hover:bg-slate-50/60"><td class="px-4 py-3">{{ $row->staff_id }}</td><td class="px-4 py-3">{{ $row->name }}</td><td class="px-4 py-3">{{ $row->designation_name }}</td><td class="px-4 py-3">{{ $row->template_name }}</td><td class="px-4 py-3">{{ $row->basic_salary }}</td><td class="px-4 py-3">@if($row->salary_id!=0)<span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">{{ translate('paid') }}</span>@else<span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-700">{{ translate('unpaid') }}</span>@endif</td><td class="px-4 py-3 text-right">@if($row->salary_id!=0)<a href="{{ route('payroll.invoice', [$row->salary_id, $row->salary_hash]) }}" class="inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-xs">{{ translate('payslip') }}</a>@else<a href="{{ route('payroll.create', [$row->id, $month, $year]) }}" class="inline-flex rounded-lg bg-[#2563eb] px-3 py-1.5 text-xs text-white">{{ translate('pay_now') }}</a>@endif</td></tr>@empty<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse
        </tbody></table></div>
    </div>
    @endif
</div>
@endsection
