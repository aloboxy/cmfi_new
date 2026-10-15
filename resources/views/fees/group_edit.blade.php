@extends('layouts.app')
@section('title', translate('fees_group'))
@section('content')
<div class="space-y-5">
 <h2 class="text-lg font-bold">{{ translate('edit') }} - {{ translate('fees_group') }}</h2>
 <form method="POST" action="{{ route('fees.group_edit',$group->id) }}" class="rounded-2xl bg-white border border-slate-200 p-5 space-y-4">
  @csrf
  <div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium mb-1">{{ translate('group_name') }} *</label><input type="text" name="name" value="{{ $group->name }}" class="w-full rounded-xl border px-3 py-2 text-sm" required></div><div><label class="block text-sm font-medium mb-1">{{ translate('description') }}</label><input type="text" name="description" value="{{ $group->description }}" class="w-full rounded-xl border px-3 py-2 text-sm"></div></div>
  <table class="w-full text-sm border rounded-xl overflow-hidden"><thead class="bg-slate-50"><tr><th class="px-3 py-2">✔</th><th class="px-3 py-2 text-left">{{ translate('fees_type') }}</th><th class="px-3 py-2">{{ translate('due_date') }}</th><th class="px-3 py-2">{{ translate('amount') }}</th></tr></thead><tbody>
   @php $map = $details->keyBy('fee_type_id'); @endphp
   @foreach($feesTypes as $idx=>$ft) @php $d=$map->get($ft->id); @endphp
   <tr class="border-t"><td class="px-3 py-2"><input type="checkbox" name="elem[{{ $idx }}][fees_type_id]" value="{{ $ft->id }}" {{ $d? 'checked':'' }}></td><td class="px-3 py-2">{{ $ft->name }}</td><td class="px-3 py-2"><input type="date" name="elem[{{ $idx }}][due_date]" value="{{ $d? $d->due_date:'' }}" class="border rounded-lg px-2 py-1 text-sm"></td><td class="px-3 py-2"><input type="number" step="0.01" name="elem[{{ $idx }}][amount]" value="{{ $d? $d->amount:'' }}" class="border rounded-lg px-2 py-1 text-sm w-28"></td></tr>
   @endforeach
  </tbody></table>
  <div class="flex justify-between"><a href="{{ route('fees.group') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm font-semibold">Update</button></div>
 </form>
</div>
@endsection
