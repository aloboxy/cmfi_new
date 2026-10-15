@extends('layouts.app')
@section('title', translate('income_report'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('income_report') }}</h2>
 <form method="POST" action="{{ route('accounting.income_report') }}" class="rounded-2xl bg-white border p-5 flex gap-3">@csrf<input type="text" name="daterange" placeholder="YYYY-MM-DD - YYYY-MM-DD" class="rounded-xl border px-3 py-2 text-sm flex-1" required><button class="rounded-xl bg-emerald-600 text-white px-6 py-2 text-sm">Search</button></form>
 @if($results)<div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-3 py-2">Date</th><th class="px-3 py-2 text-left">Account</th><th class="px-3 py-2 text-left">Voucher</th><th class="px-3 py-2">Amount</th></tr></thead><tbody class="divide-y">@foreach($results as $r)<tr><td class="px-3 py-2 text-center">{{ $r->date }}</td><td class="px-3 py-2">{{ $r->ac_name }}</td><td class="px-3 py-2">{{ $r->v_head }}</td><td class="px-3 py-2 text-center">{{ $r->amount }}</td></tr>@endforeach</tbody></table></div>@endif
</div>
@endsection
