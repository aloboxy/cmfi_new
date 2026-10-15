@extends('layouts.app')
@section('title', translate('due_fees_report'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('due_fees_report') }}</h2>
 <form method="POST" action="{{ route('fees.due_report') }}" class="rounded-2xl bg-white border p-5 flex gap-4">@csrf
  <select name="class_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">{{ translate('select') }}</option></select>
  <select name="section_id" class="rounded-xl border px-3 py-2 text-sm"><option value="all">All</option></select>
  <button class="rounded-xl bg-slate-800 text-white px-6 py-2 text-sm">Search</button>
 </form>
 @if($invoicelist)<div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3">Roll</th><th class="px-4 py-3 text-left">Student</th><th class="px-4 py-3">Total Fees</th><th class="px-4 py-3">Paid</th><th class="px-4 py-3">Due</th></tr></thead><tbody class="divide-y">@foreach($invoicelist as $r)<tr><td class="px-4 py-3 text-center">{{ $r->roll }}</td><td class="px-4 py-3">{{ $r->first_name }} {{ $r->last_name }}</td><td class="px-4 py-3 text-center">{{ $r->total_fees }}</td><td class="px-4 py-3 text-center">{{ $r->payment['total_paid'] ?? 0 }}</td><td class="px-4 py-3 text-center font-semibold">{{ $r->total_fees - ($r->payment['total_paid'] ?? 0) - ($r->payment['total_discount'] ?? 0) }}</td></tr>@endforeach</tbody></table></div>@endif
</div>
@endsection
