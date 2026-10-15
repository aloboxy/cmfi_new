@extends('layouts.app')
@section('title', translate('office_accounting'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('office_accounting') }} - Accounts</h2>
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
  <div class="rounded-2xl bg-white border p-5">
   <h3 class="font-semibold mb-4">Create Account</h3>
   <form method="POST" action="{{ route('accounting.index') }}" class="space-y-3">
    @csrf
    @if(is_superadmin_loggedin())<select name="branch_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">{{ translate('branch') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>@endif
    <input type="text" name="account_name" placeholder="Account Name *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <input type="text" name="account_number" placeholder="Account Number" class="w-full rounded-xl border px-3 py-2 text-sm">
    <input type="number" step="0.01" name="opening_balance" placeholder="Opening Balance" class="w-full rounded-xl border px-3 py-2 text-sm">
    <textarea name="description" placeholder="Description" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea>
    <button class="w-full rounded-xl bg-[#2563eb] py-2.5 text-white text-sm font-semibold">Save</button>
   </form>
  </div>
  <div class="lg:col-span-2 rounded-2xl bg-white border overflow-hidden">
   <table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3">Balance</th><th class="px-4 py-3 text-right">Action</th></tr></thead>
    <tbody class="divide-y">@foreach($accountslist as $i=>$a)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $a->name }}<div class="text-xs text-slate-500">{{ $a->number }}</div></td><td class="px-4 py-3 text-center">{{ $a->balance }}</td><td class="px-4 py-3 text-right flex justify-end gap-1"><a href="{{ route('accounting.edit',$a->id) }}" class="px-3 py-1 border rounded-lg text-xs">Edit</a><form method="POST" action="{{ route('accounting.delete',$a->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs">Delete</button></form></td></tr>@endforeach</tbody>
   </table>
  </div>
 </div>
</div>
@endsection
