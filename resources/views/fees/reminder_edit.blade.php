@extends('layouts.app')
@section('title', translate('fees_reminder'))
@section('content')
<div class="max-w-xl mx-auto rounded-2xl bg-white border p-6">
 <h3 class="font-bold mb-4">Edit Reminder</h3>
 <form method="POST" action="{{ route('fees.reminder_edit',$reminder->id) }}" class="space-y-4">
  @csrf
  <div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium mb-1">Frequency *</label><select name="frequency" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="before" {{ $reminder->frequency=='before'?'selected':'' }}>Before</option><option value="after" {{ $reminder->frequency=='after'?'selected':'' }}>After</option></select></div><div><label class="block text-sm font-medium mb-1">Days *</label><input type="number" name="days" value="{{ $reminder->days }}" class="w-full rounded-xl border px-3 py-2 text-sm" required></div></div>
  <div><label class="block text-sm font-medium mb-1">Message *</label><textarea name="message" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm" required>{{ $reminder->message }}</textarea></div>
  <div class="flex gap-4"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_student" value="1" {{ $reminder->student?'checked':'' }}> Student</label><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_guardian" value="1" {{ $reminder->guardian?'checked':'' }}> Guardian</label></div>
  <div class="flex justify-between"><a href="{{ route('fees.reminder') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm">Update</button></div>
 </form>
</div>
@endsection
