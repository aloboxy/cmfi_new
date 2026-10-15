@extends('layouts.app')
@section('title', translate('office_accounting'))
@section('content')
<div class="max-w-lg mx-auto rounded-2xl bg-white border p-6">
 <h3 class="font-bold mb-4">Edit Account</h3>
 <form method="POST" action="{{ route('accounting.edit',$account['id']) }}" class="space-y-3">
  @csrf
  <input type="text" name="account_name" value="{{ $account['name'] }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="text" name="account_number" value="{{ $account['number'] }}" class="w-full rounded-xl border px-3 py-2 text-sm">
  <textarea name="description" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ $account['description'] }}</textarea>
  <div class="flex justify-between"><a href="{{ route('accounting.index') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm">Update</button></div>
 </form>
</div>
@endsection
