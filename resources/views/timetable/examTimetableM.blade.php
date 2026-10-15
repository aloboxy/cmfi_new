<div class="space-y-3">
    @if($timetables->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-slate-200">
            <thead>
                <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-2">#</th>
                    <th class="px-3 py-2">{{ translate('subject') }}</th>
                    <th class="px-3 py-2">{{ translate('date') }}</th>
                    <th class="px-3 py-2">{{ translate('starting_time') }}</th>
                    <th class="px-3 py-2">{{ translate('ending_time') }}</th>
                    <th class="px-3 py-2">{{ translate('hall_room') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($timetables as $i=>$row)
                <tr>
                    <td class="px-3 py-2 text-slate-500">{{ $i+1 }}</td>
                    <td class="px-3 py-2 font-medium text-slate-800">{{ $row->subject_name }}</td>
                    <td class="px-3 py-2 text-slate-600">{{ $row->exam_date ? date('Y-m-d', strtotime($row->exam_date)) : '-' }}</td>
                    <td class="px-3 py-2 text-slate-600">{{ $row->time_start }}</td>
                    <td class="px-3 py-2 text-slate-600">{{ $row->time_end }}</td>
                    <td class="px-3 py-2 text-slate-600">{{ $row->hall_no ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-center text-sm text-amber-800">{{ translate('no_information_available') }}</div>
    @endif
</div>
