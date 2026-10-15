@extends('layouts.app')

@section('title', translate('class') . ' ' . translate('schedule'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('class') }} {{ translate('schedule') }}</span>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-funnel class="size-4" /></span>
                {{ translate('select_ground') }}
            </h3>
            @if(get_permission('class_timetable', 'is_add'))
            <a href="{{ route('timetable.set_classwise') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                <x-heroicon-o-plus-circle class="size-4" /> {{ translate('add') }} {{ translate('schedule') }}
            </a>
            @endif
        </div>
        <form method="POST" action="{{ route('timetable.viewclass') }}" class="p-5">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
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
                        @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
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

    @isset($timetables)
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-clock class="size-4" /></span>
                {{ translate('schedule') }} {{ translate('list') }}
            </h3>
            <div class="flex items-center gap-1.5">
                @if(get_permission('class_timetable', 'is_edit'))
                <button type="button" onclick="document.getElementById('editDayModal').classList.remove('hidden')" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#2563eb] transition" title="{{ translate('edit') }}"><x-heroicon-o-pencil-square class="size-4" /></button>
                @endif
                <button onclick="window.print()" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 transition" title="{{ translate('print') }}"><x-heroicon-o-printer class="size-4" /></button>
            </div>
        </div>
        <div class="p-5">
            @if($timetables->count() > 0)
            <div class="overflow-x-auto">
                <div id="printResult">
                    <div class="hidden print:block text-center mb-4">
                        <h4 class="font-bold text-slate-800">{{ $global_config->institute_name ?? '' }}</h4>
                        <p class="text-sm text-slate-600">{{ $global_config->address ?? '' }}</p>
                        <p class="font-bold mt-2">Class Timetable - {{ get_type_name_by_id('class', $class_id) }} ({{ get_type_name_by_id('section', $section_id) }})</p>
                    </div>
                    <table class="w-full text-sm border border-slate-200">
                        <tbody>
                            @php
                                $days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
                                $grouped = $timetables->groupBy('day');
                                $max = $grouped->map->count()->max() ?? 0;
                            @endphp
                            @foreach($days as $day)
                            <tr class="border-b border-slate-200">
                                <td class="px-4 py-3 font-bold uppercase bg-slate-50 text-slate-700 w-32">{{ $day }}</td>
                                @php $rows = $grouped->get($day, collect()); @endphp
                                @foreach($rows as $tt)
                                <td class="px-3 py-3 text-center border-l border-slate-200">
                                    @if($tt->break)
                                        <strong class="text-amber-600">BREAK</strong><br>
                                    @else
                                        <strong class="text-slate-800">{{ get_type_name_by_id('subject', $tt->subject_id) }}</strong><br>
                                    @endif
                                    <small class="text-slate-500">({{ date('g:i A', strtotime($tt->time_start)) }} - {{ date('g:i A', strtotime($tt->time_end)) }})</small><br>
                                    @if(!$tt->break)
                                    <small class="text-slate-600">{{ translate('teacher') }}: {{ get_type_name_by_id('staff', $tt->teacher_id) }}</small>
                                    @endif
                                    @if($tt->class_room)<br><small class="text-slate-500">{{ translate('class_room') }}: {{ $tt->class_room }}</small>@endif
                                </td>
                                @endforeach
                                @for($i = $rows->count(); $i < $max; $i++)
                                <td class="px-3 py-3 text-center border-l border-slate-200 text-slate-400">N/A</td>
                                @endfor
                                @if($max==0)
                                <td class="px-3 py-3 text-center border-l border-slate-200 text-slate-400">N/A</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-center text-sm text-amber-800">Oops! No Schedule Was Made !</div>
            @endif
        </div>
    </div>

    <div id="editDayModal" class="fixed inset-0 z-40 hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="document.getElementById('editDayModal').classList.add('hidden')"></div>
        <div class="relative mx-auto mt-24 w-full max-w-md rounded-2xl bg-white border border-slate-200 shadow-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200">
                <h4 class="text-sm font-bold text-slate-800">{{ translate('schedule') }} {{ translate('edit') }}</h4>
            </div>
            <form method="POST" action="{{ route('timetable.update_classwise') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                <input type="hidden" name="class_id" value="{{ $class_id }}">
                <input type="hidden" name="section_id" value="{{ $section_id }}">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('day') }} <span class="text-red-500">*</span></label>
                    <select name="day" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                        @foreach(['sunday'=>'Sunday','monday'=>'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday','thursday'=>'Thursday','friday'=>'Friday','saturday'=>'Saturday'] as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('editDayModal').classList.add('hidden')" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ translate('cancel') }}</button>
                    <button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2 text-sm font-semibold text-white hover:bg-blue-700">{{ translate('done') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endisset
</div>

@push('scripts')
<script>
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
