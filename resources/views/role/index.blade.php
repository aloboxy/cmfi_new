@extends('layouts.app')
@section('title', translate('roles'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('roles') }}</span></div>
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">{{ translate('add_role') }}</h3>
            <form method="POST" action="{{ route('role.index') }}" class="space-y-3">@csrf<input name="role" placeholder="{{ translate('role_name') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" required>@error('role')<p class="text-xs text-red-600">{{ $message }}</p>@enderror<button type="submit" class="w-full rounded-xl bg-[#2563eb] py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></form>
        </div>
        <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200"><h3 class="text-sm font-bold">{{ translate('roles') }}</h3></div>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('role') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($roles as $i=>$r)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $r->name }}</td><td class="px-4 py-3"><div class="flex justify-end gap-1.5"><a href="{{ route('role.edit',$r->id) }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-pencil-square class="size-4"/></a><a href="{{ route('role.permission',$r->id) }}" class="rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white">{{ translate('permission') }}</a><form method="POST" action="{{ route('role.destroy',$r->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">@csrf @method('DELETE')<button class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-red-600"><x-heroicon-o-trash class="size-4"/></button></form></div></td></tr>@empty<tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse</tbody></table></div>
        </div>
    </div>
</div>
@endsection
