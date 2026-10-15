@extends('layouts.app')
@section('title', translate('profile'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('profile') }}</span><a href="{{ route('profile.password') }}" class="ml-auto text-sm text-[#2563eb]">{{ translate('change_password') }}</a></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5 max-w-3xl">
        <h3 class="font-bold text-sm mb-4">{{ translate('edit_profile') }}</h3>
        <form method="POST" action="{{ route('profile.index') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">{{ translate('name') }} <span class="text-red-500">*</span></label><input name="name" value="{{ old('name',$staff->name ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" required></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label><input name="mobile_no" value="{{ old('mobile_no',$staff->mobileno ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" required></div>
                <div class="sm:col-span-2"><label class="block text-sm font-medium mb-1">{{ translate('present_address') }} <span class="text-red-500">*</span></label><textarea name="present_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" required>{{ old('present_address',$staff->present_address ?? '') }}</textarea></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('email') }} <span class="text-red-500">*</span></label><input name="email" type="email" value="{{ old('email',$staff->email ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" required></div>
                <div><label class="block text-sm font-medium mb-1">Photo</label><input type="file" name="user_photo" accept="image/*" class="text-sm"></div>
            </div>
            <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
        </form>
    </div>
</div>
@endsection
