@extends('layouts.app')
@section('title', translate('hostels_room_edit'))
@section('content')
<div class="max-w-lg mx-auto rounded-2xl bg-white border p-6">
 <h3 class="font-bold mb-4">Edit Room</h3>
 <form method="POST" action="{{ route('hostels.room_edit',$room->id) }}" class="space-y-3">
  @csrf
  <input type="text" name="name" value="{{ $room->name }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <select name="hostel_id" class="w-full rounded-xl border px-3 py-2 text-sm" required>@foreach($hostels as $h)<option value="{{ $h->id }}" {{ $h->id==$room->hostel_id?'selected':'' }}>{{ $h->name }}</option>@endforeach</select>
  <select name="category_id" class="w-full rounded-xl border px-3 py-2 text-sm" required>@foreach($categories as $c)<option value="{{ $c->id }}" {{ $c->id==$room->category_id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select>
  <input type="number" name="number_of_beds" value="{{ $room->no_beds }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="number" step="0.01" name="bed_fee" value="{{ $room->bed_fee }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <textarea name="remarks" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ $room->remarks }}</textarea>
  <div class="flex justify-between"><a href="{{ route('hostels.rooms') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm">Update</button></div>
 </form>
</div>
@endsection
