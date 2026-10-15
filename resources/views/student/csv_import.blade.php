@extends('layouts.app')
@section('title', translate('multiple_import'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('multiple_import') }}</span>
    </div>
    @if(session('csvimport'))
    <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{!! session('csvimport') !!}</div>
    @endif
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2"><span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-arrow-up-tray class="size-4"/></span> {{ translate('multiple_import') }}</h3>
            <a href="{{ route('student.csv_sample') }}" class="rounded-xl bg-white border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50">{{ translate('download_sample') ?? 'Download Sample' }}</a>
        </div>
        <form method="POST" action="{{ route('student.csv_import') }}" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf
            @if(is_superadmin_loggedin())
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                <select name="branch_id" id="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                    <option value="">{{ translate('select') }}</option>
                    @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                </select>
                @error('branch_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            @endif
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class') }} <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                    @error('class_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }} <span class="text-red-500">*</span></label>
                    <select name="section_id" id="section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option></select>
                    @error('section_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">CSV File <span class="text-red-500">*</span></label>
                <input type="file" name="userfile" accept=".csv" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                @error('userfile')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end">
                <button type="submit" name="save" value="1" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700">{{ translate('import') }}</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid=this.value;
    fetch('{{ route('ajax.getClassByBranch') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({branch_id:bid})})
    .then(r=>r.text()).then(html=>{document.getElementById('class_id').innerHTML=html});
});
document.getElementById('class_id')?.addEventListener('change', function(){
    const cid=this.value;
    fetch('{{ route('ajax.getSectionByClass') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({class_id:cid})})
    .then(r=>r.text()).then(html=>{document.getElementById('section_id').innerHTML=html});
});
</script>
@endpush
@endsection
