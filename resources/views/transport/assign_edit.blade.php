@extends('layouts.app')
@section('title', translate('assign_vehicle'))
@section('content')
<div class="max-w-lg mx-auto rounded-2xl bg-white border p-6">
 <h3 class="font-bold mb-4">Edit Assign</h3>
 <form method="POST" action="{{ route('transport.assign_edit',$assign->route_id) }}" class="space-y-3">
  @csrf
  <select name="route_id" class="w-full rounded-xl border px-3 py-2 text-sm" required>@foreach($routes as $r)<option value="{{ $r->id }}" {{ $r->id==$assign->route_id?'selected':'' }}>{{ $r->name }}</option>@endforeach</select>
  <select name="stoppage_id" class="w-full rounded-xl border px-3 py-2 text-sm" required>@foreach($stoppages as $s)<option value="{{ $s->id }}" {{ $s->id==$assign->stoppage_id?'selected':'' }}>{{ $s->stop_position }}</option>@endforeach</select>
  <select name="vehicle[]" multiple class="w-full rounded-xl border px-3 py-2 text-sm h-32" required>@foreach($vehicles as $v)<option value="{{ $v->id }}" {{ in_array($v->id,$selectedVehicles)?'selected':'' }}>{{ $v->vehicle_no }}</option>@endforeach</select>
  <div class="flex justify-between"><a href="{{ route('transport.assign') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm">Update</button></div>
 </form>
</div>
@endsection
