@extends('layouts.app')
@section('title', translate('translations'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><a href="{{ route('translations.index') }}" class="hover:text-[#2563eb]">{{ translate('translations') }}</a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ $select_language }}</span></div>
    <form method="POST" action="{{ route('translations.update',['lang'=>$select_language]) }}" class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        @csrf
        <input type="hidden" name="submit" value="update">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between"><h3 class="text-sm font-bold">{{ translate('translations') }} - {{ $select_language }}</h3><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
        <div class="overflow-x-auto max-h-[70vh]"><table class="w-full text-sm"><thead class="sticky top-0 bg-slate-50"><tr class="text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3 text-left">{{ translate('word') }}</th><th class="px-4 py-3 text-left">{{ translate('translation') }}</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($query_language as $row)<tr><td class="px-4 py-2 font-mono text-xs">{{ $row->word }}</td><td class="px-4 py-2"><input name="word_{{ $row->word }}" value="{{ $row->$select_language }}" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm"></td></tr>@endforeach</tbody></table></div>
        <div class="p-4 flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-8 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
    </form>
</div>
@endsection
