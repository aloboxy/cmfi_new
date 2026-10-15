@extends('layouts.app')

@section('title', translate('branch'))

@section('content')
<div class="space-y-5">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500">
            <x-heroicon-o-home class="size-4" />
        </a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('branch') }}</span>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: '{{ $errors->any() ? 'create' : 'list' }}' }" class="space-y-4">
        <div class="flex items-center gap-2">
            <button @click="tab='list'" :class="tab==='list' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition">
                <x-heroicon-o-list-bullet class="size-4" /> {{ translate('branch_list') }}
            </button>
            <button @click="tab='create'" :class="tab==='create' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition">
                <x-heroicon-o-plus-circle class="size-4" /> {{ translate('create_branch') }}
            </button>
        </div>

        {{-- List --}}
        <div x-show="tab==='list'" x-cloak class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-building-office-2 class="size-4" /></span>
                    {{ translate('branch_list') }}
                </h3>
                <span class="text-xs text-slate-500">{{ $branches->count() }} {{ translate('branch') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">{{ translate('branch_name') }}</th>
                            <th class="px-4 py-3">{{ translate('school_name') }}</th>
                            <th class="px-4 py-3">{{ translate('email') }}</th>
                            <th class="px-4 py-3">{{ translate('mobile_no') }}</th>
                            <th class="px-4 py-3">{{ translate('currency') }}</th>
                            <th class="px-4 py-3">{{ translate('symbol') }}</th>
                            <th class="px-4 py-3">{{ translate('city') }}</th>
                            <th class="px-4 py-3">{{ translate('address') }}</th>
                            <th class="px-4 py-3 text-right">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($branches as $i => $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $row->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->school_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->email }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->mobileno }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->currency }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->symbol }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->city }} {{ $row->state ? ', '.$row->state : '' }}</td>
                            <td class="px-4 py-3 text-slate-600 max-w-[200px] truncate">{{ $row->address }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('branch.edit', $row->id) }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#2563eb] transition" title="{{ translate('edit') }}">
                                        <x-heroicon-o-pencil-square class="size-4" />
                                    </a>
                                    <form method="POST" action="{{ route('branch.destroy', $row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition" title="{{ translate('delete') }}">
                                            <x-heroicon-o-trash class="size-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Create --}}
        <div x-show="tab==='create'" x-cloak class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-plus-circle class="size-4" /></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('create_branch') }}</h3>
            </div>
            <form method="POST" action="{{ route('branch.store') }}" class="p-5">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch_name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="branch_name" value="{{ old('branch_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none transition @error('branch_name') border-red-300 @enderror" required>
                        @error('branch_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('school_name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="school_name" value="{{ old('school_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('school_name') border-red-300 @enderror" required>
                        @error('school_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('email') border-red-300 @enderror" required>
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="mobileno" value="{{ old('mobileno') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('mobileno') border-red-300 @enderror" required>
                        @error('mobileno')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('currency') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="currency" value="{{ old('currency') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('currency') border-red-300 @enderror" required>
                        @error('currency')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('currency_symbol') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="currency_symbol" value="{{ old('currency_symbol') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none @error('currency_symbol') border-red-300 @enderror" required>
                        @error('currency_symbol')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('city') }}</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('state') }}</label>
                        <input type="text" name="state" value="{{ old('state') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('address') }}</label>
                        <textarea name="address" rows="3" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">{{ old('address') }}</textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                        <x-heroicon-o-check-circle class="size-4" /> {{ translate('save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
