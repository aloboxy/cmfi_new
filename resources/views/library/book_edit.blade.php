@extends('layouts.app')
@section('title', translate('books'))
@section('content')
<div class="max-w-lg mx-auto rounded-2xl bg-white border p-6">
 <h3 class="font-bold mb-4">Edit Book</h3>
 <form method="POST" action="{{ route('library.book_edit',$book->id) }}" enctype="multipart/form-data" class="space-y-3">
  @csrf
  <input type="text" name="book_title" value="{{ $book->title }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <select name="category_id" class="w-full rounded-xl border px-3 py-2 text-sm" required>@foreach($categories as $c)<option value="{{ $c->id }}" {{ $c->id==$book->category_id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select>
  <input type="text" name="publisher" value="{{ $book->publisher }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="text" name="author" value="{{ $book->author }}" class="w-full rounded-xl border px-3 py-2 text-sm">
  <input type="date" name="purchase_date" value="{{ $book->purchase_date }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="number" step="0.01" name="price" value="{{ $book->price }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="number" name="total_stock" value="{{ $book->total_stock }}" class="w-full rounded-xl border px-3 py-2 text-sm" required>
  <input type="file" name="cover_image" class="w-full rounded-xl border px-3 py-2 text-sm">
  <textarea name="description" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ $book->description }}</textarea>
  <div class="flex justify-between"><a href="{{ route('library.book') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm">Update</button></div>
 </form>
</div>
@endsection
