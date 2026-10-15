@extends('layouts.app')
@section('title', translate('upload_content'))
@section('content')
<div class="space-y-5">
 <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border"><x-heroicon-o-home class="size-4"/></a><span>/</span><a href="{{ route('attachments.index') }}" class="hover:text-[#2563eb]">{{ translate('upload_content') }}</a><span>/</span><span class="font-semibold text-slate-800">{{ translate('edit') }}</span></div>
 <div class="rounded-2xl bg-white border p-5 max-w-2xl"><h3 class="font-bold mb-4">{{ translate('edit') }}</h3>
 <form method="POST" action="{{ route('attachments.save') }}" enctype="multipart/form-data">@csrf<input type="hidden" name="attachment_id" value="{{ $data['id'] }}">
  @if(is_superadmin_loggedin())<div class="mb-3"><label class="block text-sm font-medium mb-1">{{ translate('branch') }}</label><select name="branch_id" class="w-full rounded-xl border px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$data['branch_id'])>{{ $b->name }}</option>@endforeach</select></div>@endif
  <div class="mb-3"><label class="block text-sm font-medium mb-1">{{ translate('title') }}</label><input type="text" name="title" value="{{ $data['title'] }}" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required></div>
  <div class="grid grid-cols-2 gap-3 mb-3"><div><label class="block text-sm font-medium mb-1">{{ translate('type') }}</label><select name="type_id" class="w-full rounded-xl border px-3.5 py-2.5 text-sm">@foreach($types as $t)<option value="{{ $t->id }}" @selected($t->id==$data['type_id'])>{{ $t->name }}</option>@endforeach</select></div><div><label class="block text-sm font-medium mb-1">{{ translate('date') }}</label><input type="date" name="date" value="{{ $data['date'] }}" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required></div></div>
  <div class="mb-3"><label class="block text-sm font-medium mb-1">{{ translate('remarks') }}</label><textarea name="remarks" rows="2" class="w-full rounded-xl border px-3.5 py-2.5 text-sm">{{ $data['remarks'] }}</textarea></div>
  <div class="mb-3"><label class="block text-sm font-medium mb-1">{{ translate('attachment') }}</label><input type="file" name="attachment_file" class="w-full rounded-xl border px-3.5 py-2.5 text-sm"><p class="text-xs text-slate-500 mt-1">Current: {{ $data['file_name'] }}</p></div>
  <div class="flex justify-end gap-2"><a href="{{ route('attachments.index') }}" class="px-4 py-2 rounded-xl border text-sm">Cancel</a><button class="px-4 py-2 rounded-xl bg-[#2563eb] text-white text-sm">{{ translate('update') }}</button></div>
 </form></div>
</div>
@endsection
