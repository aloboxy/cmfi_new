@extends('layouts.app')
@section('title', translate('parents_list'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('parents_list') }}</span>
        <a href="{{ route('parents.create') }}" class="ml-auto rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-medium text-white">{{ translate('add_parent') }}</a>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-4 py-3">{{ translate('photo') }}</th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('relation') }}</th><th class="px-4 py-3">{{ translate('email') }}</th><th class="px-4 py-3">{{ translate('mobile_no') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($parents as $p)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-2"><img src="{{ get_image_url('parent',$p->photo) }}" class="size-9 rounded-full border object-cover"></td>
                        <td class="px-4 py-2 font-medium text-slate-800">{{ $p->name }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $p->relation }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $p->email }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $p->mobileno }}</td>
                        <td class="px-4 py-2">
                            <div class="flex justify-end gap-1.5">
                                @if(get_permission('parent','is_edit'))
                                <a href="{{ route('parents.profile',$p->id) }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-[#2563eb]"><x-heroicon-o-pencil-square class="size-4"/></a>
                                @endif
                                @if(get_permission('parent','is_delete'))
                                <form method="POST" action="{{ route('parents.delete',$p->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-red-600"><x-heroicon-o-trash class="size-4"/></button>
                                </form>
                                @endif
                            </div>
                        </td>
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
