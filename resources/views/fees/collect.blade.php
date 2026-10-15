@extends('layouts.app')
@section('title', translate('collect_fees'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('collect_fees') }}</h2>
 <form method="POST" action="{{ route('fees.collect') }}" class="rounded-2xl bg-white border p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
  @csrf
  <select name="class_id" class="rounded-xl border px-3 py-2 text-sm"><option value="">{{ translate('select') }} Class</option></select>
  <select name="section_id" class="rounded-xl border px-3 py-2 text-sm"><option value="all">All</option></select>
  <button type="submit" name="search" value="1" class="rounded-xl bg-slate-800 text-white px-4 py-2 text-sm">Search</button>
 </form>
 @if($invoicelist)
 <div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3">Roll</th><th class="px-4 py-3 text-left">Student</th><th class="px-4 py-3">Class</th><th class="px-4 py-3">Groups</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">
  @foreach($invoicelist as $r)<tr><td class="px-4 py-3 text-center">{{ $r->roll }}</td><td class="px-4 py-3">{{ $r->first_name }} {{ $r->last_name }} ({{ $r->register_no }})</td><td class="px-4 py-3">{{ $r->class_name }} ({{ $r->section_name }})</td><td class="px-4 py-3">@foreach($r->feegroup as $g)<span class="inline-block bg-slate-100 rounded px-2 py-0.5 text-xs mr-1">{{ $g->group_name }}</span>@endforeach</td><td class="px-4 py-3 text-right"><a href="{{ route('fees.invoice',$r->student_id) }}" class="px-3 py-1 bg-[#2563eb] text-white rounded-lg text-xs">Collect</a></td></tr>@endforeach
 </tbody></table></div>
 @endif
</div>
@endsection
