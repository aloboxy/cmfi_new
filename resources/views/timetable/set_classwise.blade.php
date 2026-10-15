@extends('layouts.app')

@section('title', translate('add') . ' ' . translate('schedule'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('timetable.viewclass') }}" class="hover:text-[#2563eb]">{{ translate('class') }} {{ translate('schedule') }}</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('add') }} {{ translate('schedule') }}</span>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2"><span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-funnel class="size-4" /></span> {{ translate('select_ground') }}</h3>
        </div>
        <form method="POST" action="{{ route('timetable.set_classwise') }}" class="p-5">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                @if(is_superadmin_loggedin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                    <select name="branch_id" id="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$branch_id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class') }} <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                        <option value="">{{ translate('select') }}</option>
                        @foreach($classes as $c)<option value="{{ $c->id }}" @selected($c->id==$class_id)>{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }} <span class="text-red-500">*</span></label>
                    <select name="section_id" id="section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                        <option value="">{{ translate('select_class_first') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('day') }} <span class="text-red-500">*</span></label>
                    <select name="day" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                        @foreach(['sunday'=>'Sunday','monday'=>'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday','thursday'=>'Thursday','friday'=>'Friday','saturday'=>'Saturday'] as $k=>$v)
                        <option value="{{ $k }}" @selected($k==$day)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    <x-heroicon-o-funnel class="size-4" /> {{ translate('filter') }}
                </button>
            </div>
        </form>
    </div>

    @if(!empty($branch_id) && !empty($class_id) && !empty($day))
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
            <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-clock class="size-4" /></span>
            <h3 class="text-sm font-bold text-slate-800">{{ translate('add') }} {{ translate('schedule') }}</h3>
        </div>
        <form method="POST" action="{{ route('timetable.class_save', 'new') }}" class="p-5">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class_id }}">
            <input type="hidden" name="section_id" value="{{ $section_id }}">
            <input type="hidden" name="day" value="{{ $day }}">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-3 py-3 w-20 text-center">BREAK</th>
                            <th class="px-3 py-3">{{ translate('subject') }} <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3">{{ translate('teacher') }} <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3">{{ translate('starting_time') }} <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3">{{ translate('ending_time') }} <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3">{{ translate('class_room') }}</th>
                        </tr>
                    </thead>
                    <tbody id="timetable_entry_append">
                        <tr class="iadd border-b border-slate-100">
                            <td class="px-3 py-3 text-center">
                                <label class="inline-flex items-center cursor-pointer"><input type="checkbox" name="timetable[0][break]" class="rounded border-slate-300 text-[#2563eb] focus:ring-[#2563eb] break-checkbox"></label>
                            </td>
                            <td class="px-3 py-3">
                                <select name="timetable[0][subject]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm bg-white subject-select">
                                    <option value="">{{ translate('select') }}</option>
                                    @foreach($subjectAssign as $sa)<option value="{{ $sa->subject_id }}">{{ get_type_name_by_id('subject', $sa->subject_id) }}</option>@endforeach
                                </select>
                            </td>
                            <td class="px-3 py-3">
                                <select name="timetable[0][teacher]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm bg-white teacher-select">
                                    <option value="">{{ translate('select') }}</option>
                                    @foreach($teachers as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach
                                </select>
                            </td>
                            <td class="px-3 py-3">
                                <input type="time" name="timetable[0][time_start]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                            </td>
                            <td class="px-3 py-3">
                                <input type="time" name="timetable[0][time_end]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                            </td>
                            <td class="px-3 py-3">
                                <input type="text" name="timetable[0][class_room]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" placeholder="{{ translate('class_room') }}">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" onclick="appendRow()" class="mt-3 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <x-heroicon-o-plus-circle class="size-4" /> {{ translate('add_more') }}
            </button>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    <x-heroicon-o-check-circle class="size-4" /> {{ translate('save') }}
                </button>
            </div>
        </form>
    </div>
    @endif
</div>

@push('scripts')
<script>
let rowCount = 1;
function appendRow(){
    const tbody = document.getElementById('timetable_entry_append');
    const tr = document.createElement('tr');
    tr.className = 'iadd border-b border-slate-100';
    tr.innerHTML = `
        <td class="px-3 py-3 text-center"><label class="inline-flex items-center cursor-pointer"><input type="checkbox" name="timetable[${rowCount}][break]" class="rounded border-slate-300 text-[#2563eb] break-checkbox"></label></td>
        <td class="px-3 py-3"><select name="timetable[${rowCount}][subject]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm bg-white"><option value="">{{ translate('select') }}</option>@foreach($subjectAssign as $sa)<option value="{{ $sa->subject_id }}">{{ get_type_name_by_id('subject', $sa->subject_id) }}</option>@endforeach</select></td>
        <td class="px-3 py-3"><select name="timetable[${rowCount}][teacher]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm bg-white"><option value="">{{ translate('select') }}</option>@foreach($teachers as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></td>
        <td class="px-3 py-3"><input type="time" name="timetable[${rowCount}][time_start]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm" required></td>
        <td class="px-3 py-3"><input type="time" name="timetable[${rowCount}][time_end]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm" required></td>
        <td class="px-3 py-3 flex gap-1"><input type="text" name="timetable[${rowCount}][class_room]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm"><button type="button" onclick="this.closest('tr').remove()" class="grid size-8 place-items-center rounded-lg bg-red-50 border border-red-200 text-red-600 hover:bg-red-100"><svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>
    `;
    tbody.appendChild(tr);
    rowCount++;
    attachBreakHandlers();
}
function attachBreakHandlers(){
    document.querySelectorAll('.break-checkbox').forEach(cb=>{
        cb.addEventListener('change', function(){
            const tr=this.closest('tr');
            const selects=tr.querySelectorAll('select');
            selects.forEach(s=>s.disabled=this.checked);
        });
    });
}
attachBreakHandlers();

document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid=this.value;
    fetch('{{ route('ajax.getClassByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})}).then(r=>r.text()).then(html=>{document.getElementById('class_id').innerHTML=html;});
});
document.getElementById('class_id')?.addEventListener('change', function(){
    const cid=this.value;
    fetch('{{ route('ajax.getSectionByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({class_id: cid})}).then(r=>r.text()).then(html=>{document.getElementById('section_id').innerHTML=html;});
});
</script>
@endpush
@endsection
