@extends('layouts.app')
@section('title', translate('employee'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('employee') }}</span>
        <a href="{{ route('employee.create') }}" class="ml-auto rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-medium text-white">{{ translate('add_employee') }}</a>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
            <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-identification class="size-4"/></span>
            <h3 class="text-sm font-bold text-slate-800">{{ translate('employee_list') }} - {{ $roles->firstWhere('id',$act_role)?->name ?? 'All' }}</h3>
        </div>
        <div class="p-4 flex flex-wrap gap-2">
            @foreach($roles as $r)
                <a href="{{ route('employee.index', $r->id) }}" class="rounded-full px-3 py-1 text-xs font-medium border {{ $act_role==$r->id ? 'bg-[#2563eb] text-white border-[#2563eb]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">{{ $r->name }}</a>
            @endforeach
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-4 py-3">{{ translate('photo') }}</th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">Staff ID</th><th class="px-4 py-3">{{ translate('designation') }}</th><th class="px-4 py-3">{{ translate('department') }}</th><th class="px-4 py-3">{{ translate('branch') }}</th><th class="px-4 py-3">{{ translate('mobile_no') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stafflist as $s)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-2"><img src="{{ get_image_url('staff',$s->photo) }}" class="size-9 rounded-full border object-cover"></td>
                        <td class="px-4 py-2 font-medium text-slate-800">{{ $s->name }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $s->staff_id }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $s->designation_name }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $s->department_name }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ get_type_name_by_id('branch',$s->branch_id) }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $s->mobileno }}</td>
                        <td class="px-4 py-2">
                            <div class="flex justify-end gap-1.5">
                                @if(get_permission('employee','is_edit'))
                                <a href="{{ route('employee.profile',$s->id) }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-[#2563eb]"><x-heroicon-o-pencil-square class="size-4"/></a>
                                @endif
                                @if(get_permission('employee','is_delete'))
                                <form method="POST" action="{{ route('employee.delete',$s->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-red-600"><x-heroicon-o-trash class="size-4"/></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
