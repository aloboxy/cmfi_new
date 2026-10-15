@extends('layouts.app')
@section('title', translate('payslip'))
@section('content')
<div class="space-y-5">
    <div id="payslipPrint" class="rounded-2xl bg-white border border-slate-200 p-6">
        <div class="flex justify-between items-start">
            <img src="{{ asset('uploads/app_image/printing-logo.png') }}" alt="logo" class="h-12">
            <div class="text-right text-sm"><h4 class="font-bold text-slate-800">Payslip No #{{ $salary['bill_no'] }}</h4><p>{{ translate('date') }} : {{ _d($salary['created_at'] ?? now()) }}</p><p>{{ translate('salary_month') }} : {{ date('F', mktime(0,0,0,$salary['month'],1)) }} {{ $salary['year'] }}</p></div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-6 text-sm">
            <div><p class="font-semibold">To :</p><p>{{ $salary['staff_name'] }}</p><p>{{ translate('department') }} : {{ $salary['department_name'] }}</p><p>{{ translate('designation') }} : {{ $salary['designation_name'] }}</p><p>{{ translate('mobile_no') }} : {{ $salary['mobileno'] }}</p></div>
            <div class="text-right"><p class="font-semibold">From :</p><p>{{ $salary['school_name'] }}</p><p>{{ $salary['school_address'] }}</p><p>{{ $salary['school_mobileno'] }}</p><p>{{ $salary['school_email'] }}</p></div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div class="rounded-xl border border-slate-200 p-3"><h4 class="font-semibold text-sm mb-2">{{ translate('allowances') }}</h4><table class="w-full text-sm"><thead><tr class="text-xs text-slate-500"><th class="text-left py-1">{{ translate('name') }}</th><th class="text-right py-1">{{ translate('amount') }}</th></tr></thead><tbody>@forelse($detailsAllowance as $a)<tr><td class="py-1">{{ $a->name }}</td><td class="py-1 text-right">{{ $a->amount }}</td></tr>@empty<tr><td colspan="2" class="py-4 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse</tbody></table></div>
            <div class="rounded-xl border border-slate-200 p-3"><h4 class="font-semibold text-sm mb-2">{{ translate('deductions') }}</h4><table class="w-full text-sm"><thead><tr class="text-xs text-slate-500"><th class="text-left py-1">{{ translate('name') }}</th><th class="text-right py-1">{{ translate('amount') }}</th></tr></thead><tbody>@forelse($detailsDeduction as $d)<tr><td class="py-1">{{ $d->name }}</td><td class="py-1 text-right">{{ $d->amount }}</td></tr>@empty<tr><td colspan="2" class="py-4 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse</tbody></table></div>
        </div>
        <div class="mt-6 text-right text-sm space-y-1"><p><strong>{{ translate('basic_salary') }} :</strong> {{ $salary['basic_salary'] }}</p><p><strong>{{ translate('total_allowance') }} :</strong> {{ $salary['total_allowance'] }}</p><p><strong>{{ translate('total_deduction') }} :</strong> {{ $salary['total_deduction'] }}</p><p class="text-base font-bold"><strong>{{ translate('net_salary') }} :</strong> {{ $salary['net_salary'] }}</p></div>
    </div>
    <div class="flex justify-end"><button onclick="window.print()" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('print') }}</button></div>
</div>
@endsection
