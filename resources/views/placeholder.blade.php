@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl py-10">
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 text-center shadow-sm">
        <div class="mx-auto grid size-14 place-items-center rounded-2xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
            <x-heroicon-o-wrench-screwdriver class="size-7" />
        </div>
        <h1 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">{{ $title }}</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            {{ translate('this_module_is_under_construction') }} — {{ translate('coming_soon') }}
        </p>
        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500 font-mono">{{ $routeName }}</p>
        <div class="mt-6 flex justify-center gap-3">
            <a href="{{ route('dashboard') }}" class="rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-semibold text-white hover:bg-blue-600 transition">
                {{ translate('back_to_dashboard') }}
            </a>
            <button onclick="history.back()" class="rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                {{ translate('go_back') }}
            </button>
        </div>
    </div>
</div>
@endsection
