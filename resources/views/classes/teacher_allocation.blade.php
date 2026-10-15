@extends('layouts.app')

@section('title', translate('assign_class_teacher'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('assign_class_teacher') }}</span>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">
        @if(get_permission('assign_class_teacher', 'is_add'))
        <div class="lg:col-span-2">
            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                    <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-plus-circle class="size-4" /></span>
                    <h3 class="text-sm font-bold text-slate-800">{{ translate('class_teacher_allocation') }}</h3>
                </div>
                <form method="POST" action="{{ route('classes.teacher_allocation_save') }}" class="p-5 space-y-4">
                    @csrf
                    @if(is_superadmin_loggedin())
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                        <select name="branch_id" id="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                            <option value="">{{ translate('select') }}</option>
                            @foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$branch_id)>{{ $b->name }}</option>@endforeach
                        </select>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class') }} <span class="text-red-500">*</span></label>
                        <select name="class_id" id="class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                            <option value="">{{ translate('select') }}</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                        @error('class_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    @if($useSections)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }} <span class="text-red-500">*</span></label>
                        <select name="section_id" id="section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                            <option value="">{{ translate('select_class_first') }}</option>
                        </select>
                        @error('section_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class_teacher') }} <span class="text-red-500">*</span></label>
                        <select name="staff_id" id="staff_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                            <option value="">{{ translate('select') }}</option>
                            @foreach($teachers as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach
                        </select>
                        @error('staff_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                        <x-heroicon-o-check-circle class="size-4" /> {{ translate('save') }}
                    </button>
                </form>
            </div>
        </div>
        @endif

        <div class="{{ get_permission('assign_class_teacher', 'is_add') ? 'lg:col-span-3' : 'lg:col-span-5' }}">
            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                    <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-list-bullet class="size-4" /></span>
                    <h3 class="text-sm font-bold text-slate-800">{{ translate('class_teacher') }} {{ translate('list') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">{{ translate('branch') }}</th>
                                <th class="px-4 py-3">{{ translate('class_teacher') }}</th>
                                <th class="px-4 py-3">{{ translate('class') }}</th>
                                <th class="px-4 py-3">{{ translate('section') }}</th>
                                <th class="px-4 py-3 text-right">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($allocations as $i => $a)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ get_type_name_by_id('branch', $a->branch_id) }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $a->teacher_name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $a->class_name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $a->section_name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(get_permission('assign_class_teacher', 'is_edit'))
                                        <button type="button" onclick="getAllocationTeacher({{ $a->id }})" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#2563eb] transition"><x-heroicon-o-pencil-square class="size-4" /></button>
                                        @endif
                                        @if(get_permission('assign_class_teacher', 'is_delete'))
                                        <form method="POST" action="{{ route('classes.teacher_allocation_delete', $a->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
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
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative mx-auto mt-16 w-full max-w-lg rounded-2xl bg-white border border-slate-200 shadow-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2"><x-heroicon-o-pencil-square class="size-4 text-[#2563eb]" /> {{ translate('edit_assign') }}</h4>
            <button type="button" onclick="closeModal()" class="grid size-8 place-items-center rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100"><x-heroicon-o-x-mark class="size-4" /></button>
        </div>
        <div id="allocation"></div>
    </div>
</div>

@push('scripts')
<script>
function getAllocationTeacher(id){
    fetch('{{ route('classes.getAllocationTeacher') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({id:id})})
        .then(r=>r.text()).then(html=>{ document.getElementById('allocation').innerHTML=html; document.getElementById('editModal').classList.remove('hidden'); });
}
function closeModal(){ document.getElementById('editModal').classList.add('hidden'); }

document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid=this.value;
    fetch('{{ route('ajax.getClassByBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid})}).then(r=>r.text()).then(html=>{document.getElementById('class_id').innerHTML=html;});
    fetch('{{ route('ajax.getStaffListRole') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({branch_id: bid, role_id: 3})}).then(r=>r.text()).then(html=>{document.getElementById('staff_id').innerHTML=html;});
});
document.getElementById('class_id')?.addEventListener('change', function(){
    const cid=this.value;
    fetch('{{ route('ajax.getSectionByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({class_id: cid})}).then(r=>r.text()).then(html=>{const el=document.getElementById('section_id'); if(el) el.innerHTML=html;});
});
</script>
@endpush
@endsection
