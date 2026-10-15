@extends('layouts.app')
@section('title', translate('fees_reminder'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('fees_reminder') }}</h2>
 <form method="POST" action="{{ route('fees.reminder') }}" class="rounded-2xl bg-white border p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
  @csrf
  @if(is_superadmin_loggedin())<div><label class="block text-sm font-medium mb-1">{{ translate('branch') }} *</label><select name="branch_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">{{ translate('select') }}</option></select></div>@endif
  <div><label class="block text-sm font-medium mb-1">{{ translate('frequency') }} *</label><select name="frequency" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="before">Before</option><option value="after">After</option></select></div>
  <div><label class="block text-sm font-medium mb-1">{{ translate('days') }} *</label><input type="number" name="days" class="w-full rounded-xl border px-3 py-2 text-sm" required></div>
  <div class="md:col-span-3"><label class="block text-sm font-medium mb-1">{{ translate('message') }} *</label><textarea name="message" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm" required></textarea></div>
  <div class="flex gap-4"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_student" value="1"> Student</label><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="chk_guardian" value="1"> Guardian</label></div>
  <div class="md:col-span-3"><button class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-white text-sm font-semibold">Save</button></div>
 </form>
 <div class="rounded-2xl bg-white border overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3 text-left">Frequency</th><th class="px-4 py-3">Days</th><th class="px-4 py-3 text-left">Message</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">@foreach($reminderlist as $i=>$r)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3">{{ $r->frequency }}</td><td class="px-4 py-3 text-center">{{ $r->days }}</td><td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($r->message,80) }}</td><td class="px-4 py-3 text-right flex justify-end gap-1"><a href="{{ route('fees.reminder_edit',$r->id) }}" class="px-3 py-1 border rounded-lg text-xs">Edit</a><form method="POST" action="{{ route('fees.reminder_delete',$r->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs">Delete</button></form></td></tr>@endforeach</tbody></table></div>
</div>
@endsection
