@extends('layouts.app')
@section('title', translate('grades_range'))
@section('content')
<div class="space-y-5">
 <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border"><x-heroicon-o-home class="size-4"/></a><span>/</span><a href="{{ route('exam.grade') }}" class="hover:text-[#2563eb]">{{ translate('grades_range') }}</a><span>/</span><span class="font-semibold text-slate-800">{{ translate('edit') }}</span></div>
 <div class="rounded-2xl bg-white border p-5 max-w-2xl"><h3 class="font-bold mb-4">{{ translate('edit').' '.translate('grade') }}</h3>
 <form method="POST" action="{{ route('exam.grade_update',$grade->id) }}">@csrf @method('PUT')
  @if(is_superadmin_loggedin())<div class="mb-3"><label class="block text-sm font-medium mb-1">{{ translate('branch') }}</label><select name="branch_id" class="w-full rounded-xl border px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$grade->branch_id)>{{ $b->name }}</option>@endforeach</select></div>@endif
  <div class="grid grid-cols-2 gap-3 mb-3"><div><label class="block text-sm font-medium mb-1">{{ translate('name') }}</label><input type="text" name="name" value="{{ $grade->name }}" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required></div><div><label class="block text-sm font-medium mb-1">{{ translate('grade_point') }}</label><input type="text" name="grade_point" value="{{ $grade->grade_point }}" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required></div></div>
  <div class="grid grid-cols-2 gap-3 mb-3"><div><label class="block text-sm font-medium mb-1">{{ translate('mark_from') }}</label><input type="number" name="lower_mark" value="{{ $grade->lower_mark }}" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required></div><div><label class="block text-sm font-medium mb-1">{{ translate('mark_upto') }}</label><input type="number" name="upper_mark" value="{{ $grade->upper_mark }}" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required></div></div>
  <div class="mb-3"><label class="block text-sm font-medium mb-1">{{ translate('remark') }}</label><textarea name="remark" rows="2" class="w-full rounded-xl border px-3.5 py-2.5 text-sm">{{ $grade->remark }}</textarea></div>
  <div class="flex justify-end gap-2"><a href="{{ route('exam.grade') }}" class="px-4 py-2 rounded-xl border text-sm">Cancel</a><button class="px-4 py-2 rounded-xl bg-[#2563eb] text-white text-sm">{{ translate('update') }}</button></div>
 </form></div>
</div>
@endsection
