@extends('layouts.app')
@section('title', translate('roles'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><a href="{{ route('role.index') }}" class="hover:text-[#2563eb]">{{ translate('roles') }}</a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('permission') }}</span></div>
    <form method="POST" action="{{ route('role.permission',$role_id) }}" class="space-y-4">
        @csrf
        @foreach($modules as $mod)
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 font-bold text-sm">{{ $mod->name }}</div>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-2 text-left">{{ translate('permission') }}</th><th class="px-4 py-2 text-center">{{ translate('view') }}</th><th class="px-4 py-2 text-center">{{ translate('add') }}</th><th class="px-4 py-2 text-center">{{ translate('edit') }}</th><th class="px-4 py-2 text-center">{{ translate('delete') }}</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($mod->permissions as $perm)<tr><td class="px-4 py-2">{{ $perm->name }}</td>
                <td class="px-4 py-2 text-center">@if($perm->show_view)<input type="checkbox" name="privileges[{{ $perm->id }}][view]" value="1" @checked($perm->is_view) class="rounded text-[#2563eb]">@endif</td>
                <td class="px-4 py-2 text-center">@if($perm->show_add)<input type="checkbox" name="privileges[{{ $perm->id }}][add]" value="1" @checked($perm->is_add) class="rounded text-[#2563eb]">@endif</td>
                <td class="px-4 py-2 text-center">@if($perm->show_edit)<input type="checkbox" name="privileges[{{ $perm->id }}][edit]" value="1" @checked($perm->is_edit) class="rounded text-[#2563eb]">@endif</td>
                <td class="px-4 py-2 text-center">@if($perm->show_delete)<input type="checkbox" name="privileges[{{ $perm->id }}][delete]" value="1" @checked($perm->is_delete) class="rounded text-[#2563eb]">@endif</td>
            </tr>@endforeach</tbody></table></div>
        </div>
        @endforeach
        <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-8 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
    </form>
</div>
@endsection
