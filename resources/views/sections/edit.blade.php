@extends('layouts.app')

@section('title', translate('section_control'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('sections.index') }}" class="hover:text-[#2563eb]">{{ translate('section_control') }}</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('edit_section') }}</span>
    </div>

    <div class="flex gap-2">
        @if(get_permission('classes', 'is_view'))
        <a href="{{ route('classes.index') }}" class="rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50 flex items-center gap-2"><x-heroicon-o-academic-cap class="size-4" /> {{ translate('class') }}</a>
        @endif
        <a href="{{ route('sections.index') }}" class="rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50 flex items-center gap-2"><x-heroicon-o-rectangle-group class="size-4" /> {{ translate('section') }}</a>
    </div>

    <div class="max-w-2xl rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
            <span class="grid size-7 place-items-center rounded-lg bg-amber-500/10 text-amber-600"><x-heroicon-o-pencil-square class="size-4" /></span>
            <h3 class="text-sm font-bold text-slate-800">{{ translate('edit_section') }}</h3>
        </div>
        <form method="POST" action="{{ route('sections.save') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="section_id" value="{{ $section['id'] }}">
            @if(is_superadmin_loggedin())
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                    @foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$section['branch_id'])>{{ $b->name }}</option>@endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('name') }} <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $section['name']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('capacity') }}</label>
                <input type="text" name="capacity" value="{{ old('capacity', $section['capacity']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('sections.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ translate('cancel') }}</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition"><x-heroicon-o-check-circle class="size-4" /> {{ translate('update') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
