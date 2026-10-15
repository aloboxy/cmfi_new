@extends('layouts.app')
@section('title', translate('book_issue_return'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('book_issue_return') }}</h2>
 <form method="POST" action="{{ route('library.bookIssued') }}" class="rounded-2xl bg-white border p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
  @csrf
  <select name="category_id" onchange="fetch('/library/getBooksByCategory',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/x-www-form-urlencoded'},body:'category_id='+this.value}).then(r=>r.text()).then(html=>document.querySelector('[name=book_id]').innerHTML=html)" class="rounded-xl border px-3 py-2 text-sm" required><option value="">Select Category</option></select>
  <select name="book_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="">Select Book</option></select>
  <select name="role_id" class="rounded-xl border px-3 py-2 text-sm" required><option value="3">Teacher</option><option value="7">Student</option></select>
  <input type="number" name="user_id" placeholder="User ID *" class="rounded-xl border px-3 py-2 text-sm" required>
  <input type="date" name="date_of_expiry" class="rounded-xl border px-3 py-2 text-sm" required>
  <button class="rounded-xl bg-[#2563eb] text-white px-4 py-2 text-sm">Issue Book</button>
 </form>
 <div class="rounded-2xl bg-white border overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-2">#</th><th class="px-3 py-2 text-left">Book</th><th class="px-3 py-2">Role</th><th class="px-3 py-2">Issue</th><th class="px-3 py-2">Expiry</th><th class="px-3 py-2">Status</th><th class="px-3 py-2 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($booklist as $i=>$b)<tr><td class="px-3 py-2">{{ $i+1 }}</td><td class="px-3 py-2">{{ $b->title }}</td><td class="px-3 py-2 text-center">{{ $b->role_name }}</td><td class="px-3 py-2 text-center">{{ $b->date_of_issue }}</td><td class="px-3 py-2 text-center">{{ $b->date_of_expiry }}</td><td class="px-3 py-2 text-center">{{ $b->status }}</td><td class="px-3 py-2 text-right"><form method="POST" action="{{ route('library.issued_book_delete',$b->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs">Delete</button></form></td></tr>@endforeach</tbody></table></div></div>
 <form method="POST" action="{{ route('library.bookReturn') }}" class="rounded-2xl bg-white border p-5 flex gap-3">
  @csrf
  <input type="number" name="issue_id" placeholder="Issue ID" class="rounded-xl border px-3 py-2 text-sm" required>
  <input type="date" name="date" class="rounded-xl border px-3 py-2 text-sm" required>
  <select name="type" class="rounded-xl border px-3 py-2 text-sm"><option value="1">Return</option><option value="2">Renew</option></select>
  <input type="number" step="0.01" name="fine_amount" placeholder="Fine" class="rounded-xl border px-3 py-2 text-sm">
  <button class="rounded-xl bg-emerald-600 text-white px-4 py-2 text-sm">Submit</button>
 </form>
</div>
@endsection
