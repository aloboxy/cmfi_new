@extends('layouts.app')
@section('title', translate('searching_results'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('student.index') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-left class="size-4"/></a>
        <span class="font-semibold text-slate-800">{{ translate('searching_results') }} - "{{ $search_text }}"</span>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-4 py-3">{{ translate('photo') }}</th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('register_no') }}</th><th class="px-4 py-3">{{ translate('class') }}</th><th class="px-4 py-3">{{ translate('section') }}</th><th class="px-4 py-3">{{ translate('action') }}</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($query as $row)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-2"><img src="{{ get_image_url('student',$row->photo) }}" class="size-9 rounded-full border"></td>
                        <td class="px-4 py-2 font-medium">{{ $row->first_name }} {{ $row->last_name }}</td>
                        <td class="px-4 py-2">{{ $row->register_no }}</td>
                        <td class="px-4 py-2">{{ $row->class_name }}</td>
                        <td class="px-4 py-2">{{ $row->section_name }}</td>
                        <td class="px-4 py-2"><a href="{{ route('student.profile',$row->student_id ?? $row->id) }}" class="text-[#2563eb] text-xs font-medium">{{ translate('details') }}</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
