@extends('layouts.app')
@section('title', translate('student_list'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('student_list') }}</span>
    </div>
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-800">{{ translate('student_list') }}</h2>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('student.search') }}" class="flex gap-2">
                @csrf
                <input type="text" name="search_text" placeholder="{{ translate('search') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                <button type="submit" class="rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-medium text-white">{{ translate('search') }}</button>
            </form>
            @if(get_permission('student','is_add'))
            <a href="{{ route('student.create') }}" class="rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-medium text-white">{{ translate('create_admission') }}</a>
            @endif
        </div>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-3 py-3"><input type="checkbox" id="selectAllchkbox" class="rounded"></th>
                        <th class="px-3 py-3">{{ translate('photo') }}</th>
                        <th class="px-3 py-3">{{ translate('roll') }}</th>
                        <th class="px-3 py-3">{{ translate('name') }}</th>
                        <th class="px-3 py-3">{{ translate('register_no') }}</th>
                        <th class="px-3 py-3">{{ translate('class') }}</th>
                        <th class="px-3 py-3">{{ translate('section') }}</th>
                        <th class="px-3 py-3">{{ translate('guardian_name') }}</th>
                        <th class="px-3 py-3 text-right">{{ translate('action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $row)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-3 py-2"><input type="checkbox" class="cb_bulkdelete rounded" value="{{ $row['student_id'] }}" id="{{ $row['student_id'] }}"></td>
                        <td class="px-3 py-2"><img src="{{ get_image_url('student', $row['photo']) }}" class="size-9 rounded-full object-cover border border-slate-200"></td>
                        <td class="px-3 py-2">{{ $row['roll'] }}</td>
                        <td class="px-3 py-2 font-medium {{ $row['active']==0 ? 'text-red-600' : 'text-slate-800' }}">{{ $row['fullname'] }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $row['register_no'] }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $row['class_name'] }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $row['section_name'] }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $row['parent_id'] ? get_type_name_by_id('parent',$row['parent_id']) : 'N/A' }}</td>
                        <td class="px-3 py-2">
                            <div class="flex justify-end gap-1.5">
                                <button onclick="quickView({{ $row['enroll_id'] }})" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-[#2563eb]"><x-heroicon-o-eye class="size-4"/></button>
                                @if(get_permission('student','is_edit'))
                                <a href="{{ route('student.profile', $row['student_id']) }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-[#2563eb]"><x-heroicon-o-pencil-square class="size-4"/></a>
                                @endif
                                @if(get_permission('student','is_delete'))
                                <form method="POST" action="{{ route('student.delete', $row['student_id']) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-red-600 hover:border-red-200"><x-heroicon-o-trash class="size-4"/></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(count($students)>0 && get_permission('student','is_delete'))
        <div class="p-4 border-t border-slate-200 flex justify-end">
            <button id="student_bulk_delete" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">{{ translate('bulk_delete') }}</button>
        </div>
        @endif
    </div>
</div>

{{-- Quick view modal --}}
<div id="quickViewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('quickViewModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl border border-slate-200 w-full max-w-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">{{ translate('quick_view') }}</h3>
            <button onclick="document.getElementById('quickViewModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><x-heroicon-o-x-mark class="size-5"/></button>
        </div>
        <div class="p-5 space-y-4">
            <div class="flex flex-col items-center">
                <img id="quick_image" src="{{ asset('uploads/app_image/defualt.png') }}" class="size-24 rounded-full border border-slate-200 object-cover">
                <h4 id="quick_full_name" class="mt-3 font-semibold text-slate-800"></h4>
                <p id="quick_category" class="text-xs text-slate-500"></p>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-slate-500">{{ translate('register_no') }}:</span> <span id="quick_register_no" class="font-medium"></span></div>
                <div><span class="text-slate-500">{{ translate('roll') }}:</span> <span id="quick_roll" class="font-medium"></span></div>
                <div><span class="text-slate-500">{{ translate('admission_date') }}:</span> <span id="quick_admission_date" class="font-medium"></span></div>
                <div><span class="text-slate-500">{{ translate('birthday') }}:</span> <span id="quick_date_of_birth" class="font-medium"></span></div>
                <div><span class="text-slate-500">{{ translate('email') }}:</span> <span id="quick_email" class="font-medium"></span></div>
                <div><span class="text-slate-500">{{ translate('mobile_no') }}:</span> <span id="quick_mobile_no" class="font-medium"></span></div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
function quickView(id){
    fetch('{{ route('student.quickDetails') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({student_id:id})})
    .then(r=>r.json()).then(d=>{
        document.getElementById('quick_image').src=d.photo;
        document.getElementById('quick_full_name').textContent=d.full_name;
        document.getElementById('quick_category').textContent=d.student_category;
        document.getElementById('quick_register_no').textContent=d.register_no;
        document.getElementById('quick_roll').textContent=d.roll;
        document.getElementById('quick_admission_date').textContent=d.admission_date;
        document.getElementById('quick_date_of_birth').textContent=d.birthday;
        document.getElementById('quick_email').textContent=d.email;
        document.getElementById('quick_mobile_no').textContent=d.mobileno;
        document.getElementById('quickViewModal').classList.remove('hidden');
    });
}
document.getElementById('selectAllchkbox')?.addEventListener('change', function(){
    document.querySelectorAll('.cb_bulkdelete').forEach(cb=>cb.checked=this.checked);
});
document.getElementById('student_bulk_delete')?.addEventListener('click', function(){
    const ids=[...document.querySelectorAll('.cb_bulkdelete:checked')].map(cb=>cb.value);
    if(ids.length==0) return alert('Please select at least one');
    if(!confirm('{{ translate('are_you_sure') }}')) return;
    fetch('{{ route('student.bulk_delete') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({array_id:ids})})
    .then(r=>r.json()).then(d=>{ alert(d.message); location.reload(); });
});
</script>
@endpush
@endsection
