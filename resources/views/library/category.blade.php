@extends('layouts.app')
@section('title', translate('category'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('category') }}</h2>
 <form method="POST" action="{{ route('library.category') }}" class="rounded-2xl bg-white border p-5 flex gap-3">
  @csrf
  @if(is_superadmin_loggedin())<select name="branch_id" class="w-full max-w-xs rounded-xl border px-3 py-2 text-sm" required><option value="">Branch</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>@endif
  <input type="text" name="name" placeholder="Category Name *" class="flex-1 rounded-xl border px-3 py-2 text-sm" required>
  <button name="save" value="1" class="rounded-xl bg-[#2563eb] px-6 py-2 text-white text-sm">Save</button>
 </form>
 <div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($categorylist as $i=>$c)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3">{{ $c->name }}</td><td class="px-4 py-3 text-right"><form method="POST" action="{{ route('library.category_delete',$c->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs">Delete</button></form></td></tr>@endforeach</tbody></table></div>
</div>
@endsection
