@extends('layouts.app')

@section('title', translate('subject'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500"><x-heroicon-o-home class="size-4" /></a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('subject') }}</span>
    </div>

    <div x-data="{ tab: 'list' }" class="space-y-4">
        <div class="flex gap-2">
            <button @click="tab='list'" :class="tab==='list' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="rounded-xl px-4 py-2 text-sm font-medium flex items-center gap-2"><x-heroicon-o-list-bullet class="size-4" /> {{ translate('subject_list') }}</button>
            @if(get_permission('subject', 'is_add'))
            <button @click="tab='create'" :class="tab==='create' ? 'bg-[#2563eb] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="rounded-xl px-4 py-2 text-sm font-medium flex items-center gap-2"><x-heroicon-o-plus-circle class="size-4" /> {{ translate('create_subject') }}</button>
            @endif
            <a href="{{ route('subject.class_assign') }}" class="rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50 flex items-center gap-2"><x-heroicon-o-link class="size-4" /> {{ translate('class_assign') }}</a>
            <a href="{{ route('subject.teacher_assign') }}" class="rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50 flex items-center gap-2"><x-heroicon-o-user-group class="size-4" /> {{ translate('teacher_assign') }}</a>
        </div>

        <div x-show="tab==='list'" class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-book-open class="size-4" /></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('subject_list') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">{{ translate('branch') }}</th>
                            <th class="px-4 py-3">{{ translate('subject_name') }}</th>
                            <th class="px-4 py-3">{{ translate('subject_code') }}</th>
                            <th class="px-4 py-3">{{ translate('subject_type') }}</th>
                            <th class="px-4 py-3">{{ translate('subject_author') }}</th>
                            <th class="px-4 py-3 text-right">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($subjectlist as $i=>$row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->branch_name }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $row->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->subject_code }}</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $row->subject_type }}</span></td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->subject_author ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(get_permission('subject', 'is_edit'))
                                    <a href="{{ route('subject.edit', $row->id) }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#2563eb] transition"><x-heroicon-o-pencil-square class="size-4" /></a>
                                    @endif
                                    @if(get_permission('subject', 'is_delete'))
                                    <form method="POST" action="{{ route('subject.destroy', $row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-600 transition"><x-heroicon-o-trash class="size-4" /></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(get_permission('subject', 'is_add'))
        <div x-show="tab==='create'" x-cloak class="rounded-2xl bg-white border border-slate-200 overflow-hidden max-w-3xl">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-plus-circle class="size-4" /></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('create_subject') }}</h3>
            </div>
            <form method="POST" action="{{ route('subject.save') }}" class="p-5 space-y-4">
                @csrf
                @if(is_superadmin_loggedin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                    <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('subject_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('subject_code') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="subject_code" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('subject_author') }}</label>
                    <input type="text" name="subject_author" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('subject_type') }} <span class="text-red-500">*</span></label>
                    <select name="subject_type" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none" required>
                        <option value="">{{ translate('select') }}</option>
                        <option value="Theory">Theory</option>
                        <option value="Practical">Practical</option>
                        <option value="Optional">Optional</option>
                        <option value="Mandatory">Mandatory</option>
                    </select>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition"><x-heroicon-o-check-circle class="size-4" /> {{ translate('save') }}</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
