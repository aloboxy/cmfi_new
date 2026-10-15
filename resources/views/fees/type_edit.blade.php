@extends('layouts.app')
@section('title', translate('fees_type'))
@section('content')
<div class="max-w-lg mx-auto rounded-2xl bg-white border border-slate-200 p-6">
 <h3 class="font-bold mb-4">{{ translate('edit') }} - {{ translate('fees_type') }}</h3>
 <form method="POST" action="{{ route('fees.type_edit',$category->id) }}" class="space-y-3">
  @csrf
  <div><label class="block text-sm font-medium mb-1">{{ translate('name') }} *</label><input type="text" name="type_name" value="{{ $category->name }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required></div>
  <div><label class="block text-sm font-medium mb-1">{{ translate('description') }}</label><textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ $category->description }}</textarea></div>
  <div class="flex justify-between"><a href="{{ route('fees.type') }}" class="px-4 py-2 rounded-xl border text-sm">{{ translate('back') }}</a><button class="px-6 py-2 rounded-xl bg-[#2563eb] text-white text-sm font-semibold">{{ translate('update') }}</button></div>
 </form>
</div>
@endsection
