@extends('layouts.app')
@section('title', translate('salary_template'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('payroll.salary_template') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-left class="size-4" /></a><span class="font-semibold text-slate-800">{{ translate('edit') }} {{ translate('salary_template') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form method="POST" action="{{ route('payroll.salary_template_edit',$template->id) }}" id="editForm" class="space-y-4">
            @csrf
            @if(is_superadmin_loggedin())
            <div><label class="block text-sm font-medium mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label><select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$template->branch_id)>{{ $b->name }}</option>@endforeach</select></div>
            @endif
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1.5">{{ translate('salary_grade') }} <span class="text-red-500">*</span></label><input type="text" name="template_name" value="{{ $template->name }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
                <div><label class="block text-sm font-medium mb-1.5">{{ translate('basic_salary') }} <span class="text-red-500">*</span></label><input type="number" step="0.01" name="basic_salary" id="basic_salary" value="{{ $template->basic_salary }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
                <div><label class="block text-sm font-medium mb-1.5">{{ translate('overtime') }}</label><input type="number" step="0.01" name="overtime_rate" value="{{ $template->overtime_salary }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 p-4"><h4 class="text-sm font-semibold mb-3">{{ translate('allowances') }}</h4>
                    @foreach($allowances as $i=>$al)
                    <div class="flex gap-2 mt-2"><input type="text" name="allowance[{{ $i }}][name]" value="{{ $al->name }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm"><input type="number" step="0.01" name="allowance[{{ $i }}][amount]" value="{{ $al->amount }}" class="allowance w-28 rounded-lg border border-slate-200 px-3 py-2 text-sm"><button type="button" onclick="this.parentElement.remove();calc()" class="rounded-lg bg-red-50 px-2 text-red-600">×</button></div>
                    @endforeach
                    <div id="allowanceRows"></div><button type="button" onclick="addAllowance()" class="mt-3 rounded-lg bg-slate-100 px-3 py-1.5 text-xs">{{ translate('add_rows') }}</button>
                </div>
                <div class="rounded-xl border border-slate-200 p-4"><h4 class="text-sm font-semibold mb-3">{{ translate('deductions') }}</h4>
                    @foreach($deductions as $i=>$de)
                    <div class="flex gap-2 mt-2"><input type="text" name="deduction[{{ $i }}][name]" value="{{ $de->name }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm"><input type="number" step="0.01" name="deduction[{{ $i }}][amount]" value="{{ $de->amount }}" class="deduction w-28 rounded-lg border border-slate-200 px-3 py-2 text-sm"><button type="button" onclick="this.parentElement.remove();calc()" class="rounded-lg bg-red-50 px-2 text-red-600">×</button></div>
                    @endforeach
                    <div id="deductionRows"></div><button type="button" onclick="addDeduction()" class="mt-3 rounded-lg bg-slate-100 px-3 py-1.5 text-xs">{{ translate('add_rows') }}</button>
                </div>
            </div>
            <div class="flex justify-end gap-2"><a href="{{ route('payroll.salary_template') }}" class="rounded-xl bg-slate-100 px-6 py-2.5 text-sm">{{ translate('cancel') }}</a><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('update') }}</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
let alI={{ $allowances->count() }}, deI={{ $deductions->count() }};
function addAllowance(){ const r=`<div class="flex gap-2 mt-2" id="al_${alI}"><input type="text" name="allowance[${alI}][name]" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm"><input type="number" step="0.01" name="allowance[${alI}][amount]" class="allowance w-28 rounded-lg border border-slate-200 px-3 py-2 text-sm"><button type="button" onclick="document.getElementById('al_${alI}').remove()" class="rounded-lg bg-red-50 px-2 text-red-600">×</button></div>`; document.getElementById('allowanceRows').insertAdjacentHTML('beforeend', r); alI++; }
function addDeduction(){ const r=`<div class="flex gap-2 mt-2" id="de_${deI}"><input type="text" name="deduction[${deI}][name]" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm"><input type="number" step="0.01" name="deduction[${deI}][amount]" class="deduction w-28 rounded-lg border border-slate-200 px-3 py-2 text-sm"><button type="button" onclick="document.getElementById('de_${deI}').remove()" class="rounded-lg bg-red-50 px-2 text-red-600">×</button></div>`; document.getElementById('deductionRows').insertAdjacentHTML('beforeend', r); deI++; }
document.getElementById('editForm')?.addEventListener('submit', function(e){ e.preventDefault(); const fd=new FormData(this); fetch(this.action,{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') window.location=j.url; else alert(Object.values(j.error||{}).join("\n")); });});
</script>
@endpush
@endsection
