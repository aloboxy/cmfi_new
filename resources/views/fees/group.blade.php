@extends('layouts.app')
@section('title', translate('fees_group'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('fees_group') }}</h2>
 <div class="rounded-2xl bg-white border border-slate-200 p-5">
  <form method="POST" action="{{ route('fees.group') }}" class="space-y-4">
   @csrf
   @if(is_superadmin_loggedin())
   <div><label class="block text-sm font-medium mb-1">{{ translate('branch') }} *</label><select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
   @endif
   <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><label class="block text-sm font-medium mb-1">{{ translate('group_name') }} *</label><input type="text" name="name" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required></div>
    <div><label class="block text-sm font-medium mb-1">{{ translate('description') }}</label><input type="text" name="description" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></div>
   </div>
   <div class="border rounded-xl overflow-hidden">
    <table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-3 py-2 text-left">✔</th><th class="px-3 py-2 text-left">{{ translate('fees_type') }}</th><th class="px-3 py-2 text-left">{{ translate('due_date') }}</th><th class="px-3 py-2 text-left">{{ translate('amount') }}</th></tr></thead>
     <tbody>
      @foreach($feesTypes as $idx=>$ft)
      <tr class="border-t"><td class="px-3 py-2"><input type="checkbox" name="elem[{{ $idx }}][fees_type_id]" value="{{ $ft->id }}" onchange="this.closest('tr').querySelectorAll('input[type=text],input[type=date]').forEach(i=>i.disabled=!this.checked)"></td>
       <td class="px-3 py-2">{{ $ft->name }}</td><td class="px-3 py-2"><input type="date" name="elem[{{ $idx }}][due_date]" class="rounded-lg border px-2 py-1 text-sm" disabled></td><td class="px-3 py-2"><input type="number" step="0.01" name="elem[{{ $idx }}][amount]" class="rounded-lg border px-2 py-1 text-sm w-28" disabled></td></tr>
      @endforeach
     </tbody>
    </table>
   </div>
   <button class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-white text-sm font-semibold">{{ translate('save') }}</button>
  </form>
 </div>
 <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
  <table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">#</th><th class="px-4 py-3 text-left">{{ translate('group_name') }}</th><th class="px-4 py-3 text-left">{{ translate('branch') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead>
   <tbody class="divide-y">
    @foreach($categorylist as $i=>$g)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $g->name }}</td><td class="px-4 py-3">{{ $g->branch_name }}</td><td class="px-4 py-3 text-right flex justify-end gap-1"><a href="{{ route('fees.group_edit',$g->id) }}" class="px-3 py-1 border rounded-lg text-xs">Edit</a><form method="POST" action="{{ route('fees.group_delete',$g->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs">Delete</button></form></td></tr>@endforeach
   </tbody>
  </table>
 </div>
</div>
@endsection
