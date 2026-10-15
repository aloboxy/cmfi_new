@extends('layouts.app')
@section('title', translate('assign_vehicle'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('assign_vehicle') }}</h2>
 <form method="POST" action="{{ route('transport.assign') }}" class="rounded-2xl bg-white border p-5 grid grid-cols-1 md:grid-cols-4 gap-4">
  @csrf
  <select name="route_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">Route</option>@foreach($routes as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select>
  <select name="stoppage_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">Stoppage</option>@foreach($stoppages as $s)<option value="{{ $s->id }}">{{ $s->stop_position }}</option>@endforeach</select>
  <select name="vehicle[]" multiple class="rounded-xl border px-3 py-2 text-sm h-24" required>@foreach($vehicles as $v)<option value="{{ $v->id }}">{{ $v->vehicle_no }}</option>@endforeach</select>
  <button class="rounded-xl bg-[#2563eb] text-white px-4 py-2 text-sm">Assign</button>
 </form>
 <div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Route</th><th class="px-4 py-3">Stoppage</th><th class="px-4 py-3 text-left">Vehicles</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($assignList as $a)<tr><td class="px-4 py-3">{{ $a->name }}</td><td class="px-4 py-3 text-center">{{ $a->stop_position }}</td><td class="px-4 py-3">@foreach($a->vehicles as $v)<span class="inline-block bg-slate-100 rounded px-2 py-0.5 text-xs mr-1">{{ $v->vehicle_no }}</span>@endforeach</td><td class="px-4 py-3 text-right flex justify-end gap-1"><a href="{{ route('transport.assign_edit',$a->route_id) }}" class="px-2 py-1 border rounded text-xs">Edit</a><form method="POST" action="{{ route('transport.assign_delete',$a->route_id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs">Delete</button></form></td></tr>@endforeach</tbody></table></div>
</div>
@endsection
