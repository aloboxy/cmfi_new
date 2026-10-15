@extends('layouts.app')
@section('title', translate('master_gradesheet'))
@section('content')
<div class="space-y-6">

  {{-- ── Breadcrumb + Actions ── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="flex items-center gap-2 text-[13px] text-slate-500 flex-wrap">
      <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border shadow-xs">
        <x-heroicon-o-home class="size-4"/>
      </a>
      <span>/</span>
      <a href="{{ route('exam.index') }}" class="hover:text-blue-600 font-medium">{{ translate('exam_master') }}</a>
      <span>/</span>
      <span class="font-semibold text-slate-800">{{ translate('master_gradesheet') }}</span>
    </div>

    <div class="flex items-center gap-2 print:hidden">
      @if(!empty($marksMatrix))
        <a href="{{ route('exam.tabulation_export', ['class_id'=>$class_id,'section_id'=>$section_id,'exam_id'=>$exam_id,'session_id'=>$session_id]) }}"
           class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-semibold shadow-xs transition">
          <x-heroicon-o-arrow-down-tray class="size-4"/>
          {{ translate('export_csv') }}
        </a>
      @endif
      <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 text-sm font-semibold shadow-xs transition">
        <x-heroicon-o-printer class="size-4"/>
        {{ translate('print') }}
      </button>
    </div>
  </div>

  {{-- ── Filter Form ── --}}
  <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs print:hidden">
    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 font-bold text-slate-800 text-sm">
      <x-heroicon-o-funnel class="size-4 text-blue-600" />
      {{ translate('filter_master_gradesheet') }}
    </div>
    <form method="GET" action="{{ route('exam.tabulation') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5">
      @if(is_superadmin_loggedin())
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase mb-1">{{ translate('branch') }}</label>
          <select name="branch_id" id="tab_branch" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">{{ translate('select') }}</option>
            @foreach($branches as $b)
              <option value="{{ $b->id }}" @selected($b->id == $branch_id)>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
      @endif

      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">{{ translate('exam') }} <span class="text-red-500">*</span></label>
        <select name="exam_id" id="tab_exam" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="">{{ translate('select') }}</option>
          @foreach($exams as $e)
            <option value="{{ $e->id }}" @selected($e->id == $exam_id)>{{ $e->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">{{ translate('class') }} <span class="text-red-500">*</span></label>
        <select name="class_id" id="tab_class" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="">{{ translate('select') }}</option>
          @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected($c->id == $class_id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">{{ translate('section') }} <span class="text-red-500">*</span></label>
        <select name="section_id" id="tab_section" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="">{{ translate('select') }}</option>
        </select>
      </div>

      <div class="lg:col-span-5 flex justify-end pt-1">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 text-sm font-semibold shadow-xs transition">
          <x-heroicon-o-magnifying-glass class="size-4" />
          {{ translate('generate_gradesheet') }}
        </button>
      </div>
    </form>
  </div>

  {{-- ── Results ── --}}
  @if(!empty($marksMatrix) && isset($classObj))

    {{-- ── Summary Stat Cards ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 print:hidden">

      <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs flex flex-col gap-1">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ translate('enrolled') }}</p>
        <p class="text-2xl font-black text-slate-800">{{ $classStats['total_students'] }}</p>
        <p class="text-[11px] text-slate-400 mt-auto">Total students</p>
      </div>

      <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs flex flex-col gap-1">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ translate('appeared') }}</p>
        <p class="text-2xl font-black text-slate-800">{{ $classStats['appeared_students'] }}</p>
        <p class="text-[11px] text-slate-400 mt-auto">Sat for exam</p>
      </div>

      {{-- Passed — BLUE --}}
      <div class="rounded-2xl bg-blue-50 border border-blue-200 p-4 shadow-xs flex flex-col gap-1">
        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">{{ translate('passed') }}</p>
        <p class="text-2xl font-black text-blue-700">{{ $classStats['passed_students'] }}</p>
        <p class="text-[11px] text-blue-500 mt-auto">Students passed</p>
      </div>

      {{-- Failed — RED --}}
      <div class="rounded-2xl bg-red-50 border border-red-200 p-4 shadow-xs flex flex-col gap-1">
        <p class="text-xs font-semibold text-red-600 uppercase tracking-wide">{{ translate('failed') }}</p>
        <p class="text-2xl font-black text-red-700">{{ $classStats['failed_students'] }}</p>
        <p class="text-[11px] text-red-400 mt-auto">Students failed</p>
      </div>

      <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs flex flex-col gap-1">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ translate('pass_rate') }}</p>
        <p class="text-2xl font-black text-slate-800">{{ $classStats['pass_rate'] }}%</p>
        <p class="text-[11px] text-slate-400 mt-auto">Of appeared</p>
      </div>

      <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs flex flex-col gap-1">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ translate('class_average') }}</p>
        <p class="text-2xl font-black text-slate-800">{{ $classStats['average_score'] }}</p>
        <p class="text-[11px] text-slate-400 mt-auto">Avg. pts</p>
      </div>
    </div>

    {{-- ── Gradesheet Table ── --}}
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">

      <div class="px-5 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/40 print:border-b-2 print:border-slate-300">
        <div>
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <x-heroicon-o-table-cells class="size-5 text-blue-600 print:hidden" />
            {{ translate('master_gradesheet') }}
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            {{ $exam->name ?? '' }} &bull;
            {{ $classObj->name ?? '' }} &bull;
            {{ $sectionObj->name ?? '' }} &bull;
            <span class="text-blue-600 font-semibold">{{ $classStats['total_students'] }} Students</span>
            <span class="print:inline hidden text-slate-400"> &bull; Printed: {{ now()->format('d M Y') }}</span>
          </p>
        </div>
        <div class="flex items-center gap-3 text-xs print:hidden">
          <span class="inline-flex items-center gap-1.5 font-semibold text-blue-700">
            <span class="size-3 rounded-full bg-blue-600 inline-block"></span> Passed = Blue
          </span>
          <span class="inline-flex items-center gap-1.5 font-semibold text-red-700">
            <span class="size-3 rounded-full bg-red-600 inline-block"></span> Failed = Red
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-xs" id="gradesheetTable">
          <thead>
            <tr class="bg-slate-100/80 text-[11px] font-bold uppercase text-slate-600 border-b border-slate-200">
              <th class="px-3 py-3.5 text-center w-10">#</th>
              <th class="px-3 py-3.5 text-center w-12">{{ translate('roll') }}</th>
              <th class="px-3 py-3.5 text-left min-w-[160px]">{{ translate('student') }}</th>

              @foreach($subjects as $subj)
                <th class="px-3 py-3.5 text-center min-w-[90px] border-l border-slate-200">
                  <div class="leading-tight">{{ $subj->name }}</div>
                  @if(!empty($subj->subject_code))
                    <div class="text-[9px] font-normal text-slate-400 lowercase">{{ $subj->subject_code }}</div>
                  @endif
                </th>
              @endforeach

              <th class="px-3 py-3.5 text-center min-w-[80px] border-l border-slate-200">{{ translate('total') }}</th>
              <th class="px-3 py-3.5 text-center min-w-[60px]">%</th>
              <th class="px-3 py-3.5 text-center min-w-[50px]">GPA</th>
              <th class="px-3 py-3.5 text-center min-w-[60px]">{{ translate('grade') }}</th>
              <th class="px-3 py-3.5 text-center min-w-[90px]">{{ translate('status') }}</th>
              <th class="px-3 py-3.5 text-center min-w-[50px]">{{ translate('rank') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($marksMatrix as $idx => $row)
              @php
                $st = $row['student'];
                $isPassed = $row['is_pass'];
              @endphp
              <tr class="hover:bg-slate-50/60 transition {{ !$isPassed ? 'bg-red-50/20' : '' }}">
                <td class="px-3 py-3 text-center text-slate-400 font-medium">{{ $idx + 1 }}</td>
                <td class="px-3 py-3 text-center font-bold text-slate-700">{{ $st->roll }}</td>
                <td class="px-3 py-3">
                  <div class="font-bold text-slate-800 leading-tight text-[12px]">{{ $st->first_name }} {{ $st->last_name }}</div>
                  <div class="text-[10px] text-slate-400 font-mono">{{ $st->register_no }}</div>
                </td>

                @foreach($subjects as $subj)
                  @php $sr = $row['subject_results'][$subj->id] ?? null; @endphp
                  <td class="px-3 py-3 text-center border-l border-slate-100">
                    @if($sr)
                      @if($sr['is_absent'])
                        <div class="flex flex-col items-center">
                          <span class="text-[12px] font-black text-red-500">ABS</span>
                          <span class="text-[10px] px-1.5 py-0.5 rounded-md font-bold bg-red-100 text-red-700 mt-0.5">F</span>
                        </div>
                      @else
                        <div class="flex flex-col items-center gap-0.5">
                          <span class="text-[12px] font-black {{ $sr['color'] === 'blue' ? 'text-blue-700' : 'text-red-700' }}">
                            {{ $sr['obtained_mark'] ?? '-' }}
                          </span>
                          <span class="text-[9px] text-slate-400">/{{ (int)$sr['full_mark'] }}</span>
                          <span class="text-[10px] px-1.5 py-0.5 rounded-md font-bold mt-0.5 {{ $sr['color'] === 'blue' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
                            {{ $sr['grade_name'] }}
                          </span>
                        </div>
                      @endif
                    @else
                      <span class="text-slate-300">–</span>
                    @endif
                  </td>
                @endforeach

                <td class="px-3 py-3 text-center border-l border-slate-100">
                  <span class="font-black text-[13px] {{ $isPassed ? 'text-blue-800' : 'text-red-800' }}">{{ $row['total_obtained'] }}</span>
                  <div class="text-[10px] text-slate-400">/{{ (int)($row['total_max']) }}</div>
                </td>

                <td class="px-3 py-3 text-center">
                  <span class="font-bold {{ $isPassed ? 'text-blue-700' : 'text-red-700' }}">{{ $row['percentage'] }}%</span>
                </td>

                <td class="px-3 py-3 text-center">
                  <span class="font-bold text-slate-700">{{ $row['gpa'] }}</span>
                </td>

                <td class="px-3 py-3 text-center">
                  <span class="px-2 py-0.5 rounded-md text-[11px] font-black {{ $isPassed ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
                    {{ $row['overall_grade'] }}
                  </span>
                </td>

                {{-- Status — BLUE for PASSED, RED for FAILED --}}
                <td class="px-3 py-3 text-center">
                  @if($isPassed)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold tracking-wide uppercase border bg-blue-50 text-blue-700 border-blue-200">
                      <span class="size-1.5 rounded-full bg-blue-500 inline-block"></span>PASSED
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold tracking-wide uppercase border bg-red-50 text-red-700 border-red-200">
                      <span class="size-1.5 rounded-full bg-red-500 inline-block"></span>FAILED
                    </span>
                  @endif
                </td>

                <td class="px-3 py-3 text-center">
                  <span class="inline-flex items-center justify-center size-7 rounded-full {{ $isPassed ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-600' }} text-[11px] font-black">
                    {{ $row['rank'] }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="bg-slate-50 border-t-2 border-slate-300 text-[11px] font-bold text-slate-600">
              <td colspan="3" class="px-3 py-3.5 text-right font-bold text-slate-700">CLASS SUMMARY</td>
              @foreach($subjects as $subj)
                <td class="px-3 py-3.5 text-center border-l border-slate-200 text-slate-400">–</td>
              @endforeach
              <td class="px-3 py-3.5 text-center border-l border-slate-200">
                <div class="text-slate-500">High: {{ $classStats['highest_score'] }}</div>
              </td>
              <td class="px-3 py-3.5 text-center">
                <div class="text-slate-500">Avg: {{ $classStats['average_score'] }}</div>
              </td>
              <td colspan="2" class="px-3 py-3.5 text-center text-slate-400">–</td>
              <td colspan="2" class="px-3 py-3.5 text-center">
                <div class="text-blue-700">{{ $classStats['passed_students'] }} Passed</div>
                <div class="text-red-700">{{ $classStats['failed_students'] }} Failed</div>
                <div class="text-slate-500">{{ $classStats['pass_rate'] }}% Rate</div>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/40 text-xs text-slate-500 flex flex-wrap gap-4 justify-between print:hidden">
        <div class="flex items-center gap-4">
          <span class="flex items-center gap-1.5 font-semibold text-blue-700">
            <span class="size-2.5 rounded-full bg-blue-500 inline-block"></span>
            Passed: {{ $classStats['passed_students'] }} students ({{ $classStats['pass_rate'] }}%)
          </span>
          <span class="flex items-center gap-1.5 font-semibold text-red-700">
            <span class="size-2.5 rounded-full bg-red-500 inline-block"></span>
            Failed: {{ $classStats['failed_students'] }} students
          </span>
        </div>
        <div class="text-slate-400">
          Highest: <strong class="text-slate-600">{{ $classStats['highest_score'] }} pts</strong> &bull;
          Average: <strong class="text-slate-600">{{ $classStats['average_score'] }} pts</strong>
        </div>
      </div>
    </div>

  @elseif(request()->has('class_id') && request()->has('exam_id'))
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-12 text-center">
      <x-heroicon-o-chart-bar class="mx-auto size-14 text-slate-200 mb-3" />
      <p class="font-bold text-slate-600 text-base">{{ translate('no_marks_found') }}</p>
      <p class="text-xs text-slate-400 mt-1">No grades have been entered yet for this exam and class/section combination.</p>
    </div>
  @endif
</div>

<style>
@media print {
  .print\:hidden { display: none !important; }
  thead tr th { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .bg-blue-100 { background-color: #dbeafe !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .bg-red-100  { background-color: #fee2e2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .bg-blue-50  { background-color: #eff6ff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .bg-red-50   { background-color: #fef2f2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .text-blue-700 { color: #1d4ed8 !important; }
  .text-red-700  { color: #b91c1c !important; }
}
</style>

@push('scripts')
<script>
document.getElementById('tab_class')?.addEventListener('change', function(){
  const cid = this.value;
  if (!cid) return;
  fetch('{{ route('ajax.getSectionByClass') }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
    body: JSON.stringify({ class_id: cid })
  }).then(r => r.text()).then(html => {
    const el = document.getElementById('tab_section');
    if (el) { 
      el.innerHTML = html;
      @if($section_id) el.value = '{{ $section_id }}'; @endif
    }
  });
});

document.getElementById('tab_branch')?.addEventListener('change', function(){
  const bid = this.value;
  if (!bid) return;
  fetch('{{ route('ajax.getClassByBranch') }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
    body: JSON.stringify({ branch_id: bid })
  }).then(r => r.text()).then(html => {
    const el = document.getElementById('tab_class');
    if (el) el.innerHTML = html;
  });
});

@if($class_id)
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('tab_class')?.dispatchEvent(new Event('change'));
});
@endif
</script>
@endpush
@endsection
