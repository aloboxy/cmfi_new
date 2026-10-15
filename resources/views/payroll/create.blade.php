@extends('layouts.app')
@section('title', translate('pay_now'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('payroll.index') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-left class="size-4" /></a><span class="font-semibold text-slate-800">{{ translate('salary_details') }}</span></div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-200 p-5 space-y-4">
            <div class="flex gap-4">
                <img src="{{ get_image_url('staff', $staff['photo'] ?? '') }}" class="size-20 rounded-xl object-cover border border-slate-200" alt="">
                <div class="text-sm"><p class="font-semibold text-slate-800">{{ $staff['name'] }}</p><p class="text-slate-500">{{ translate('designation') }}: {{ $staff['designation_name'] }}</p><p class="text-slate-500">{{ translate('department') }}: {{ $staff['department_name'] }}</p><p class="text-slate-500">{{ translate('joining_date') }}: {{ _d($staff['joining_date'] ?? '') }}</p></div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm"><div>{{ translate('salary_grade') }}: <span class="font-medium">{{ $staff['template_name'] }}</span></div><div>{{ translate('basic_salary') }}: {{ $staff['basic_salary'] }}</div><div>{{ translate('overtime') }}: {{ $staff['overtime_salary'] }}</div></div>
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 p-3"><h4 class="text-sm font-semibold mb-2">{{ translate('allowances') }}</h4><table class="w-full text-sm">@foreach($allowances as $a)<tr class="border-b"><td class="py-1">{{ $a->name }}</td><td class="py-1 text-right">{{ $a->amount }}</td></tr>@endforeach @if($allowances->isEmpty())<tr><td colspan="2" class="py-3 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endif</table></div>
                <div class="rounded-xl border border-slate-200 p-3"><h4 class="text-sm font-semibold mb-2">{{ translate('deductions') }}</h4><table class="w-full text-sm">@foreach($deductions as $d)<tr class="border-b"><td class="py-1">{{ $d->name }}</td><td class="py-1 text-right">{{ $d->amount }}</td></tr>@endforeach @if(!empty($advance))<tr><td class="py-1">Advance Salary</td><td class="py-1 text-right">{{ $advance }}</td></tr>@endif @if($deductions->isEmpty() && empty($advance))<tr><td colspan="2" class="py-3 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endif</table></div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="text-sm font-bold mb-4">{{ translate('payment_details') }}</h3>
            <form method="POST" action="{{ route('payroll.create', [$staff['id'], $month, $year]) }}" class="space-y-3">
                @csrf
                <input type="hidden" name="paid" value="1"><input type="hidden" name="branch_id" value="{{ $staff['branch_id'] }}"><input type="hidden" name="staff_id" value="{{ $staff['id'] }}"><input type="hidden" name="basic_salary" value="{{ $staff['basic_salary'] }}"><input type="hidden" name="salary_template_id" value="{{ $staff['salary_template_id'] }}"><input type="hidden" name="month" value="{{ $month }}"><input type="hidden" name="year" value="{{ $year }}">
                @php $total_allowance = $allowances->sum('amount'); $total_deduction = $deductions->sum('amount') + ($advance ?? 0); $net = $staff['basic_salary'] + $total_allowance - $total_deduction; @endphp
                <div><label class="block text-sm font-medium mb-1">{{ translate('total_allowance') }}</label><input type="number" name="total_allowance" id="total_allowance" value="{{ $total_allowance }}" readonly class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('total_deduction') }}</label><input type="number" name="total_deduction" id="total_deduction" value="{{ $total_deduction }}" readonly class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('overtime_total_hour') }}</label><input type="number" step="0.01" id="overtime_total_hour" name="overtime_total_hour" value="0" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('overtime_amount') }}</label><input type="number" id="overtime_amount_view" value="0" readonly class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50"><input type="hidden" id="overtime_amount" name="overtime_amount" value="0"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('net_salary') }}</label><input type="text" name="net_salary" id="net_salary" value="{{ $net }}" readonly class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('pay_via') }} <span class="text-red-500">*</span></label><select name="pay_via" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required><option value="">{{ translate('select') }}</option>@foreach($payvia_list as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('remarks') }}</label><textarea name="remarks" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></textarea></div>
                <button type="submit" class="w-full rounded-xl bg-[#2563eb] py-2.5 text-sm font-semibold text-white">{{ translate('paid') }}</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('overtime_total_hour')?.addEventListener('input', function(){
    const perHour = parseFloat({{ floatval($staff['overtime_salary'] ?? 0) }});
    const total_allowance_init = parseFloat({{ $total_allowance }});
    const net_init = parseFloat({{ $net }});
    const overtime_hour = parseFloat(this.value)||0;
    const overtime_amount = overtime_hour * perHour;
    document.getElementById('overtime_amount_view').value = overtime_amount;
    document.getElementById('overtime_amount').value = overtime_amount;
    document.getElementById('total_allowance').value = total_allowance_init + overtime_amount;
    document.getElementById('net_salary').value = net_init + overtime_amount;
});
</script>
@endpush
@endsection
