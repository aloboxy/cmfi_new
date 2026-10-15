@extends('layouts.app')
@section('title', translate('vehicle_master'))
@section('content')
<div class="max-w-lg mx-auto rounded-2xl bg-white border p-6">
 <h3 class="font-bold mb-4">Edit Vehicle</h3>
 <form method="POST" action="{{ route('transport.vehicle_edit',$vehicle->id) }}" class="space-y-3">
  @csrf
  <input type="text" name="vehicle_no" value="{{ $vehicle->vehicle_no }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="number" name="capacity" value="{{ $vehicle->capacity }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="text" name="driver_name" value="{{ $vehicle->driver_name }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="text" name="driver_phone" value="{{ $vehicle->driver_phone }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="text" name="driver_license" value="{{ $vehicle->driver_license }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <div class="flex justify-between"><a href="{{ route('transport.vehicles') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm">Update</button></div>
 </form>
</div>
@endsection
