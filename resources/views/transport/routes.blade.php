@extends('layouts.app')
@section('title', translate('route_master'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('route_master') }}</h2>
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
  <div class="rounded-2xl bg-white border p-5">
   <h3 class="font-semibold mb-4">Create Route</h3>
   <form method="POST" action="{{ route('transport.routes') }}" class="space-y-3">
    @csrf
    @if(is_superadmin_loggedin())<select name="branch_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">Branch</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>@endif
    <input type="text" name="route_name" placeholder="Route Name *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <input type="text" name="start_place" placeholder="Start Place *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <input type="text" name="stop_place" placeholder="Stop Place *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <textarea name="remarks" placeholder="Remarks" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea>
    <button class="w-full rounded-xl bg-[#2563eb] py-2.5 text-white text-sm font-semibold">Save</button>
   </form>
  </div>
  <div class="lg:col-span-2 rounded-2xl bg-white border overflow-hidden">
   <table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3">Stop</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($transportlist as $i=>$r)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $r->name }}</td><td class="px-4 py-3 text-center">{{ $r->stop_place }}</td><td class="px-4 py-3 text-right flex justify-end gap-1"><a href="{{ route('transport.route_edit',$r->id) }}" class="px-2 py-1 border rounded text-xs">Edit</a><form method="POST" action="{{ route('transport.route_delete',$r->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs">Delete</button></form></td></tr>@endforeach</tbody></table>
  </div>
 </div>
</div>
@endsection
