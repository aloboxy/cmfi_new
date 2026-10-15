@extends('layouts.app')
@section('title', translate('hostel_room'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('hostel_room') }}</h2>
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
  <div class="rounded-2xl bg-white border p-5">
   <h3 class="font-semibold mb-4">Add Room</h3>
   <form method="POST" action="{{ route('hostels.rooms') }}" class="space-y-3">
    @csrf
    @if(is_superadmin_loggedin())<select name="branch_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">Branch</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>@endif
    <input type="text" name="name" placeholder="Room Name *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <select name="hostel_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">Hostel</option>@foreach($hostels as $h)<option value="{{ $h->id }}">{{ $h->name }}</option>@endforeach</select>
    <select name="category_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">Category</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
    <input type="number" name="number_of_beds" placeholder="No of Beds *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <input type="number" step="0.01" name="bed_fee" placeholder="Cost Per Bed *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <textarea name="remarks" placeholder="Remarks" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea>
    <button class="w-full rounded-xl bg-[#2563eb] py-2.5 text-white text-sm font-semibold">Save</button>
   </form>
  </div>
  <div class="lg:col-span-2 rounded-2xl bg-white border overflow-hidden">
   <table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3 text-left">Room</th><th class="px-4 py-3">Hostel</th><th class="px-4 py-3">Beds</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($roomlist as $i=>$r)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $r->name }}</td><td class="px-4 py-3 text-center">{{ $r->hostel_name }}</td><td class="px-4 py-3 text-center">{{ $r->no_beds }}</td><td class="px-4 py-3 text-right flex justify-end gap-1"><a href="{{ route('hostels.room_edit',$r->id) }}" class="px-2 py-1 border rounded text-xs">Edit</a><form method="POST" action="{{ route('hostels.room_delete',$r->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs">Delete</button></form></td></tr>@endforeach</tbody></table>
  </div>
 </div>
</div>
@endsection
