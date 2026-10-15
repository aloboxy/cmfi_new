@extends('layouts.app')
@section('title', translate('salary_template'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('salary_template') }}</span>
    </div>

    <div x-data="{ tab: 'list' }" class="space-y-4">
        <div class="flex items-center gap-2">
            <button @click="tab='list'" :class="tab==='list' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200'" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition">{{ translate('template_list') }}</button>
            @if(get_permission('salary_template','is_add'))
            <button @click="tab='create'" :class="tab==='create' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200'" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition">{{ translate('create_template') }}</button>
            @endif
        </div>

        <div x-show="tab==='list'" x-cloak class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2"><span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-list-bullet class="size-4" /></span> {{ translate('salary_template') }} {{ translate('list') }}</h3>
                <span class="text-xs text-slate-500">{{ $templates->count() }} {{ translate('template') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('branch') }}</th><th class="px-4 py-3">{{ translate('salary_grade') }}</th><th class="px-4 py-3">{{ translate('basic_salary') }}</th><th class="px-4 py-3">{{ translate('overtime') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($templates as $i=>$row)
                        <tr class="hover:bg-slate-50/60"><td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td><td class="px-4 py-3">{{ get_type_name_by_id('branch',$row->branch_id) }}</td><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3">{{ $row->basic_salary }}</td><td class="px-4 py-3">{{ $row->overtime_salary }}</td>
                            <td class="px-4 py-3"><div class="flex items-center justify-end gap-1.5">
                                <button onclick="viewTemplate({{ $row->id }})" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-[#2563eb]"><x-heroicon-o-eye class="size-4" /></button>
                                @if(get_permission('salary_template','is_edit'))<a href="{{ route('payroll.salary_template_edit',$row->id) }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-[#2563eb]"><x-heroicon-o-pencil-square class="size-4" /></a>@endif
                                @if(get_permission('salary_template','is_delete'))<form method="POST" action="{{ route('payroll.salary_template_delete',$row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">@csrf @method('DELETE')<button class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-red-600"><x-heroicon-o-trash class="size-4" /></button></form>@endif
                            </div></td></tr>
                        @empty<tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(get_permission('salary_template','is_add'))
        <div x-show="tab==='create'" x-cloak class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2"><span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-plus-circle class="size-4" /></span><h3 class="text-sm font-bold text-slate-800">{{ translate('create_template') }}</h3></div>
            <form method="POST" action="{{ route('payroll.salary_template') }}" id="salaryTemplateForm" class="p-5 space-y-4">
                @csrf
                @if(is_superadmin_loggedin())
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label><select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('salary_grade') }} <span class="text-red-500">*</span></label><input type="text" name="template_name" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('basic_salary') }} <span class="text-red-500">*</span></label><input type="number" step="0.01" name="basic_salary" id="basic_salary" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('overtime') }} (per hour)</label><input type="number" step="0.01" name="overtime_rate" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-200 p-4"><h4 class="text-sm font-semibold text-slate-800 mb-3">{{ translate('allowances') }}</h4><div class="space-y-2"><div class="flex gap-2"><input type="text" name="allowance[0][name]" placeholder="{{ translate('name_of_allowance') }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm"><input type="number" step="0.01" name="allowance[0][amount]" placeholder="{{ translate('amount') }}" class="allowance w-28 rounded-lg border border-slate-200 px-3 py-2 text-sm"></div><div id="allowanceRows"></div></div><button type="button" onclick="addAllowance()" class="mt-3 inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700">{{ translate('add_rows') }}</button></div>
                    <div class="rounded-xl border border-slate-200 p-4"><h4 class="text-sm font-semibold text-slate-800 mb-3">{{ translate('deductions') }}</h4><div class="space-y-2"><div class="flex gap-2"><input type="text" name="deduction[0][name]" placeholder="{{ translate('name_of_deductions') }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm"><input type="number" step="0.01" name="deduction[0][amount]" placeholder="{{ translate('amount') }}" class="deduction w-28 rounded-lg border border-slate-200 px-3 py-2 text-sm"></div><div id="deductionRows"></div></div><button type="button" onclick="addDeduction()" class="mt-3 inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700">{{ translate('add_rows') }}</button></div>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 grid grid-cols-2 gap-3 text-sm">
                    <div>{{ translate('total_allowance') }}: <span id="total_allowance_view">0</span></div>
                    <div>{{ translate('total_deduction') }}: <span id="total_deduction_view">0</span></div>
                    <div class="col-span-2 font-bold">{{ translate('net_salary') }}: <span id="net_salary_view">0</span></div>
                </div>
                <div class="flex justify-end"><button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700">{{ translate('save') }}</button></div>
            </form>
        </div>
        @endif
    </div>

    <div id="viewModal" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50"><div class="bg-white rounded-2xl max-w-2xl w-full p-6 m-4"><div id="viewContent"></div><div class="mt-4 text-right"><button onclick="closeView()" class="rounded-xl bg-slate-100 px-4 py-2 text-sm">{{ translate('close') }}</button></div></div></div>
</div>
@push('scripts')
<script>
let alI=1, deI=1;
function addAllowance(){ const r=`<div class="flex gap-2 mt-2" id="al_${alI}"><input type="text" name="allowance[${alI}][name]" placeholder="{{ translate('name_of_allowance') }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm"><input type="number" step="0.01" name="allowance[${alI}][amount]" placeholder="{{ translate('amount') }}" class="allowance w-28 rounded-lg border border-slate-200 px-3 py-2 text-sm"><button type="button" onclick="document.getElementById('al_${alI}').remove();calc()" class="rounded-lg bg-red-50 px-2 text-red-600">×</button></div>`; document.getElementById('allowanceRows').insertAdjacentHTML('beforeend', r); alI++; }
function addDeduction(){ const r=`<div class="flex gap-2 mt-2" id="de_${deI}"><input type="text" name="deduction[${deI}][name]" placeholder="{{ translate('name_of_deductions') }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm"><input type="number" step="0.01" name="deduction[${deI}][amount]" placeholder="{{ translate('amount') }}" class="deduction w-28 rounded-lg border border-slate-200 px-3 py-2 text-sm"><button type="button" onclick="document.getElementById('de_${deI}').remove();calc()" class="rounded-lg bg-red-50 px-2 text-red-600">×</button></div>`; document.getElementById('deductionRows').insertAdjacentHTML('beforeend', r); deI++; }
function calc(){ let ta=0, td=0; document.querySelectorAll('.allowance').forEach(e=>ta+=parseFloat(e.value)||0); document.querySelectorAll('.deduction').forEach(e=>td+=parseFloat(e.value)||0); document.getElementById('total_allowance_view').textContent=ta; document.getElementById('total_deduction_view').textContent=td; let basic=parseFloat(document.getElementById('basic_salary').value)||0; document.getElementById('net_salary_view').textContent=(basic+ta-td); }
document.addEventListener('input', calc);
function viewTemplate(id){ fetch('{{ url('payroll/salary_template_view') }}/'+id).then(r=>r.text()).then(h=>{ document.getElementById('viewContent').innerHTML=h; document.getElementById('viewModal').classList.remove('hidden'); document.getElementById('viewModal').classList.add('flex');});}
function closeView(){ document.getElementById('viewModal').classList.add('hidden'); document.getElementById('viewModal').classList.remove('flex');}
document.getElementById('salaryTemplateForm')?.addEventListener('submit', function(e){ e.preventDefault(); const form=this; const fd=new FormData(form); fetch(form.action,{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') window.location=j.url; else { alert(Object.values(j.error||j.errors||{}).flat().join("\n")); } }).catch(()=>form.submit()); });
</script>
@endpush
@endsection
