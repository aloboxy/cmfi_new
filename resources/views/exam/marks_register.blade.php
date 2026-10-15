@extends('layouts.app')
@section('title', translate('mark_entries'))
@section('content')
<div class="space-y-6">
  <!-- Header Breadcrumbs -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
      <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border shadow-xs"><x-heroicon-o-home class="size-4"/></a>
      <span>/</span>
      <span class="font-semibold text-slate-800">{{ translate('exam_master') }}</span>
      <span>/</span>
      <span class="font-semibold text-slate-800">{{ translate('mark_entries') }}</span>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('exam.tabulation', ['class_id' => $class_id ?? '', 'section_id' => $section_id ?? '', 'exam_id' => $exam_id ?? '']) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-4 py-2 text-sm font-semibold transition">
        <x-heroicon-o-table-cells class="size-4 text-blue-600"/>
        {{ translate('master_gradesheet') }}
      </a>
    </div>
  </div>

  <!-- Search Filter Card -->
  <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 font-bold text-slate-800 text-sm">
      <x-heroicon-o-funnel class="size-4 text-blue-600" />
      {{ translate('select_ground') }}
    </div>
    <form method="POST" action="{{ route('exam.marks') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3.5">
      @csrf
      @if(is_superadmin_loggedin())
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase mb-1">{{ translate('branch') }} <span class="text-red-500">*</span></label>
          <select name="branch_id" id="marks_branch" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">{{ translate('select') }}</option>
            @foreach($branches as $b)
              <option value="{{ $b->id }}" @selected($b->id == $branch_id)>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
      @endif

      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">{{ translate('class') }} <span class="text-red-500">*</span></label>
        <select name="class_id" id="marks_class" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="">{{ translate('select') }}</option>
          @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected($c->id == ($class_id ?? ''))>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">{{ translate('section') }} <span class="text-red-500">*</span></label>
        <select name="section_id" id="marks_section" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="">{{ translate('select') }}</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">{{ translate('exam') }} <span class="text-red-500">*</span></label>
        <select name="exam_id" id="marks_exam" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="">{{ translate('select') }}</option>
          @foreach($exams as $e)
            <option value="{{ $e->id }}" @selected($e->id == ($exam_id ?? ''))>{{ $e->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">{{ translate('subject') }} <span class="text-red-500">*</span></label>
        <select name="subject_id" id="marks_subject" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="">{{ translate('select') }}</option>
        </select>
      </div>

      <div class="sm:col-span-2 md:col-span-5 flex justify-end pt-2">
        <button type="submit" name="search" value="1" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 text-sm font-semibold shadow-xs transition">
          <x-heroicon-o-magnifying-glass class="size-4" />
          {{ translate('filter_students') }}
        </button>
      </div>
    </form>
  </div>

  <!-- Student Marks Entry Table -->
  @if(isset($students))
    @php
      $fullMarksTotal = 0;
      if ($hasDistribution) {
        foreach ($distributionItems as $di) {
          $fullMarksTotal += (float) ($di['full_mark'] ?? 100);
        }
      } else {
        $fullMarksTotal = 100;
      }
    @endphp

    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden" x-data="marksEntryHandler()">
      <!-- Table Header & Distribution Notice -->
      <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/40">
        <div>
          <div class="flex items-center gap-2">
            <h3 class="font-bold text-slate-800 text-base">{{ translate('mark_entries') }}</h3>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
              {{ $students->count() }} {{ translate('students') }}
            </span>
          </div>
          <p class="text-xs text-slate-500 mt-1">
            Subject: <strong class="text-slate-700">{{ $subjectInfo->name ?? 'Subject' }}</strong> | 
            Max Total Marks: <strong class="text-slate-700">{{ $fullMarksTotal }} pts</strong>
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          @if($hasDistribution)
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700">
              <x-heroicon-o-chart-pie class="size-4 text-indigo-500" />
              <span>{{ translate('grade_distribution_active') }}:</span>
              @foreach($distributionItems as $did => $dval)
                <span class="bg-white px-2 py-0.5 rounded-md border border-indigo-100 shadow-2xs font-bold text-indigo-900">
                  {{ $dval['name'] ?? get_type_name_by_id('exam_mark_distribution', $did) }} ({{ $dval['full_mark'] ?? 0 }} pts)
                </span>
              @endforeach
            </div>
          @else
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-xs font-medium text-amber-800">
              <span class="size-2 rounded-full bg-amber-500"></span>
              <span>{{ translate('no_distribution') }} &bull; {{ translate('single_full_mark') }} ({{ $fullMarksTotal }} pts)</span>
            </div>
          @endif
        </div>
      </div>

      <form method="POST" action="{{ route('exam.mark_save') }}" id="marksForm" @submit="isSaving = true">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class_id }}">
        <input type="hidden" name="section_id" value="{{ $section_id }}">
        <input type="hidden" name="exam_id" value="{{ $exam_id }}">
        <input type="hidden" name="subject_id" value="{{ $subject_id }}">

        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-slate-100/70 text-xs font-bold uppercase text-slate-600 border-b border-slate-200">
                <th class="px-4 py-3.5 text-center w-12">#</th>
                <th class="px-4 py-3.5 text-left min-w-[180px]">{{ translate('student') }}</th>
                <th class="px-4 py-3.5 text-center w-16">{{ translate('roll') }}</th>
                
                @if($hasDistribution)
                  @foreach($distributionItems as $did => $dval)
                    <th class="px-4 py-3.5 text-center min-w-[120px]">
                      <div>{{ $dval['name'] ?? get_type_name_by_id('exam_mark_distribution', $did) }}</div>
                      <div class="text-[10px] font-normal text-slate-500 lowercase">(max {{ $dval['full_mark'] ?? 100 }})</div>
                    </th>
                  @endforeach
                @else
                  <th class="px-4 py-3.5 text-center min-w-[140px]">
                    <div>{{ translate('mark') }} / {{ translate('points') }}</div>
                    <div class="text-[10px] font-normal text-slate-500 lowercase">(max {{ $fullMarksTotal }})</div>
                  </th>
                @endif

                <th class="px-4 py-3.5 text-center min-w-[100px]">{{ translate('total_score') }}</th>
                <th class="px-4 py-3.5 text-center min-w-[90px]">{{ translate('grade') }}</th>
                <th class="px-4 py-3.5 text-center min-w-[110px]">{{ translate('status') }}</th>
                <th class="px-4 py-3.5 text-center w-24">{{ translate('absent') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @foreach($students as $idx => $s)
                @php 
                  $decoded = json_decode($s->get_mark ?? '{}', true); 
                  if (!is_array($decoded)) $decoded = []; 
                  $isAbsent = ($s->get_abs == 'on' || $s->get_abs == 1); 
                  
                  // Initial calculations
                  $initialTotal = 0;
                  if ($hasDistribution) {
                    foreach ($distributionItems as $did => $dval) {
                      $initialTotal += (float) ($decoded[$did] ?? 0);
                    }
                  } else {
                    $initialTotal = (float) ($decoded[0] ?? ($s->get_mark ?: 0));
                  }
                  $initialGrade = get_grade_info($initialTotal, $fullMarksTotal, $branch_id, $isAbsent);
                @endphp

                <tr class="hover:bg-slate-50/70 transition" x-data="studentRow({{ $idx }}, {{ $fullMarksTotal }}, {{ $isAbsent ? 'true' : 'false' }})">
                  <td class="px-4 py-3 text-center text-slate-400 font-medium">{{ $idx + 1 }}</td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                      <div class="size-8 rounded-full bg-blue-100 text-blue-600 font-bold grid place-items-center text-xs shrink-0">
                        {{ strtoupper(substr($s->first_name, 0, 1)) }}
                      </div>
                      <div>
                        <p class="font-bold text-slate-800 leading-tight">{{ $s->first_name }} {{ $s->last_name }}</p>
                        <p class="text-xs text-slate-400 font-mono">Reg: {{ $s->register_no }}</p>
                      </div>
                    </div>
                    <input type="hidden" name="mark[{{ $idx }}][student_id]" value="{{ $s->student_id }}">
                  </td>
                  <td class="px-4 py-3 text-center font-semibold text-slate-700">{{ $s->roll }}</td>

                  <!-- Input Fields for Marks -->
                  @if($hasDistribution)
                    @foreach($distributionItems as $did => $dval)
                      @php $maxD = (float) ($dval['full_mark'] ?? 100); @endphp
                      <td class="px-4 py-3 text-center">
                        <input type="number" step="0.1" min="0" max="{{ $maxD }}" 
                          name="mark[{{ $idx }}][assessment][{{ $did }}]" 
                          value="{{ $decoded[$did] ?? '' }}" 
                          :disabled="absent"
                          @input="recalculate()"
                          data-max="{{ $maxD }}"
                          class="mark-input w-24 rounded-xl border border-slate-200 px-3 py-1.5 text-center text-sm font-bold text-slate-800 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none disabled:bg-slate-100 disabled:text-slate-400 transition"
                          placeholder="0">
                      </td>
                    @endforeach
                  @else
                    <td class="px-4 py-3 text-center">
                      <input type="number" step="0.1" min="0" max="{{ $fullMarksTotal }}" 
                        name="mark[{{ $idx }}][assessment][0]" 
                        value="{{ $decoded[0] ?? ($s->get_mark !== '' ? $s->get_mark : '') }}" 
                        :disabled="absent"
                        @input="recalculate()"
                        data-max="{{ $fullMarksTotal }}"
                        class="mark-input w-28 rounded-xl border border-slate-200 px-3 py-1.5 text-center text-sm font-bold text-slate-800 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none disabled:bg-slate-100 disabled:text-slate-400 transition"
                        placeholder="0">
                    </td>
                  @endif

                  <!-- Dynamic Total Score -->
                  <td class="px-4 py-3 text-center">
                    <span class="font-extrabold text-sm text-slate-800" x-text="totalScore + ' / ' + maxTotal">
                      {{ $initialTotal }} / {{ $fullMarksTotal }}
                    </span>
                    <div class="text-[11px] text-slate-500 font-medium" x-text="percentage + '%'">
                      {{ $initialGrade['percentage'] }}%
                    </div>
                  </td>

                  <!-- Dynamic Grade Badge -->
                  <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center justify-center min-w-[40px] px-2 py-0.5 rounded-md text-xs font-black"
                      :class="isPass ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700'"
                      x-text="grade">
                      {{ $initialGrade['name'] }}
                    </span>
                  </td>

                  <!-- Dynamic Pass / Fail Status Badge (BLUE for Pass, RED for Fail) -->
                  <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold tracking-wide uppercase shadow-2xs border"
                      :class="isPass ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-red-50 text-red-700 border-red-200'"
                      x-text="statusText">
                      @if($initialGrade['is_pass'])
                        PASSED
                      @else
                        FAILED
                      @endif
                    </span>
                  </td>

                  <!-- Absent Checkbox -->
                  <td class="px-4 py-3 text-center">
                    <label class="inline-flex items-center cursor-pointer">
                      <input type="checkbox" name="mark[{{ $idx }}][absent]" value="on" 
                        x-model="absent" @change="recalculate()"
                        class="size-5 rounded-md text-red-600 border-slate-300 focus:ring-red-500">
                    </label>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="flex items-center gap-4 text-xs font-medium text-slate-500">
            <span class="flex items-center gap-1.5">
              <span class="size-3 rounded-full bg-blue-600"></span>
              <strong>Passed Grade:</strong> Blue
            </span>
            <span class="flex items-center gap-1.5">
              <span class="size-3 rounded-full bg-red-600"></span>
              <strong>Failed Grade:</strong> Red
            </span>
          </div>

          <div class="flex items-center gap-2">
            <button type="submit" :disabled="isSaving" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-2.5 text-sm font-bold shadow-xs transition">
              <x-heroicon-o-check-circle class="size-5" />
              <span x-text="isSaving ? 'Saving Marks...' : '{{ translate('save_all_marks') }}'"></span>
            </button>
          </div>
        </div>
      </form>
    </div>
  @endif
</div>

@push('scripts')
<script>
// Dynamic Dropdown Fetching
document.getElementById('marks_class')?.addEventListener('change', function(){
  const cid = this.value;
  if (!cid) return;
  fetch('{{ route('ajax.getSectionByClass') }}', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
    body: JSON.stringify({class_id: cid})
  }).then(r=>r.text()).then(html=>{
    const el = document.getElementById('marks_section');
    if (el) el.innerHTML = html;
    @if(isset($section_id)) el.value = '{{ $section_id }}'; @endif
  });

  fetch('{{ route('ajax.getSubjectByClass') }}', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
    body: JSON.stringify({class_id: cid})
  }).then(r=>r.text()).then(html=>{
    const el = document.getElementById('marks_subject');
    if (el) el.innerHTML = html;
    @if(isset($subject_id)) el.value = '{{ $subject_id }}'; @endif
  });
});

