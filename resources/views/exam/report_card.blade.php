@extends('layouts.app')

@section('title', translate('report_card'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
  <div class="flex items-center gap-3">
    <div class="grid size-10 place-items-center rounded-xl bg-[#2563eb] text-white"><x-heroicon-o-document-text class="size-5" /></div>
    <div>
      <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ translate('report_card') }}</h1>
      <p class="text-sm text-slate-500">{{ translate('generate_report_card_bulk_individual') }}</p>
    </div>
  </div>

  {{-- Filters --}}
  <form id="filterForm" class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
      @if(is_superadmin_loggedin())
        <div>
          <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Branch</label>
          <select id="filter_branch" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm">
            <option value="">{{ translate('select') }}</option>
            @foreach($branches as $b)<option value="{{ $b->id }}" @selected($branch_id==$b->id)>{{ $b->name }}</option>@endforeach
          </select>
        </div>
      @endif
      <div>
        <label class="text-xs font-semibold">Session</label>
        <select id="filter_session" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm">
          @foreach($sessions as $s)<option value="{{ $s->id }}" @selected($s->id==get_session_id())>{{ $s->school_year }}</option>@endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold">Class</label>
        <select id="filter_class" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm">
          <option value="">{{ translate('select') }}</option>
          @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold">Section</label>
        <select id="filter_section" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm"><option value="">{{ translate('select') }}</option></select>
      </div>
      <div>
        <label class="text-xs font-semibold">Exam</label>
        <select id="filter_exam" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm">
          <option value="">{{ translate('select') }}</option>
          @foreach($exams as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach
        </select>
      </div>
    </div>
    <div class="flex flex-wrap items-center gap-4 pt-2">
      <label class="text-xs font-semibold">Template Type</label>
      <select id="template_type" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
        <option value="semester">Semester</option>
        <option value="period">Period</option>
        <option value="end_of_year">End of Year</option>
      </select>
      <label class="flex items-center gap-1.5 text-xs"><input type="checkbox" id="opt_grade_scale" checked class="rounded"> Grade Scale</label>
      <label class="flex items-center gap-1.5 text-xs"><input type="checkbox" id="opt_attendance" checked class="rounded"> Attendance</label>
      <button type="button" id="btnSearch" class="ml-auto rounded-xl bg-[#2563eb] px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">Search Students</button>
    </div>
  </form>

  {{-- Bulk / Individual generation --}}
  <form id="printForm" method="POST" action="{{ route('exam.report_card.print') }}" target="_blank" class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 hidden">
    @csrf
    <input type="hidden" name="branch_id" id="print_branch">
    <input type="hidden" name="session_id" id="print_session">
    <input type="hidden" name="exam_id" id="print_exam">
    <input type="hidden" name="template_type" id="print_template_type">
    <input type="hidden" name="grade_scale" id="print_grade_scale" value="1">
    <input type="hidden" name="attendance" id="print_attendance" value="1">

    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold">Select Students — Bulk or Individual</h3>
      <div class="flex items-center gap-2">
        <label class="flex items-center gap-1.5 text-xs"><input type="checkbox" id="checkAll" class="rounded"> Select All</label>
        <span class="text-xs text-slate-500"><span id="selectedCount">0</span> selected</span>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="bg-slate-50 dark:bg-slate-800 text-left"><th class="px-3 py-2 w-8"><span class="sr-only">Check</span></th><th class="px-3 py-2">Student</th><th class="px-3 py-2">Register No</th><th class="px-3 py-2">Roll</th><th class="px-3 py-2">Class/Section</th><th class="px-3 py-2 w-[260px]">Remarks</th></tr></thead>
        <tbody id="studentTbody" class="divide-y divide-slate-100 dark:divide-slate-800">
          <tr><td colspan="6" class="px-3 py-8 text-center text-slate-400">Search to load students</td></tr>
        </tbody>
      </table>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
      <button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-bold text-white shadow hover:opacity-90 flex items-center gap-2">
        <x-heroicon-o-printer class="size-4" /> Generate Report Cards (Bulk)
      </button>
      <button type="button" id="btnIndividual" class="rounded-xl border border-slate-200 dark:border-slate-700 px-5 py-2.5 text-sm font-semibold hover:bg-slate-50">Generate Individual (first selected)</button>
      <span class="text-xs text-slate-400 self-center">Opens print-ready HTML with page breaks — use browser Print → Save as PDF</span>
    </div>
  </form>

  <div class="rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 p-4 text-sm">
    <p class="font-semibold text-amber-800 dark:text-amber-300">How it works</p>
    <p class="text-amber-700 dark:text-amber-400 mt-1">Uses your <a href="{{ route('settings.report_card_template') }}" class="underline">Report Card Template</a> — Classic order/titles, Canvas (Fabric) blocks, and GrapeJS design (priority: GrapeJS → Canvas → Classic). Bulk prints with page breaks; individual prints one card.</p>
  </div>
</div>
@endsection

@push('scripts')
<script>
const branchEl = document.getElementById('filter_branch');
const classEl = document.getElementById('filter_class');
const sectionEl = document.getElementById('filter_section');
const examEl = document.getElementById('filter_exam');
const sessionEl = document.getElementById('filter_session');

async function fetchJson(url, opts={}) {
  const res = await fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}, credentials:'same-origin', ...opts});
  const txt = await res.text();
  try { return JSON.parse(txt); } catch(e){ return txt; }
}

