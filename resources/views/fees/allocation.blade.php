@extends('layouts.app')
@section('title', translate('fees_allocation'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('fees_allocation') }}</h2>
 <form method="POST" action="{{ route('fees.allocation') }}" class="rounded-2xl bg-white border p-5">
  @csrf
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
   <select name="class_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">{{ translate('select') }} Class</option></select>
   <select name="section_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="all">All Section</option></select>
   <select name="fee_group_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">{{ translate('select') }}</option>@foreach($feeGroups as $g)<option value="{{ $g->id }}" {{ ($fee_group_id??'')==$g->id?'selected':'' }}>{{ $g->name }}</option>@endforeach</select>
   <button type="submit" name="search" value="1" class="rounded-xl bg-slate-800 text-white px-4 py-2 text-sm">Search</button>
  </div>
 </form>
 @if(isset($studentlist))
 <form method="POST" action="{{ route('fees.allocation') }}" class="rounded-2xl bg-white border overflow-hidden">
  @csrf
  <input type="hidden" name="fee_group_id" value="{{ $fee_group_id }}">
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-3 py-2"><input type="checkbox" id="checkAll" onclick="document.querySelectorAll('.chk').forEach(c=>c.checked=this.checked)"></th><th class="px-3 py-2 text-left">Roll</th><th class="px-3 py-2 text-left">Student</th><th class="px-3 py-2 text-left">Register No</th></tr></thead><tbody class="divide-y">
   @foreach($studentlist as $s)<tr><td class="px-3 py-2"><input type="checkbox" name="stu_operations[]" value="{{ $s->student_id }}" class="chk" {{ $s->group_id?'checked':'' }}></td><td class="px-3 py-2">{{ $s->roll }}</td><td class="px-3 py-2">{{ $s->fullname }}</td><td class="px-3 py-2">{{ $s->register_no }}</td></tr>@endforeach
  </tbody></table></div>
  <div class="p-4 text-right"><button type="submit" name="save" value="1" class="rounded-xl bg-[#2563eb] text-white px-6 py-2 text-sm font-semibold">Save Allocation</button></div>
 </form>
 @endif
</div>
@endsection
