@extends('layouts.app')
@section('title', translate('id_card_generate'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('id_card_generate') }}</span>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('id_card_generate') }}</div>
        <div class="p-5">
            <form method="POST" action="{{ route('student.idcard') }}" class="space-y-4">
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
                <button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('search') }}</button>
            </form>
        </div>
    </div>
    @if(isset($students))
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('student_list') }} ({{ count($students) }})</div>
        <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($students as $s)
            <div class="rounded-xl border border-slate-200 p-4 flex flex-col items-center text-center">
                <img src="{{ get_image_url('student',$s->photo) }}" class="size-16 rounded-full border border-slate-200 object-cover">
                <h4 class="mt-2 font-bold text-slate-800">{{ $s->fullname }}</h4>
                <p class="text-xs text-slate-500">{{ translate('register_no') }}: {{ $s->register_no }}</p>
                <p class="text-xs text-slate-500">{{ $s->class_name }} - {{ $s->section_name }}</p>
                <div class="mt-2 text-[10px] text-slate-400">QR: {{ $s->qr_code }}</div>
            </div>
            @empty
            <p class="col-span-3 text-center text-slate-500 py-8">{{ translate('no_information_available') }}</p>
            @endforelse
        </div>
    </div>
    @endif
</div>
@push('scripts')
<script>
document.getElementById('branch_id')?.addEventListener('change', function(){
    fetch('{{ route('ajax.getClassByBranch') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({branch_id:this.value})})
    .then(r=>r.text()).then(html=>document.getElementById('class_id').innerHTML=html);
});
document.getElementById('class_id')?.addEventListener('change', function(){
    fetch('{{ route('ajax.getSectionByClass') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({class_id:this.value,all:true})})
    .then(r=>r.text()).then(html=>document.getElementById('section_id').innerHTML=html);
});
</script>
@endpush
@endsection