// Branch -> Class -> Section cascade
branchEl?.addEventListener('change', async ()=>{
  const bid = branchEl.value;
  if(!bid) return;
  const html = await fetch(`/ajax/getClassByBranch?branch_id=${bid}`).then(r=>r.text());
  classEl.innerHTML = '<option value="">{{ translate("select") }}</option>' + html;
  sectionEl.innerHTML = '<option value="">{{ translate("select") }}</option>';
  const examsHtml = await fetch(`/ajax/getExamByBranch?branch_id=${bid}`).then(r=>r.text()).catch(()=> '');
  if(examsHtml) examEl.innerHTML = '<option value="">{{ translate("select") }}</option>' + examsHtml;
});

classEl?.addEventListener('change', async ()=>{
  const cid = classEl.value;
  if(!cid) { sectionEl.innerHTML='<option value="">{{ translate("select") }}</option>'; return; }
  const html = await fetch(`/ajax/getSectionByClass?class_id=${cid}`).then(r=>r.text());
  sectionEl.innerHTML = '<option value="">{{ translate("select") }}</option>' + html;
});

document.getElementById('btnSearch').addEventListener('click', async ()=>{
  const branch_id = branchEl ? branchEl.value : '{{ $branch_id }}';
  const session_id = sessionEl.value;
  const class_id = classEl.value;
  const section_id = sectionEl.value;
  const exam_id = examEl.value;
  if(!class_id || !section_id || !exam_id){ alert('Select Class, Section and Exam'); return; }
  const tbody = document.getElementById('studentTbody');
  tbody.innerHTML = '<tr><td colspan="6" class="px-3 py-8 text-center">Loading...</td></tr>';
  // Use existing endpoint: get student list for report card — fallback to enroll query via custom endpoint
  // Try to fetch students via exam report card helper: POST to a small helper that returns student list
  // For now, use the same logic as marks page: students in enroll
  const res = await fetch(`/exam/report-card/students?`+new URLSearchParams({branch_id, session_id, class_id, section_id, exam_id}), {headers:{'X-Requested-With':'XMLHttpRequest'}});
  let data;
  try { data = await res.json(); } catch(e){ data = []; }
  // Fallback: if endpoint not yet implemented, show mock helper via direct DB? Use fetch to a new endpoint we will create
  if(!Array.isArray(data) || !data.length){
    // Try legacy ajax: getStudentByClass? Use exam model via custom route
    // As fallback, show empty with message
    tbody.innerHTML = '<tr><td colspan="6" class="px-3 py-6 text-center text-slate-500">No students found for this filter. Ensure students are enrolled and marks exist.</td></tr>';
    document.getElementById('printForm').classList.remove('hidden');
    return;
  }
  tbody.innerHTML = '';
  data.forEach(s=>{
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-slate-50 dark:hover:bg-slate-800';
    tr.innerHTML = `<td class="px-3 py-2"><input type="checkbox" name="student_id[]" value="${s.student_id||s.id}" class="chk rowChk rounded"></td>
      <td class="px-3 py-2 font-medium">${s.first_name||s.name} ${s.last_name||''}</td>
      <td class="px-3 py-2">${s.register_no||''}</td>
      <td class="px-3 py-2">${s.roll||''}</td>
      <td class="px-3 py-2">${s.class_name||''} ${s.section_name? '('+s.section_name+')':''}</td>
      <td class="px-3 py-2"><input type="text" name="remarks[${s.student_id||s.id}]" placeholder="Remarks (optional)" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-2 py-1.5 text-xs"></td>`;
    tbody.appendChild(tr);
  });
  document.getElementById('printForm').classList.remove('hidden');
  updatePrintHidden();
});

function updatePrintHidden(){
  const branch_id = branchEl ? branchEl.value : '{{ $branch_id }}';
  document.getElementById('print_branch').value = branch_id;
  document.getElementById('print_session').value = sessionEl.value;
  document.getElementById('print_exam').value = examEl.value;
  document.getElementById('print_template_type').value = document.getElementById('template_type').value;
  document.getElementById('print_grade_scale').value = document.getElementById('opt_grade_scale').checked ? 1 : 0;
  document.getElementById('print_attendance').value = document.getElementById('opt_attendance').checked ? 1 : 0;
  const count = document.querySelectorAll('.rowChk:checked').length;
  document.getElementById('selectedCount').textContent = count;
}
document.getElementById('template_type')?.addEventListener('change', updatePrintHidden);
document.getElementById('opt_grade_scale')?.addEventListener('change', updatePrintHidden);
document.getElementById('opt_attendance')?.addEventListener('change', updatePrintHidden);
document.addEventListener('change', e=>{ if(e.target.classList.contains('rowChk')) updatePrintHidden(); });
document.getElementById('checkAll')?.addEventListener('change', e=>{
  document.querySelectorAll('.rowChk').forEach(c=> c.checked = e.target.checked);
  updatePrintHidden();
});
document.getElementById('btnIndividual')?.addEventListener('click', ()=>{
  const first = document.querySelector('.rowChk:checked');
  if(!first){ alert('Select at least one student'); return; }
  // Uncheck others, keep only first
  const val = first.value;
  document.querySelectorAll('.rowChk').forEach(c=> c.checked = (c.value===val));
  updatePrintHidden();
  document.getElementById('printForm').submit();
});
</script>
@endpush
