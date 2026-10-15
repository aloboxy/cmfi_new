@extends('layouts.app')

@section('title', 'Report Card Preview')

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Report Card Preview — Branch {{ $branchId }}</h1>
    <button onclick="window.print()" class="rounded-xl bg-[var(--primary)] px-4 py-2 text-sm font-semibold text-white print:hidden">Print</button>
  </div>
  <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm print:shadow-none print:border-0">
    <div class="text-center border-b pb-4 mb-6">
      <img src="{{ asset('uploads/app_image/report-card-logo.png') }}" onerror="this.style.display='none'" class="h-16 mx-auto mb-2">
      <h2 class="text-lg font-bold">{{ $global_config->institute_name ?? 'School' }}</h2>
      <p class="text-sm text-slate-500">Report Card — Academic Year {{ date('Y') }}</p>
    </div>
    @include('settings.report_card_preview_inline', ['template' => $template, 'mock' => $mock])
    @if(!empty($template->grapejs_html))
      <div class="mt-8 border-t pt-6">
        <h3 class="font-bold mb-2">Custom Design (GrapeJS)</h3>
        <div class="prose max-w-none">{!! $template->grapejs_html !!}<style>{!! $template->grapejs_css !!}</style></div>
      </div>
    @endif
  </div>
</div>
@endsection
