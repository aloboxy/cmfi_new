@extends('layouts.app')
@section('title', translate('homework'))
@section('content')
<div class="space-y-5">
 <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border"><x-heroicon-o-home class="size-4"/></a><span>/</span><a href="{{ route('homework.index') }}" class="hover:text-[#2563eb]">{{ translate('homework') }}</a><span>/</span><span class="font-semibold text-slate-800">{{ translate('evaluation') }}</span></div>
 <div class="rounded-2xl bg-white border overflow-hidden"><div class="px-5 py-4 border-b font-bold text-sm">{{ translate('evaluation') }}</div>
  <form method="POST" action="{{ route('homework.evaluate_save') }}">@csrf<input type="hidden" name="homework_id" value="{{ $homework_id ?? $homeworklist[0]->id ?? '' }}">
   <div class="p-4"><label class="block text-sm font-medium mb-1">{{ translate('date') }} <span class="text-red-500">*</span></label><input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-64 rounded-xl border px-3.5 py-2.5 text-sm" required></div>
   <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('student') }}</th><th class="px-4 py-3">{{ translate('roll') }}</th><th class="px-4 py-3">{{ translate('status') }}</th><th class="px-4 py-3">{{ translate('rank') }}</th><th class="px-4 py-3">{{ translate('remark') }}</th></tr></thead><tbody class="divide-y">
    @foreach($homeworklist as $i=>$row)<tr class="hover:bg-slate-50"><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $row->first_name ?? $row->fullname }}<input type="hidden" name="evaluate[{{ $i }}][student_id]" value="{{ $row->student_id }}"><input type="hidden" name="evaluate[{{ $i }}][evaluation_id]" value="{{ $row->ev_id ?? '' }}"></td><td class="px-4 py-3">{{ $row->roll }}</td><td class="px-4 py-3"><select name="evaluate[{{ $i }}][status]" class="rounded-lg border px-2 py-1.5 text-sm"><option value="">Select</option><option value="c" @selected(($row->ev_status ?? '')=='c')>Completed</option><option value="nc" @selected(($row->ev_status ?? '')=='nc')>Not Completed</option></select></td><td class="px-4 py-3"><input type="number" name="evaluate[{{ $i }}][rank]" value="{{ $row->rank ?? '' }}" class="w-20 rounded-lg border px-2 py-1.5 text-sm"></td><td class="px-4 py-3"><input type="text" name="evaluate[{{ $i }}][remark]" value="{{ $row->ev_remarks ?? '' }}" class="w-full rounded-lg border px-2 py-1.5 text-sm"></td></tr>@endforeach
   </tbody></table></div>
   <div class="p-4 flex justify-end"><button class="rounded-xl bg-emerald-600 text-white px-6 py-2.5 text-sm font-semibold">{{ translate('save') }}</button></div>
  </form></div>
</div>
@endsection
