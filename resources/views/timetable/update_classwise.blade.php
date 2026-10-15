@extends('layouts.app')

@section('title', translate('class') . ' ' . translate('schedule'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('timetable.viewclass') }}" class="hover:text-[#2563eb]">{{ translate('class') }} {{ translate('schedule') }}</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('schedule') }} {{ translate('edit') }}</span>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-amber-500/10 text-amber-600"><x-heroicon-o-pencil-square class="size-4" /></span>
                {{ $timetables->count() > 0 ? translate('class') . ' ' . get_type_name_by_id('class', $class_id) . ' (' . translate('section') . ': ' . get_type_name_by_id('section', $section_id) . ') - ' . ucfirst($day) . ' - ' : '' }}{{ translate('schedule') }} {{ translate('edit') }}
            </h3>
        </div>
        <form method="POST" action="{{ route('timetable.class_save', 'update') }}" class="p-5">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class_id }}">
            <input type="hidden" name="section_id" value="{{ $section_id }}">
            <input type="hidden" name="day" value="{{ $day }}">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">

            @if($timetables->count() > 0)
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
                    <tbody id="timetable_entry">
                        @foreach($timetables as $key => $tt)
                        <tr class="border-b border-slate-100">
                            <input type="hidden" name="i[]" value="{{ $tt->id }}">
                            <td class="px-3 py-3 text-center">
                                <label class="inline-flex items-center cursor-pointer"><input type="checkbox" name="timetable[{{ $key }}][break]" @checked($tt->break) class="rounded border-slate-300 text-[#2563eb] break-checkbox"></label>
                            </td>
                            <td class="px-3 py-3">
                                <select name="timetable[{{ $key }}][subject]" @disabled($tt->break) class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm bg-white">
                                    <option value="">{{ translate('select') }}</option>
                                    @foreach($subjects as $sub)<option value="{{ $sub->id }}" @selected($sub->id==$tt->subject_id)>{{ $sub->name }}</option>@endforeach
                                </select>
                            </td>
                            <td class="px-3 py-3">
                                <select name="timetable[{{ $key }}][teacher]" @disabled($tt->break) class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm bg-white">
                                    <option value="">{{ translate('select') }}</option>
                                    @foreach($teachers as $id=>$name)<option value="{{ $id }}" @selected($id==$tt->teacher_id)>{{ $name }}</option>@endforeach
                                </select>
                            </td>
                            <td class="px-3 py-3">
                                <input type="time" name="timetable[{{ $key }}][time_start]" value="{{ date('H:i', strtotime($tt->time_start)) }}" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                            </td>
                            <td class="px-3 py-3">
                                <input type="time" name="timetable[{{ $key }}][time_end]" value="{{ date('H:i', strtotime($tt->time_end)) }}" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                            </td>
                            <td class="px-3 py-3 flex gap-1">
                                <input type="text" name="timetable[{{ $key }}][class_room]" value="{{ $tt->class_room }}" class="w-full rounded-xl border border-slate-200 px-2 py-2 text-sm">
                                <button type="button" onclick="this.closest('tr').remove()" class="grid size-8 place-items-center rounded-lg bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 shrink-0"><x-heroicon-o-trash class="size-4" /></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-center text-sm text-amber-800">Oops! No Schedule Was Made !</div>
            @endif

            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    <x-heroicon-o-check-circle class="size-4" /> {{ translate('update') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.break-checkbox').forEach(cb=>{
    cb.addEventListener('change', function(){
        const tr=this.closest('tr');
        tr.querySelectorAll('select').forEach(s=>s.disabled=this.checked);
    });
});
document.getElementById('timetable_entry')?.addEventListener('click', function(e){
    if(e.target.closest('.removeTR')) e.target.closest('tr').remove();
});
</script>
@endpush
@endsection
