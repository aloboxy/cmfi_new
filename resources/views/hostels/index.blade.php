@extends('layouts.app')
@section('title', translate('hostel_master'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('hostel_master') }}</h2>
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
  <div class="rounded-2xl bg-white border p-5">
   <h3 class="font-semibold mb-4">Add Hostel</h3>
   <form method="POST" action="{{ route('hostels.index') }}" class="space-y-3">
    @csrf
    @if(is_superadmin_loggedin())<select name="branch_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">Branch</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>@endif
    <input type="text" name="name" placeholder="Hostel Name *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <select name="category_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">Category</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
    <input type="text" name="watchman_name" placeholder="Watchman Name *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <textarea name="hostel_address" placeholder="Address" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea>
    <textarea name="remarks" placeholder="Remarks" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea>
    <button class="w-full rounded-xl bg-[#2563eb] py-2.5 text-white text-sm font-semibold">Save</button>
   </form>
  </div>
  <div class="lg:col-span-2 rounded-2xl bg-white border overflow-hidden">
   <table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3">Category</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($hostellist as $i=>$h)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $h->name }}</td><td class="px-4 py-3 text-center">{{ $h->category_name }}</td><td class="px-4 py-3 text-right flex justify-end gap-1"><a href="{{ route('hostels.edit',$h->id) }}" class="px-2 py-1 border rounded text-xs">Edit</a><form method="POST" action="{{ route('hostels.delete',$h->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs">Delete</button></form></td></tr>@endforeach</tbody></table>
  </div>
 </div>
</div>
@endsection
