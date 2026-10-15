@extends('layouts.app')
@section('title', translate('account_statement'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('account_statement') }}</h2>
 <form method="POST" action="{{ route('accounting.statement') }}" class="rounded-2xl bg-white border p-5 grid grid-cols-1 md:grid-cols-4 gap-4">
  @csrf
  <select name="account_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">Select Account</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select>
  <select name="type" class="rounded-xl border px-3 py-2 text-sm"><option value="all">All</option><option value="deposit">Deposit</option><option value="expense">Expense</option></select>
  <input type="text" name="daterange" placeholder="YYYY-MM-DD - YYYY-MM-DD" value="{{ isset($daterange)? implode(' - ',$daterange):'' }}" class="rounded-xl border px-3 py-2 text-sm" required>
  <button class="rounded-xl bg-slate-800 text-white px-4 py-2 text-sm">Search</button>
 </form>
 @if($results)<div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-3 py-2">Date</th><th class="px-3 py-2 text-left">Voucher</th><th class="px-3 py-2">DR</th><th class="px-3 py-2">CR</th><th class="px-3 py-2">Balance</th></tr></thead><tbody class="divide-y">@foreach($results as $r)<tr><td class="px-3 py-2 text-center">{{ $r->date }}</td><td class="px-3 py-2">{{ $r->v_head }}</td><td class="px-3 py-2 text-center">{{ $r->dr }}</td><td class="px-3 py-2 text-center">{{ $r->cr }}</td><td class="px-3 py-2 text-center">{{ $r->bal }}</td></tr>@endforeach</tbody></table></div>@endif
</div>
@endsection
