@extends('layouts.app')
@section('title', translate('roles'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><a href="{{ route('role.index') }}" class="hover:text-[#2563eb]">{{ translate('roles') }}</a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('edit') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5 max-w-xl">
        <h3 class="font-bold text-sm mb-3">{{ translate('edit_role') }}</h3>
        <form method="POST" action="{{ route('role.edit',$role->id) }}" class="space-y-3">@csrf<input name="role" value="{{ $role->name }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" required>@error('role')<p class="text-xs text-red-600">{{ $message }}</p>@enderror<div class="flex justify-end gap-2"><a href="{{ route('role.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm">{{ translate('cancel') }}</a><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('update') }}</button></div></form>
    </div>
</div>
@endsection
