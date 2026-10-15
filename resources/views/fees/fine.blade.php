@extends('layouts.app')
@section('title', translate('fine_setup'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('fine_setup') }}</h2>
 <form method="POST" action="{{ route('fees.fine') }}" class="rounded-2xl bg-white border border-slate-200 p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
  @csrf
  @if(is_superadmin_loggedin())<div><label class="block text-sm font-medium mb-1">{{ translate('branch') }} *</label><select name="branch_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>@endif
  <div><label class="block text-sm font-medium mb-1">{{ translate('group_name') }} *</label><select name="group_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">{{ translate('select') }}</option>@foreach($feeGroups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach</select></div>
  <div><label class="block text-sm font-medium mb-1">{{ translate('fees_type') }} *</label><select name="fine_type_id" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="">{{ translate('select') }}</option>@foreach($feesTypes as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
  <div><label class="block text-sm font-medium mb-1">{{ translate('fine_type') }} *</label><select name="fine_type" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="1">Fixed</option><option value="2">Percentage</option></select></div>
  <div><label class="block text-sm font-medium mb-1">{{ translate('fine') }} {{ translate('value') }} *</label><input type="number" step="0.01" name="fine_value" class="w-full rounded-xl border px-3 py-2 text-sm" required></div>
  <div><label class="block text-sm font-medium mb-1">{{ translate('late_fee_frequency') }} *</label><input type="number" name="fee_frequency" class="w-full rounded-xl border px-3 py-2 text-sm" required></div>
  <div class="md:col-span-3"><button class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-white text-sm font-semibold">{{ translate('save') }}</button></div>
 </form>
 <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
  <table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3 text-left">{{ translate('group_name') }}</th><th class="px-4 py-3 text-left">{{ translate('fees_type') }}</th><th class="px-4 py-3">Fine</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y">
   @foreach($finelist as $i=>$f)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3">{{ $f->group_name }}</td><td class="px-4 py-3">{{ $f->type_name }}</td><td class="px-4 py-3">{{ $f->fine_value }} ({{ $f->fine_type==1?'Fixed':'%'}})</td><td class="px-4 py-3 text-right flex justify-end gap-1"><a href="{{ route('fees.fine_edit',$f->id) }}" class="px-3 py-1 border rounded-lg text-xs">Edit</a><form method="POST" action="{{ route('fees.fine_delete',$f->id) }}" onsubmit="return confirm('Sure?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs">Delete</button></form></td></tr>@endforeach
  </tbody></table>
 </div>
</div>
@endsection
