@extends('layouts.app')
@section('title', translate('books'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('books') }}</h2>
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
  <div class="rounded-2xl bg-white border p-5">
   <h3 class="font-semibold mb-4">Add Book</h3>
   <form method="POST" action="{{ route('library.book') }}" enctype="multipart/form-data" class="space-y-3">
    @csrf
    @if(is_superadmin_loggedin())<select name="branch_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">Branch</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>@endif
    <input type="text" name="book_title" placeholder="Book Title *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <select name="category_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">Category</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
    <input type="text" name="author" placeholder="Author" class="w-full rounded-xl border px-3 py-2 text-sm">
    <input type="text" name="publisher" placeholder="Publisher *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <input type="text" name="isbn_no" placeholder="ISBN" class="w-full rounded-xl border px-3 py-2 text-sm">
    <input type="text" name="edition" placeholder="Edition" class="w-full rounded-xl border px-3 py-2 text-sm">
    <input type="date" name="purchase_date" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <input type="number" step="0.01" name="price" placeholder="Price *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <input type="number" name="total_stock" placeholder="Total Stock *" class="w-full rounded-xl border px-3 py-2 text-sm" required>
    <input type="file" name="cover_image" class="w-full rounded-xl border px-3 py-2 text-sm">
    <textarea name="description" placeholder="Description" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea>
    <button class="w-full rounded-xl bg-[#2563eb] py-2.5 text-white text-sm font-semibold">Save</button>
   </form>
  </div>
  <div class="lg:col-span-2 rounded-2xl bg-white border overflow-hidden">
   <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-2">#</th><th class="px-3 py-2 text-left">Title</th><th class="px-3 py-2">Category</th><th class="px-3 py-2">Stock</th><th class="px-3 py-2 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($booklist as $i=>$b)<tr><td class="px-3 py-2">{{ $i+1 }}</td><td class="px-3 py-2 font-medium">{{ $b->title }}</td><td class="px-3 py-2 text-center">{{ $b->category_name }}</td><td class="px-3 py-2 text-center">{{ $b->total_stock }} ({{ $b->issued_copies }} issued)</td><td class="px-3 py-2 text-right flex justify-end gap-1"><a href="{{ route('library.book_edit',$b->id) }}" class="px-2 py-1 border rounded text-xs">Edit</a><form method="POST" action="{{ route('library.book_delete',$b->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs">Delete</button></form></td></tr>@endforeach</tbody></table></div>
  </div>
 </div>
</div>
@endsection
