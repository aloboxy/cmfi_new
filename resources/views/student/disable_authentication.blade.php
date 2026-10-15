@extends('layouts.app')
@section('title', translate('deactivate_account'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('deactivate_account') }}</span>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-800">{{ translate('deactivate_account') }}</h3>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('student.disable_authentication') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @if(is_superadmin_loggedin())
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }}</label>
                        <select name="branch_id" id="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                            <option value="">{{ translate('select') }}</option>
                            @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                        </select>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class') }}</label>
                        <select name="class_id" id="class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                            <option value="">{{ translate('select') }}</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }}</label>
                        <select name="section_id" id="section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option></select>
                    </div>
                </div>
                <button type="submit" name="search" value="1" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('search') }}</button>
            </form>
            @if(isset($students))
            <form method="POST" action="{{ route('student.disable_authentication') }}" class="mt-6 space-y-4">
                @csrf
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-4 py-3"><input type="checkbox" id="selectAll"></th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('register_no') }}</th><th class="px-4 py-3">{{ translate('class') }}</th><th class="px-4 py-3">{{ translate('section') }}</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $s)
                            <tr><td class="px-4 py-2"><input type="checkbox" name="views_bulk_operations[]" value="{{ $s->student_id }}" class="rounded"></td><td class="px-4 py-2 font-medium">{{ $s->fullname }}</td><td class="px-4 py-2">{{ $s->register_no }}</td><td class="px-4 py-2">{{ $s->class_name }}</td><td class="px-4 py-2">{{ $s->section_name }}</td></tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(count($students)>0)
                <button type="submit" name="auth" value="1" class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">{{ translate('activate') }}</button>
                @endif
            </form>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('branch_id')?.addEventListener('change', function(){
    fetch('{{ route('ajax.getClassByBranch') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({branch_id:this.value})})
    .then(r=>r.text()).then(html=>document.getElementById('class_id').innerHTML=html);
});
document.getElementById('class_id')?.addEventListener('change', function(){
    fetch('{{ route('ajax.getSectionByClass') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({class_id:this.value})})
    .then(r=>r.text()).then(html=>document.getElementById('section_id').innerHTML=html);
});
document.getElementById('selectAll')?.addEventListener('change', function(){document.querySelectorAll('input[name="views_bulk_operations[]"]').forEach(cb=>cb.checked=this.checked);});
</script>
@endpush
@endsection
