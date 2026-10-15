@extends('layouts.app')
@section('title', translate('profile'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><a href="{{ route('profile.index') }}" class="hover:text-[#2563eb]">{{ translate('profile') }}</a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('change_password') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5 max-w-xl">
        <h3 class="font-bold text-sm mb-4">{{ translate('change_password') }}</h3>
        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            <div><label class="block text-sm font-medium mb-1">{{ translate('current_password') }} <span class="text-red-500">*</span></label><input type="password" name="current_password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm @error('current_password') border-red-300 @enderror" required>@error('current_password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('new_password') }} <span class="text-red-500">*</span></label><input type="password" name="new_password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm @error('new_password') border-red-300 @enderror" required>@error('new_password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium mb-1">{{ translate('confirm_password') }} <span class="text-red-500">*</span></label><input type="password" name="confirm_password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" required></div>
            <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
        </form>
    </div>
</div>
@endsection
