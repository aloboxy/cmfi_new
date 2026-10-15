@extends('layouts.app')
@section('title', translate('student_promotion'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('student_promotion') }}</span>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('student_promotion') }}</div>
        <div class="p-5">
            <form method="POST" action="{{ route('student.transfer') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @if(is_superadmin_loggedin())
                    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }}</label><select name="branch_id" id="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
                    @endif
                    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class') }}</label><select name="class_id" id="class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option>@foreach($classes as $c)<option value="{{ $c->id }}" @selected(($data['class_id']??null)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }}</label><select name="section_id" id="section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option></select></div>
                </div>
                <button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('search') }}</button>
            </form>
        </div>
    </div>
    @if(isset($students))
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">{{ translate('student_list') }} ({{ count($students) }})</h3>
        </div>
        <form id="promoteForm" class="p-5 space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('promote_to_session') }} <span class="text-red-500">*</span></label>
                    <select name="promote_session_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach(DB::table('schoolyear')->get() as $y)<option value="{{ $y->id }}">{{ $y->school_year }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('promote_to_class') }} <span class="text-red-500">*</span></label>
                    <select name="promote_class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('promote_section_id') }} <span class="text-red-500">*</span></label>
                    <select name="promote_section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option></select>
                </div>
            </div>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead><tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-4 py-3"><input type="checkbox" id="selectAllProm"></th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('register_no') }}</th><th class="px-4 py-3">{{ translate('roll') }}</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $idx=>$s)
                        <tr>
                            <td class="px-4 py-2"><input type="checkbox" class="cb_prom" checked onchange="document.getElementById('prom_roll_{{ $idx }}').disabled=!this.checked; if(!this.checked) this.nextElementSibling?.remove();">
                                <input type="hidden" name="promote[{{ $idx }}][student_id]" value="{{ $s->student_id }}">
                                <input type="hidden" name="promote[{{ $idx }}][enroll_id]" value="{{ $s->id }}">
                            </td>
                            <td class="px-4 py-2 font-medium">{{ $s->fullname }}</td>
                            <td class="px-4 py-2">{{ $s->register_no }}</td>
                            <td class="px-4 py-2"><input type="text" name="promote[{{ $idx }}][roll]" id="prom_roll_{{ $idx }}" value="{{ $s->roll }}" class="w-24 rounded-lg border border-slate-200 px-2 py-1 text-sm"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('promote') }}</button>
            </div>
        </form>
    </div>
    @endif
</div>
@push('scripts')
<script>
document.getElementById('selectAllProm')?.addEventListener('change', function(){document.querySelectorAll('.cb_prom').forEach(cb=>cb.checked=this.checked);});
document.getElementById('promoteForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const fd=new FormData(this);
    // only send checked
    document.querySelectorAll('.cb_prom:not(:checked)').forEach(cb=>{
        cb.closest('tr').querySelectorAll('input[name^="promote"]').forEach(inp=>fd.delete(inp.name));
    });
    fetch('{{ route('student.transfersave') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:fd})
    .then(r=>r.json()).then(d=>{
        if(d.status=='success'){ alert('{{ translate('information_has_been_updated_successfully') }}'); location.href=d.url; }
        else alert(JSON.stringify(d.error));
    });
});
</script>
@endpush
@endsection
