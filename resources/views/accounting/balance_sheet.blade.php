@extends('layouts.app')
@section('title', translate('balance_sheet'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('balance_sheet') }}</h2>
 <div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Account</th><th class="px-4 py-3">Total DR</th><th class="px-4 py-3">Total CR</th><th class="px-4 py-3">Balance</th></tr></thead><tbody class="divide-y">@foreach($results as $r)<tr><td class="px-4 py-3">{{ $r->ac_name }}</td><td class="px-4 py-3 text-center">{{ $r->total_dr }}</td><td class="px-4 py-3 text-center">{{ $r->total_cr }}</td><td class="px-4 py-3 text-center font-semibold">{{ $r->fbalance }}</td></tr>@endforeach</tbody></table></div>
</div>
@endsection
