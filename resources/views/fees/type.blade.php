@extends('layouts.app')
@section('title', translate('fees_type'))
@section('content')
<div class="space-y-5">
 <div class="flex items-center justify-between">
  <h2 class="text-lg font-bold">{{ translate('fees_type') }}</h2>
 </div>
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
  <div class="lg:col-span-1 rounded-2xl bg-white border border-slate-200 p-5">
   <h3 class="font-semibold mb-4">{{ translate('create') }}</h3>
   <form method="POST" action="{{ route('fees.type') }}" class="space-y-3">
    @csrf
    @if(is_superadmin_loggedin())
    <div>
     <label class="block text-sm font-medium mb-1">{{ translate('branch') }} *</label>
     <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
      <option value="">{{ translate('select') }}</option>
      @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
     </select>
    </div>
    @endif
    <div>
     <label class="block text-sm font-medium mb-1">{{ translate('name') }} *</label>
     <input type="text" name="type_name" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
    </div>
    <div>
     <label class="block text-sm font-medium mb-1">{{ translate('description') }}</label>
     <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
    </div>
    <button class="w-full rounded-xl bg-[#2563eb] py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button>
   </form>
  </div>
  <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-200 overflow-hidden">
   <div class="overflow-x-auto">
    <table class="w-full text-sm">
     <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">#</th><th class="px-4 py-3 text-left">{{ translate('name') }}</th><th class="px-4 py-3 text-left">{{ translate('branch') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead>
     <tbody class="divide-y divide-slate-100">
      @forelse($categorylist as $i=>$row)
      <tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3">{{ $row->branch_name ?? '' }}</td>
       <td class="px-4 py-3 text-right flex justify-end gap-1">
        <a href="{{ route('fees.type_edit',$row->id) }}" class="px-3 py-1 rounded-lg border text-xs">Edit</a>
        <form method="POST" action="{{ route('fees.type_delete',$row->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-3 py-1 rounded-lg bg-red-50 text-red-600 text-xs">Delete</button></form>
       </td></tr>
      @empty <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr> @endforelse
     </tbody>
    </table>
   </div>
  </div>
 </div>
</div>
@endsection
