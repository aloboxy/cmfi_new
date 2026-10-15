@extends('layouts.app')
@section('title','Edit Expense')
@section('content')
<div class="max-w-lg mx-auto rounded-2xl bg-white border p-6">
 <h3 class="font-bold mb-4">Edit Expense</h3>
 <form method="POST" action="{{ route('accounting.voucher_expense_edit',$expense['id']) }}" enctype="multipart/form-data" class="space-y-3">@csrf
  <select name="voucher_head_id" class="w-full rounded-xl border px-3 py-2 text-sm" required>@foreach($voucherHeads as $vh)<option value="{{ $vh->id }}" {{ $vh->id==$expense['voucher_head_id']?'selected':'' }}>{{ $vh->name }}</option>@endforeach</select>
  <input type="date" name="date" value="{{ $expense['date'] }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="text" name="ref_no" value="{{ $expense['ref'] }}" class="w-full rounded-xl border px-3 py-2 text-sm">
  <input type="file" name="attachment_file" class="w-full rounded-xl border px-3 py-2 text-sm">
  <textarea name="description" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ $expense['description'] }}</textarea>
  <div class="flex justify-between"><a href="{{ route('accounting.expense_voucher') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm">Update</button></div>
 </form>
</div>
@endsection
