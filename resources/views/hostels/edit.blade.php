@extends('layouts.app')
@section('title', translate('hostel_master'))
@section('content')
<div class="max-w-lg mx-auto rounded-2xl bg-white border p-6">
 <h3 class="font-bold mb-4">Edit Hostel</h3>
 <form method="POST" action="{{ route('hostels.edit',$hostel->id) }}" class="space-y-3">
  @csrf
  <input type="text" name="name" value="{{ $hostel->name }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <select name="category_id" class="w-full rounded-xl border px-3 py-2 text-sm" required>@foreach($categories as $c)<option value="{{ $c->id }}" {{ $c->id==$hostel->category_id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select>
  <input type="text" name="watchman_name" value="{{ $hostel->watchman }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <textarea name="hostel_address" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ $hostel->address }}</textarea>
  <textarea name="remarks" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ $hostel->remarks }}</textarea>
  <div class="flex justify-between"><a href="{{ route('hostels.index') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm">Update</button></div>
 </form>
</div>
@endsection
