@extends('layouts.app')
@section('title', translate('employee_attendance'))
@section('content')
<div class="space-y-5">
 <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border"><x-heroicon-o-home class="size-4"/></a><span>/</span><span class="font-semibold text-slate-800">{{ translate('employee_attendance') }}</span></div>
 <div class="rounded-2xl bg-white border p-5">
  <form method="POST" action="{{ route('attendance.staff') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">@csrf
   @if(is_superadmin_loggedin())<div><label class="block text-sm font-medium mb-1">{{ translate('branch') }}</label><select name="branch_id" class="w-full rounded-xl border px-3 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$branch_id)>{{ $b->name }}</option>@endforeach</select></div>@endif
   <div><label class="block text-sm font-medium mb-1">{{ translate('role') }}</label><select name="staff_role" class="w-full rounded-xl border px-3 py-2.5 text-sm" required><option value="">{{ translate('select') }}</option>@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
   <div><label class="block text-sm font-medium mb-1">{{ translate('date') }}</label><input type="date" name="date" value="{{ $date ?? date('Y-m-d') }}" class="w-full rounded-xl border px-3 py-2.5 text-sm" required max="{{ date('Y-m-d') }}"></div>
   <div class="flex items-end"><button type="submit" name="search" value="1" class="w-full rounded-xl bg-[#2563eb] text-white py-2.5 text-sm font-semibold">{{ translate('search') }}</button></div>
  </form>
 </div>
 @if(isset($attendencelist))
 <div class="rounded-2xl bg-white border overflow-hidden">
  <form method="POST" action="{{ route('attendance.staff') }}">@csrf<input type="hidden" name="date" value="{{ $date }}"><input type="hidden" name="save" value="1">
   <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('staff_id') }}</th><th class="px-4 py-3">{{ translate('status') }}</th><th class="px-4 py-3">{{ translate('remark') }}</th></tr></thead><tbody class="divide-y">
    @foreach($attendencelist as $i=>$row)<tr class="hover:bg-slate-50"><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $row->name }}<input type="hidden" name="attendance[{{ $i }}][staff_id]" value="{{ $row->id }}"><input type="hidden" name="attendance[{{ $i }}][attendance_id]" value="{{ $row->atten_id ?? '' }}"></td><td class="px-4 py-3">{{ $row->staff_id }}</td><td class="px-4 py-3"><select name="attendance[{{ $i }}][status]" class="rounded-lg border px-2 py-1.5 text-sm"><option value="P" @selected(($row->att_status ?? '')=='P')>P - Present</option><option value="A" @selected(($row->att_status ?? '')=='A')>A - Absent</option><option value="L" @selected(($row->att_status ?? '')=='L')>L - Late</option><option value="H" @selected(($row->att_status ?? '')=='H')>H - Holiday</option></select></td><td class="px-4 py-3"><input type="text" name="attendance[{{ $i }}][remark]" value="{{ $row->att_remark ?? '' }}" class="w-full rounded-lg border px-2 py-1.5 text-sm"></td></tr>@endforeach
   </tbody></table></div>
   <div class="p-4 flex justify-end"><button class="rounded-xl bg-emerald-600 text-white px-6 py-2.5 text-sm font-semibold">{{ translate('save') }}</button></div>
  </form></div>
 @endif
</div>
@endsection
