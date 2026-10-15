@extends('layouts.app')
@section('title', translate('deposit'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">Deposit Voucher</h2>
 <form method="POST" action="{{ route('accounting.voucher_save') }}" enctype="multipart/form-data" class="rounded-2xl bg-white border p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
  @csrf
  <input type="hidden" name="voucher_type" value="deposit">
  <select name="account_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">Select Account</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select>
  <select name="voucher_head_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">Voucher Head</option>@foreach($voucherHeads as $vh)<option value="{{ $vh->id }}">{{ $vh->name }}</option>@endforeach</select>
  <input type="number" step="0.01" name="amount" placeholder="Amount *" class="rounded-xl border px-3 py-2 text-sm" required>
  <input type="date" name="date" value="{{ date('Y-m-d') }}" class="rounded-xl border px-3 py-2 text-sm" required>
  <input type="text" name="ref_no" placeholder="Ref No" class="rounded-xl border px-3 py-2 text-sm">
  <select name="pay_via" class="rounded-xl border px-3 py-2 text-sm"><option value="">Pay Via</option><option value="1">Cash</option><option value="2">Bank</option></select>
  <input type="file" name="attachment_file" class="rounded-xl border px-3 py-2 text-sm">
  <textarea name="description" placeholder="Description" class="rounded-xl border px-3 py-2 text-sm md:col-span-3" rows="2"></textarea>
  <button class="rounded-xl bg-emerald-600 text-white px-6 py-2.5 text-sm font-semibold">Save Deposit</button>
 </form>
 <div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-2">#</th><th class="px-3 py-2 text-left">Account</th><th class="px-3 py-2 text-left">Voucher Head</th><th class="px-3 py-2">Amount</th><th class="px-3 py-2">Date</th><th class="px-3 py-2 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($voucherlist as $i=>$v)<tr><td class="px-3 py-2">{{ $i+1 }}</td><td class="px-3 py-2">{{ $v->ac_name }}</td><td class="px-3 py-2">{{ $v->v_head }}</td><td class="px-3 py-2 text-center">{{ $v->amount }}</td><td class="px-3 py-2 text-center">{{ $v->date }}</td><td class="px-3 py-2 text-right flex justify-end gap-1"><a href="{{ route('accounting.voucher_deposit_edit',$v->id) }}" class="px-2 py-1 border rounded text-xs">Edit</a><form method="POST" action="{{ route('accounting.voucher_delete',$v->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs">Delete</button></form></td></tr>@endforeach</tbody></table></div>
</div>
@endsection
