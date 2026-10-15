@extends('layouts.app')

@section('title', translate('exam') . ' ' . translate('schedule'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('exam') }} {{ translate('schedule') }}</span>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2"><span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-funnel class="size-4" /></span> {{ translate('select_ground') }}</h3>
        </div>
        <form method="POST" action="{{ route('timetable.viewexam') }}" class="p-5">
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

    @isset($examlist)
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2"><span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-clock class="size-4" /></span> {{ translate('schedule') }} {{ translate('list') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">#</th>
                        @if(is_superadmin_loggedin())<th class="px-4 py-3">{{ translate('branch') }}</th>@endif
                        <th class="px-4 py-3">{{ translate('exam_name') }}</th>
                        <th class="px-4 py-3 text-right">{{ translate('action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($examlist as $i=>$row)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                        @if(is_superadmin_loggedin())<td class="px-4 py-3 text-slate-600">{{ $row->branch_name }}</td>@endif
                        <td class="px-4 py-3 font-medium text-slate-800">
                            @php $examName = DB::table('exam')->where('id', $row->exam_id)->first(); $termName = $examName && $examName->term_id ? DB::table('exam_term')->where('id', $examName->term_id)->value('name') : null; @endphp
                            {{ $examName->name ?? '-' }} @if($termName) ({{ $termName }}) @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" onclick="getExamTimetableM({{ $row->exam_id }}, {{ $row->class_id }}, {{ $row->section_id }})" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#2563eb] transition"><x-heroicon-o-eye class="size-4" /></button>
                                @if(get_permission('exam_timetable', 'is_delete'))
                                <form method="POST" action="{{ route('timetable.exam_delete', ['examId'=>$row->exam_id, 'classId'=>$row->class_id, 'sectionId'=>$row->section_id]) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-600 transition"><x-heroicon-o-trash class="size-4" /></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endisset
</div>

<div id="examModal" class="fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="document.getElementById('examModal').classList.add('hidden')"></div>
    <div class="relative mx-auto mt-10 w-full max-w-3xl rounded-2xl bg-white border border-slate-200 shadow-xl overflow-hidden max-h-[80vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-5 py-4 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800">{{ translate('exam') }} {{ translate('schedule') }}</h4>
            <button type="button" onclick="document.getElementById('examModal').classList.add('hidden')" class="grid size-8 place-items-center rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100"><x-heroicon-o-x-mark class="size-4" /></button>
        </div>
        <div id="quick_view" class="p-5"></div>
    </div>
</div>

@push('scripts')
<script>
function getExamTimetableM(examId, classId, sectionId){
    fetch('{{ route('timetable.getExamTimetableM') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({exam_id: examId, class_id: classId, section_id: sectionId})})
        .then(r=>r.text()).then(html=>{document.getElementById('quick_view').innerHTML=html; document.getElementById('examModal').classList.remove('hidden');});
}
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
