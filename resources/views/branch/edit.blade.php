@extends('layouts.app')

@section('title', translate('branch'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('branch.index') }}" class="hover:text-[#2563eb]">{{ translate('branch') }}</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('edit_branch') }}</span>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-amber-500/10 text-amber-600"><x-heroicon-o-pencil-square class="size-4" /></span>
                {{ translate('edit_branch') }}
            </h3>
            <a href="{{ route('branch.index') }}" class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ translate('branch_list') }}</a>
        </div>
        <form method="POST" action="{{ route('branch.update', $branch->id) }}" class="p-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="branch_name" value="{{ old('branch_name', $branch->name) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('branch_name') border-red-300 @enderror" required>
                    @error('branch_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('school_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="school_name" value="{{ old('school_name', $branch->school_name) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('school_name') border-red-300 @enderror" required>
                    @error('school_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('email') }} <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $branch->email) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('email') border-red-300 @enderror" required>
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="mobileno" value="{{ old('mobileno', $branch->mobileno) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('mobileno') border-red-300 @enderror" required>
                    @error('mobileno')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('currency') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="currency" value="{{ old('currency', $branch->currency) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('currency') border-red-300 @enderror" required>
                    @error('currency')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('currency_symbol') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $branch->symbol) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('currency_symbol') border-red-300 @enderror" required>
                    @error('currency_symbol')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('city') }}</label>
                    <input type="text" name="city" value="{{ old('city', $branch->city) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('state') }}</label>
                    <input type="text" name="state" value="{{ old('state', $branch->state) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('address') }}</label>
                    <textarea name="address" rows="3" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">{{ old('address', $branch->address) }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('branch.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ translate('cancel') }}</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    <x-heroicon-o-check-circle class="size-4" /> {{ translate('update') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
