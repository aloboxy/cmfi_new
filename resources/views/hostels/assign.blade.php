@extends('layouts.app')
@section('title', translate('allocation_list'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('allocation_list') }}</h2>
 <form method="POST" action="{{ route('hostels.assign') }}" class="rounded-2xl bg-white border p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
  @csrf
  <select name="class_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">Select Class</option></select>
  <select name="section_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">Select Section</option></select>
  <button class="rounded-xl bg-slate-800 text-white px-4 py-2 text-sm">Search</button>
 </form>
 @if($allocationlist)<div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3">Enroll</th><th class="px-4 py-3 text-left">Student</th><th class="px-4 py-3">Hostel</th><th class="px-4 py-3">Room</th><th class="px-4 py-3">Fee</th></tr></thead><tbody class="divide-y">@foreach($allocationlist as $a)<tr><td class="px-4 py-3 text-center">{{ $a->enroll_id }}</td><td class="px-4 py-3">{{ $a->first_name }} {{ $a->last_name }} ({{ $a->register_no }})</td><td class="px-4 py-3 text-center">{{ $a->hostel_name }}</td><td class="px-4 py-3 text-center">{{ $a->room_name }}</td><td class="px-4 py-3 text-center">{{ $a->bed_fee }}</td></tr>@endforeach</tbody></table></div>@endif
</div>
@endsection
