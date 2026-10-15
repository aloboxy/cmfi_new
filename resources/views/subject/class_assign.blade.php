@extends('layouts.app')

@section('title', translate('class_assign'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('subject.index') }}" class="hover:text-[#2563eb]">{{ translate('subject') }}</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('class_assign') }}</span>
    </div>

    <div x-data="{ tab: 'list' }" class="space-y-4">
        <div class="flex gap-2">
            <button @click="tab='list'" :class="tab==='list' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="rounded-xl px-4 py-2 text-sm font-medium flex items-center gap-2"><x-heroicon-o-list-bullet class="size-4" /> {{ translate('assign') }} {{ translate('list') }}</button>
            @if(get_permission('subject_class_assign', 'is_add'))
            <button @click="tab='create'" :class="tab==='create' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="rounded-xl px-4 py-2 text-sm font-medium flex items-center gap-2"><x-heroicon-o-plus-circle class="size-4" /> {{ translate('assign') }}</button>
            @endif
        </div>

        <div x-show="tab==='list'" class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-list-bullet class="size-4" /></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('assign') }} {{ translate('list') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3">#</th>
                            @if(is_superadmin_loggedin())<th class="px-4 py-3">{{ translate('branch') }}</th>@endif
                            <th class="px-4 py-3">{{ translate('class') }}</th>
                            <th class="px-4 py-3">{{ translate('section') }}</th>
                            <th class="px-4 py-3">{{ translate('subject') }}</th>
                            <th class="px-4 py-3 text-right">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assignlist as $i=>$row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                            @if(is_superadmin_loggedin())<td class="px-4 py-3 text-slate-600">{{ $row->branch_name }}</td>@endif
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $row->class_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->section_name ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                @php
                                    $subs = DB::table('subject_assign as sa')->join('subject as s','s.id','=','sa.subject_id')->where('sa.class_id',$row->class_id)->where('sa.section_id',$row->section_id)->where('sa.branch_id',$row->branch_id)->where('sa.session_id', get_session_id())->get();
                                @endphp
                                @foreach($subs as $s) <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs mr-1 mb-1">{{ $s->name }}</span> @endforeach
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(get_permission('subject_class_assign', 'is_edit'))
                                    <button type="button" onclick="openEditModal({{ $row->class_id }}, {{ $row->section_id }})" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#2563eb] transition"><x-heroicon-o-pencil-square class="size-4" /></button>
                                    @endif
                                    @if(get_permission('subject_class_assign', 'is_delete'))
                                    <form method="POST" action="{{ route('subject.class_assign_delete', ['classId'=>$row->class_id, 'sectionId'=>$row->section_id]) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-600 transition"><x-heroicon-o-trash class="size-4" /></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(get_permission('subject_class_assign', 'is_add'))
        <div x-show="tab==='create'" x-cloak class="rounded-2xl bg-white border border-slate-200 overflow-hidden max-w-3xl">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-plus-circle class="size-4" /></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('assign') }} {{ translate('subject') }}</h3>
            </div>
            <form method="POST" action="{{ route('subject.class_assign_save') }}" class="p-5 space-y-4">
                @csrf
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
                    <select name="class_id" id="class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }} <span class="text-red-500">*</span></label>
                    <select name="section_id" id="section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                        <option value="">{{ translate('select_class_first') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('subject') }} <span class="text-red-500">*</span></label>
                    <select name="subjects[]" id="subject_holder" multiple class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none min-h-[140px]">
                        @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400">{{ translate('select_multiple_subject') }}</p>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition"><x-heroicon-o-check-circle class="size-4" /> {{ translate('save') }}</button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Edit Modal --}}
<div id="editClassAssignModal" class="fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeClassAssignModal()"></div>
    <div class="relative mx-auto mt-16 w-full max-w-lg rounded-2xl bg-white border border-slate-200 shadow-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2"><x-heroicon-o-pencil-square class="size-4 text-[#2563eb]" /> {{ translate('edit_assign') }}</h4>
            <button type="button" onclick="closeClassAssignModal()" class="grid size-8 place-items-center rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100"><x-heroicon-o-x-mark class="size-4" /></button>
        </div>
        <form method="POST" action="{{ route('subject.class_assign_edit') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="branch_id" id="ebranch_id" value="">
            <input type="hidden" name="class_id" id="eclass_id" value="">
            <input type="hidden" name="section_id" id="esection_id" value="">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('subject') }} <span class="text-red-500">*</span></label>
                <select name="subjects[]" id="esubject_holder" multiple class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none min-h-[160px]"></select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeClassAssignModal()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ translate('cancel') }}</button>
                <button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2 text-sm font-semibold text-white hover:bg-blue-700">{{ translate('update') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditModal(classId, sectionId){
    fetch('{{ route('ajax.getClassAssignM') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({class_id: classId, section_id: sectionId})})
        .then(r=>r.json()).then(data=>{
            document.getElementById('ebranch_id').value = data.branch_id;
            document.getElementById('eclass_id').value = data.class_id;
            document.getElementById('esection_id').value = data.section_id;
            document.getElementById('esubject_holder').innerHTML = data.subject;
            document.getElementById('editClassAssignModal').classList.remove('hidden');
        });
}
function closeClassAssignModal(){ document.getElementById('editClassAssignModal').classList.add('hidden'); }

document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid=this.value;
    fetch('{{ route('ajax.getClassByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})}).then(r=>r.text()).then(html=>{document.getElementById('class_id').innerHTML=html;});
    fetch('{{ route('ajax.getDataByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({table:'subject', branch_id: bid})}).then(r=>r.text()).then(html=>{
        // update subject_holder with new options but keep as simple list
        const parser = new DOMParser(); const doc = parser.parseFromString(html, 'text/html');
        const options = doc.querySelectorAll('option');
        const sel = document.getElementById('subject_holder');
        sel.innerHTML=''; options.forEach(o=>{ if(o.value) sel.appendChild(new Option(o.textContent, o.value)); });
    });
});
document.getElementById('class_id')?.addEventListener('change', function(){
    const cid=this.value;
    fetch('{{ route('ajax.getSectionByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({class_id: cid})}).then(r=>r.text()).then(html=>{document.getElementById('section_id').innerHTML=html;});
});
</script>
@endpush
@endsection
