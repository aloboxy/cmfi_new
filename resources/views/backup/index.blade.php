@extends('layouts.app')
@section('title', translate('database_backup'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('database_backup') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5 flex items-center justify-between">
        <div><h3 class="font-bold text-sm">{{ translate('database_backup') }}</h3><p class="text-xs text-slate-500">{{ translate('backup_description') }}</p></div>
        <a href="{{ route('backup.create') }}" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('create_backup') }}</a>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200"><h3 class="text-sm font-bold">{{ translate('backup_list') }}</h3></div>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('file_name') }}</th><th class="px-4 py-3">{{ translate('size') }}</th><th class="px-4 py-3">{{ translate('date') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($files as $i=>$f)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-mono text-xs">{{ $f['name'] }}</td><td class="px-4 py-3">{{ number_format($f['size']/1024,1) }} KB</td><td class="px-4 py-3">{{ date('Y-m-d H:i', $f['mtime']) }}</td><td class="px-4 py-3"><div class="flex justify-end gap-1.5"><a href="{{ route('backup.download',['file'=>$f['name']]) }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-down-tray class="size-4"/></a><form method="POST" action="{{ route('backup.delete',$f['name']) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">@csrf @method('DELETE')<button class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-red-600"><x-heroicon-o-trash class="size-4"/></button></form></div></td></tr>@empty<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse</tbody></table></div>
    </div>
</div>
@endsection
