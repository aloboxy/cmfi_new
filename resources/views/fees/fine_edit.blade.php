@extends('layouts.app')
@section('title', translate('fine_setup'))
@section('content')
<div class="max-w-2xl mx-auto rounded-2xl bg-white border p-6">
 <h3 class="font-bold mb-4">{{ translate('edit') }}</h3>
 <form method="POST" action="{{ route('fees.fine_edit',$fine->id) }}" class="grid grid-cols-2 gap-4">
  @csrf
  <div><label class="block text-sm font-medium mb-1">Group *</label><select name="group_id" class="w-full rounded-xl border px-3 py-2 text-sm" required>@foreach($feeGroups as $g)<option value="{{ $g->id }}" {{ $g->id==$fine->group_id?'selected':'' }}>{{ $g->name }}</option>@endforeach</select></div>
  <div><label class="block text-sm font-medium mb-1">Fees Type *</label><select name="fine_type_id" class="w-full rounded-xl border px-3 py-2 text-sm" required>@foreach($feesTypes as $t)<option value="{{ $t->id }}" {{ $t->id==$fine->type_id?'selected':'' }}>{{ $t->name }}</option>@endforeach</select></div>
  <div><label class="block text-sm font-medium mb-1">Fine Type *</label><select name="fine_type" class="w-full rounded-xl border px-3 py-2 text-sm" required><option value="1" {{ $fine->fine_type==1?'selected':'' }}>Fixed</option><option value="2" {{ $fine->fine_type==2?'selected':'' }}>Percentage</option></select></div>
  <div><label class="block text-sm font-medium mb-1">Fine Value *</label><input type="number" step="0.01" name="fine_value" value="{{ $fine->fine_value }}" class="w-full rounded-xl border px-3 py-2 text-sm" required></div>
  <div><label class="block text-sm font-medium mb-1">Fee Frequency *</label><input type="number" name="fee_frequency" value="{{ $fine->fee_frequency }}" class="w-full rounded-xl border px-3 py-2 text-sm" required></div>
  <div class="col-span-2 flex justify-between"><a href="{{ route('fees.fine') }}" class="px-4 py-2 border rounded-xl text-sm">Back</a><button class="px-6 py-2 bg-[#2563eb] text-white rounded-xl text-sm">Update</button></div>
 </form>
</div>
@endsection
