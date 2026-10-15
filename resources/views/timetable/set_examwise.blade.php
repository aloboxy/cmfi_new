@extends('layouts.app')

@section('title', translate('add') . ' ' . translate('schedule'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('timetable.viewexam') }}" class="hover:text-[#2563eb]">{{ translate('exam') }} {{ translate('schedule') }}</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('add') }} {{ translate('schedule') }}</span>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2"><span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-funnel class="size-4" /></span> {{ translate('select_ground') }}</h3>
        </div>
        <form method="POST" action="{{ route('timetable.set_examwise') }}" class="p-5">
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
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('exam_name') }} <span class="text-red-500">*</span></label>
                    <select name="exam_id" id="exam_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                        <option value="">{{ translate('select') }}</option>
                        @foreach($exams as $e)
                            @php $term = $e->term_id ? DB::table('exam_term')->where('id',$e->term_id)->value('name') : null; $label = $e->name . ($term ? " ($term)" : ""); @endphp
                            <option value="{{ $e->id }}" @selected($e->id==$exam_id)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
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
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    <x-heroicon-o-funnel class="size-4" /> {{ translate('filter') }}
                </button>
            </div>
        </form>
    </div>

    @isset($subjectassign)
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2"><span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-clock class="size-4" /></span> {{ translate('add') }} {{ translate('schedule') }}</h3>
        </div>
        <form method="POST" action="{{ route('timetable.exam_create') }}" class="p-5">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $exam_id }}">
            <input type="hidden" name="class_id" value="{{ $class_id }}">
            <input type="hidden" name="section_id" value="{{ $section_id }}">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-3 py-3">{{ translate('subject') }}</th>
                            <th class="px-3 py-3">{{ translate('date') }} <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3">{{ translate('starting_time') }} <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3">{{ translate('ending_time') }} <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3">{{ translate('hall_room') }} <span class="text-red-500">*</span></th>
                            @if($examDistribution)
                                @foreach($examDistribution as $distId)<th class="px-3 py-3">{{ get_type_name_by_id('exam_mark_distribution', $distId) }} <span class="text-red-500">*</span></th>@endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($subjectassign as $key=>$row)
                        <tr>
                            <td class="px-3 py-3 font-medium text-slate-800">
                                <input type="hidden" name="timetable[{{ $key }}][subject_id]" value="{{ $row->subject_id ?? $row['subject_id'] }}">
                                {{ get_type_name_by_id('subject', $row->subject_id ?? $row['subject_id']) }}
                            </td>
                            <td class="px-3 py-3">
                                <input type="date" name="timetable[{{ $key }}][date]" value="{{ $row->exam_date ?? '' }}" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                            </td>
                            <td class="px-3 py-3">
                                <input type="time" name="timetable[{{ $key }}][time_start]" value="{{ $row->time_start ?? '' }}" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                            </td>
                            <td class="px-3 py-3">
                                <input type="time" name="timetable[{{ $key }}][time_end]" value="{{ $row->time_end ?? '' }}" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                            </td>
                            <td class="px-3 py-3">
                                <select name="timetable[{{ $key }}][hall_id]" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                                    <option value="">{{ translate('not_selected') }}</option>
                                    @foreach($halls as $hall)<option value="{{ $hall->id }}" @selected(($row->hall_id ?? null)==$hall->id)>{{ $hall->hall_no }}</option>@endforeach
                                </select>
                            </td>
                            @if($examDistribution)
                                @php $getMark = json_decode($row->mark_distribution ?? '{}', true) ?? []; @endphp
                                @foreach($examDistribution as $distId)
                                    @php $full = $getMark[$distId]['full_mark'] ?? ''; $pass = $getMark[$distId]['pass_mark'] ?? ''; @endphp
                                    <td class="px-3 py-3">
                                        <div class="flex gap-1">
                                            <input type="number" name="timetable[{{ $key }}][full_mark][{{ $distId }}]" value="{{ $full }}" placeholder="Full" class="w-20 rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                                            <input type="number" name="timetable[{{ $key }}][pass_mark][{{ $distId }}]" value="{{ $pass }}" placeholder="Pass" class="w-20 rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                                        </div>
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    <x-heroicon-o-check-circle class="size-4" /> {{ translate('save') }}
                </button>
            </div>
        </form>
    </div>
    @endisset
</div>

@push('scripts')
<script>
document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid=this.value;
    fetch('{{ route('ajax.getClassByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})}).then(r=>r.text()).then(html=>{document.getElementById('class_id').innerHTML=html;});
    fetch('{{ route('ajax.getExamByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})}).then(r=>r.text()).then(html=>{document.getElementById('exam_id').innerHTML=html;});
});
document.getElementById('class_id')?.addEventListener('change', function(){
    const cid=this.value;
    fetch('{{ route('ajax.getSectionByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({class_id: cid})}).then(r=>r.text()).then(html=>{document.getElementById('section_id').innerHTML=html;});
});
</script>
@endpush
@endsection
