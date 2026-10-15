@extends('layouts.app')

@section('title', translate('section_control'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('section_control') }}</span>
    </div>

    <div class="flex gap-2">
        @if(get_permission('classes', 'is_view'))
        <a href="{{ route('classes.index') }}" class="rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50 flex items-center gap-2"><x-heroicon-o-academic-cap class="size-4" /> {{ translate('class') }}</a>
        @endif
        <a href="{{ route('sections.index') }}" class="rounded-xl bg-[#2563eb] text-white px-4 py-2 text-sm font-medium shadow flex items-center gap-2"><x-heroicon-o-rectangle-group class="size-4" /> {{ translate('section') }}</a>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">
        @if(get_permission('section', 'is_add'))
        <div class="lg:col-span-2">
            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                    <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-plus-circle class="size-4" /></span>
                    <h3 class="text-sm font-bold text-slate-800">{{ translate('create_section') }}</h3>
                </div>
                <form method="POST" action="{{ route('sections.save') }}" class="p-5 space-y-4">
                    @csrf
                    @if(is_superadmin_loggedin())
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                        <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                            <option value="">{{ translate('select') }}</option>
                            @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                        </select>
                        @error('branch_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('capacity') }}</label>
                        <input type="text" name="capacity" value="{{ old('capacity') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                        @error('capacity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                        <x-heroicon-o-check-circle class="size-4" /> {{ translate('save') }}
                    </button>
                </form>
            </div>
        </div>
        @endif

        <div class="{{ get_permission('section', 'is_add') ? 'lg:col-span-3' : 'lg:col-span-5' }}">
            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                    <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-list-bullet class="size-4" /></span>
                    <h3 class="text-sm font-bold text-slate-800">{{ translate('section_list') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">{{ translate('branch') }}</th>
                                <th class="px-4 py-3">{{ translate('section_name') }}</th>
                                <th class="px-4 py-3">{{ translate('capacity') }}</th>
                                <th class="px-4 py-3 text-right">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($sections as $i=>$row)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $row->branch_name }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $row->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $row->capacity ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(get_permission('section', 'is_edit'))
                                        <a href="{{ route('sections.edit', $row->id) }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#2563eb] transition"><x-heroicon-o-pencil-square class="size-4" /></a>
                                        @endif
                                        @if(get_permission('section', 'is_delete'))
                                        <form method="POST" action="{{ route('sections.destroy', $row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-600 transition"><x-heroicon-o-trash class="size-4" /></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