document.getElementById('marks_branch')?.addEventListener('change', function(){
  const bid = this.value;
  if (!bid) return;
  fetch('{{ route('ajax.getClassByBranch') }}', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
    body: JSON.stringify({branch_id: bid})
  }).then(r=>r.text()).then(html=>{
    const el = document.getElementById('marks_class');
    if (el) el.innerHTML = html;
  });
});

@if(isset($class_id) && $class_id)
  document.getElementById('marks_class')?.dispatchEvent(new Event('change'));
@endif

// Reactive Marks Form Engine
function marksEntryHandler() {
  return {
    isSaving: false,
  };
}

function studentRow(rowIdx, maxTotal, isAbsentInitial) {
  return {
    rowIdx: rowIdx,
    maxTotal: maxTotal,
    absent: isAbsentInitial,
    totalScore: 0,
    percentage: 0,
    grade: 'F',
    isPass: false,
    statusText: 'FAILED',

    init() {
      this.recalculate();
    },

    recalculate() {
      if (this.absent) {
        this.totalScore = 0;
        this.percentage = 0;
        this.grade = 'ABS';
        this.isPass = false;
        this.statusText = 'ABSENT (FAIL)';
        return;
      }

      let sum = 0;
      const inputs = this.$el.querySelectorAll('.mark-input');
      inputs.forEach(inp => {
        const val = parseFloat(inp.value) || 0;
        const max = parseFloat(inp.getAttribute('data-max')) || maxTotal;
        if (val > max) {
          inp.classList.add('border-red-500', 'bg-red-50');
        } else {
          inp.classList.remove('border-red-500', 'bg-red-50');
        }
        sum += val;
      });

      this.totalScore = Math.round(sum * 10) / 10;
      this.percentage = this.maxTotal > 0 ? Math.round((this.totalScore / this.maxTotal) * 1000) / 10 : 0;

      // Grade calculation (Pass >= 50% in Blue, Fail < 50% in Red)
      if (this.percentage >= 90) {
        this.grade = 'A+';
        this.isPass = true;
      } else if (this.percentage >= 80) {
        this.grade = 'A';
        this.isPass = true;
      } else if (this.percentage >= 70) {
        this.grade = 'B';
        this.isPass = true;
      } else if (this.percentage >= 60) {
        this.grade = 'C';
        this.isPass = true;
      } else if (this.percentage >= 50) {
        this.grade = 'D';
        this.isPass = true;
      } else {
        this.grade = 'F';
        this.isPass = false;
      }

      this.statusText = this.isPass ? 'PASSED' : 'FAILED';
    }
  };
}
</script>
@endpush
@endsection

