@extends('layouts.app')
@section('title', translate('voucher_head'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">Voucher Head</h2>
 <form method="POST" action="{{ route('accounting.voucher_head') }}" class="rounded-2xl bg-white border p-5 flex gap-3">
  @csrf
  @if(is_superadmin_loggedin())<select name="branch_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">{{ translate('branch') }}</option></select>@endif
  <input type="text" name="voucher_head" placeholder="Name *" class="rounded-xl border px-3 py-2 text-sm" required>
  <select name="type" class="rounded-xl border px-3 py-2 text-sm" required><option value="income">Income</option><option value="expense">Expense</option></select>
  <button class="rounded-xl bg-[#2563eb] px-6 py-2 text-white text-sm">Save</button>
 </form>
 <div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3">Type</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($productlist as $i=>$p)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3">{{ $p->name }}</td><td class="px-4 py-3 text-center">{{ $p->type }}</td><td class="px-4 py-3 text-right"><form method="POST" action="{{ route('accounting.voucher_head_delete',$p->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs">Delete</button></form></td></tr>@endforeach</tbody></table></div>
</div>
@endsection
