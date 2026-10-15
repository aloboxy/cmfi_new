@extends('layouts.app')

@section('title', translate('teacher_assign'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('subject.index') }}" class="hover:text-[#2563eb]">{{ translate('subject') }}</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('teacher_assign') }}</span>
    </div>

    <div x-data="{ tab: 'list' }" class="space-y-4">
        <div class="flex gap-2">
            <button @click="tab='list'" :class="tab==='list' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="rounded-xl px-4 py-2 text-sm font-medium flex items-center gap-2"><x-heroicon-o-list-bullet class="size-4" /> {{ translate('teacher_assign_list') }}</button>
            @if(get_permission('subject_teacher_assign', 'is_add'))
            <button @click="tab='create'" :class="tab==='create' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="rounded-xl px-4 py-2 text-sm font-medium flex items-center gap-2"><x-heroicon-o-plus-circle class="size-4" /> {{ translate('assign') }}</button>
            @endif
        </div>

        <div x-show="tab==='list'" class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-user-group class="size-4" /></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('teacher_assign_list') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3">#</th>
                            @if(is_superadmin_loggedin())<th class="px-4 py-3">{{ translate('branch') }}</th>@endif
                            <th class="px-4 py-3">{{ translate('teacher') }}</th>
                            <th class="px-4 py-3">{{ translate('department') }}</th>
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
                            @if(is_superadmin_loggedin())<td class="px-4 py-3 text-slate-600">{{ get_type_name_by_id('branch', $row->branch_id) }}</td>@endif
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $row->teacher_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->department_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->class_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->section_name ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->subject_name }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    @if(get_permission('subject_teacher_assign', 'is_delete'))
                                    <form method="POST" action="{{ route('subject.teacher_assign_delete', $row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-600 transition"><x-heroicon-o-trash class="size-4" /></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(get_permission('subject_teacher_assign', 'is_add'))
        <div x-show="tab==='create'" x-cloak class="rounded-2xl bg-white border border-slate-200 overflow-hidden max-w-3xl">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-plus-circle class="size-4" /></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('assign') }} {{ translate('teacher') }}</h3>
            </div>
            <form method="POST" action="{{ route('subject.teacher_assign') }}" class="p-5 space-y-4">
                @csrf
                @if(is_superadmin_loggedin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                    <select name="branch_id" id="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('teacher') }} <span class="text-red-500">*</span></label>
                    <select name="staff_id" id="staff_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($teachers as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach
                    </select>
                </div>
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
                    <select name="subject_id" id="subject_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                        <option value="">{{ translate('select') }}</option>
                    </select>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition"><x-heroicon-o-check-circle class="size-4" /> {{ translate('save') }}</button>
            </form>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid=this.value;
    fetch('{{ route('ajax.getClassByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})}).then(r=>r.text()).then(html=>{document.getElementById('class_id').innerHTML=html; document.getElementById('subject_id').innerHTML='<option value="">{{ translate('select') }}</option>';});
    fetch('{{ route('ajax.getStaffListRole') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid, role_id: 3})}).then(r=>r.text()).then(html=>{document.getElementById('staff_id').innerHTML=html;});
});
document.getElementById('class_id')?.addEventListener('change', function(){
    const cid=this.value;
    fetch('{{ route('ajax.getSectionByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({class_id: cid})}).then(r=>r.text()).then(html=>{document.getElementById('section_id').innerHTML=html; document.getElementById('subject_id').innerHTML='<option value="">{{ translate('select') }}</option>';});
});
document.getElementById('section_id')?.addEventListener('change', function(){
    const sid=this.value; const cid=document.getElementById('class_id').value;
    fetch('{{ route('subject.getByClassSection') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({classID: cid, sectionID: sid})}).then(r=>r.text()).then(html=>{document.getElementById('subject_id').innerHTML=html;});
});
</script>
@endpush
@endsection
