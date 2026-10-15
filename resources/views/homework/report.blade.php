@extends('layouts.app')
@section('title', translate('homework'))
@section('content')
<div class="space-y-5">
 <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border"><x-heroicon-o-home class="size-4"/></a><span>/</span><span class="font-semibold text-slate-800">{{ translate('evaluation_report') }}</span></div>
 <div class="rounded-2xl bg-white border p-5">
  <form method="POST" action="{{ route('homework.report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">@csrf
   @if(is_superadmin_loggedin())<div><label class="block text-sm font-medium mb-1">{{ translate('branch') }}</label><select name="branch_id" id="rep_branch" class="w-full rounded-xl border px-3 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==($branch_id??''))>{{ $b->name }}</option>@endforeach</select></div>@endif
   <div><label class="block text-sm font-medium mb-1">{{ translate('class') }}</label><select name="class_id" id="rep_class" class="w-full rounded-xl border px-3 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
   <div><label class="block text-sm font-medium mb-1">{{ translate('section') }}</label><select name="section_id" id="rep_section" class="w-full rounded-xl border px-3 py-2.5 text-sm"><option value="">{{ translate('select') }}</option></select></div>
   <div><label class="block text-sm font-medium mb-1">{{ translate('subject') }}</label><select name="subject_id" id="rep_subject" class="w-full rounded-xl border px-3 py-2.5 text-sm"><option value="">{{ translate('select') }}</option></select></div>
   <div class="md:col-span-4 flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] text-white px-6 py-2.5 text-sm font-semibold">{{ translate('search') }}</button></div>
  </form>
 </div>
 @if(isset($homeworklist))
 <div class="rounded-2xl bg-white border overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('subject') }}</th><th class="px-4 py-3">{{ translate('date') }}</th><th class="px-4 py-3">{{ translate('status') }}</th><th class="px-4 py-3">{{ translate('action') }}</th></tr></thead><tbody class="divide-y">
  @forelse($homeworklist as $i=>$row)<tr class="hover:bg-slate-50"><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3">{{ $row->subject_name }}</td><td class="px-4 py-3">{{ _d($row->date_of_homework) }}</td><td class="px-4 py-3">{{ $row->status==1?'Scheduled':'Published' }}</td><td class="px-4 py-3"><a href="{{ route('homework.evaluate',$row->id) }}" class="rounded-lg bg-[#2563eb] text-white px-3 py-1.5 text-xs">{{ translate('view') }}</a></td></tr>@empty<tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse
 </tbody></table></div></div>
 @endif
</div>
@push('scripts')<script>
document.getElementById('rep_class')?.addEventListener('change', function(){ const cid=this.value; fetch('{{ route('ajax.getSectionByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({class_id: cid})}).then(r=>r.text()).then(html=>{ document.getElementById('rep_section').innerHTML=html; }); fetch('{{ route('ajax.getSubjectByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({class_id: cid})}).then(r=>r.text()).then(html=>{ document.getElementById('rep_subject').innerHTML=html; }); });
</script>@endpush
@endsection
