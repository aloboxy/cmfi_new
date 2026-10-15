@extends('layouts.app')

@section('title', translate('report_card_template'))

@push('styles')
<style>
  .designer-container{display:flex;gap:24px;background:transparent;padding:0;margin-top:15px}
  .designer-toolbox{width:280px;background:rgba(255,255,255,.7);backdrop-filter:blur(20px);padding:20px;border-radius:16px;border:1px solid rgba(255,255,255,.4);box-shadow:0 10px 40px rgba(0,0,0,.04);flex-shrink:0;display:flex;flex-direction:column;gap:10px}
  .dark .designer-toolbox{background:rgba(30,41,59,.6);border:1px solid rgba(255,255,255,.08)}
  .designer-canvas-wrapper{flex-grow:1;background:#f1f5f9;background-image:linear-gradient(rgba(148,163,184,.1) 1px,transparent 1px),linear-gradient(90deg,rgba(148,163,184,.1) 1px,transparent 1px);background-size:20px 20px;padding:40px 20px;border-radius:16px;display:flex;flex-direction:column;align-items:center;overflow:auto;box-shadow:inset 0 2px 10px rgba(0,0,0,.02);border:1px solid rgba(0,0,0,.03)}
  .dark .designer-canvas-wrapper{background:#0f172a;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px)}
  .canvas-container{box-shadow:0 25px 50px -12px rgba(0,0,0,.25);background:#fff;margin:0 auto;border-radius:4px;transition:transform .3s ease}
  .toolbox-item{display:flex;align-items:center;gap:12px;padding:12px 16px;background:#fff;border:1px solid rgba(0,0,0,.05);border-radius:10px;cursor:grab;font-weight:500;font-size:.9rem;color:#475569;transition:all .25s;box-shadow:0 2px 5px rgba(0,0,0,.02)}
  .dark .toolbox-item{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);color:#cbd5e1}
  .toolbox-item:hover{transform:translateY(-2px) scale(1.02);box-shadow:0 8px 20px rgba(29,78,216,.12);border-color:#1d4ed8;color:#1d4ed8}
  .toolbox-item svg{color:#3b82f6;width:16px;height:16px;flex-shrink:0}
  .designer-toolbar{width:100%;display:flex;justify-content:space-between;align-items:center;margin-bottom:0;padding:15px 24px;background:rgba(255,255,255,.85);backdrop-filter:blur(12px);border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.03);border:1px solid rgba(255,255,255,.5)}
  .dark .designer-toolbar{background:rgba(30,41,59,.85);border:1px solid rgba(255,255,255,.08)}
  .toolbox-heading{font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;font-weight:700;margin-top:10px;margin-bottom:5px;padding-left:5px}
  #gjs{border:1px solid #e2e8f0;border-radius:12px;overflow:hidden}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/grapesjs@0.21.10/dist/css/grapes.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css">
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ tab: 'classic' }" x-cloak>

  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
        <x-heroicon-o-document-text class="size-6 text-[#2563eb]" /> {{ translate('report_card_template') }}
      </h1>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ translate('design_and_configure_report_card_layout') }}</p>
    </div>
    @if($branches->count() > 1)
      <select onchange="location.href='{{ route('settings.report_card_template') }}?branch_id='+this.value" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
        @foreach($branches as $b)
          <option value="{{ $b->id }}" @selected($branch_id==$b->id)>{{ $b->name }}</option>
        @endforeach
      </select>
    @endif
  </div>

  {{-- Tabs --}}
  <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
    <div class="flex overflow-x-auto border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
      <button @click="tab='classic'" :class="tab==='classic' ? 'bg-white dark:bg-slate-900 text-[#2563eb] border-b-2 border-[#2563eb]' : 'text-slate-500'" class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap"> {{ translate('classic_builder') }}</button>
      <button @click="tab='canvas'" :class="tab==='canvas' ? 'bg-white dark:bg-slate-900 text-[#2563eb] border-b-2 border-[#2563eb]' : 'text-slate-500'" class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap"><i class="fas fa-paint-brush mr-1"></i> Canvas Designer</button>
      <button @click="tab='grapejs'" :class="tab==='grapejs' ? 'bg-white dark:bg-slate-900 text-[#2563eb] border-b-2 border-[#2563eb]' : 'text-slate-500'" class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap"><i class="fas fa-layer-group mr-1"></i> GrapeJS</button>
      <button @click="tab='preview'" :class="tab==='preview' ? 'bg-white dark:bg-slate-900 text-[#2563eb] border-b-2 border-[#2563eb]' : 'text-slate-500'" class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap">Preview</button>
    </div>

    {{-- CLASSIC BUILDER (ported from report_card_template.php) --}}
    <div x-show="tab==='classic'" class="p-6">
      @php
        $front_settings = $template->front_layout_settings ?? [];
        if (is_string($front_settings)) $front_settings = json_decode($front_settings, true) ?: [];
        if (empty($front_settings)) $front_settings = \App\Models\ReportCardTemplate::defaultLayout();
        $order_values = [];
        if (!empty($front_settings['order']) && is_array($front_settings['order'])) {
          foreach ($front_settings['order'] as $i=>$sec) $order_values[$sec] = $i+1;
        }
        $titles = $front_settings['titles'] ?? [];
        $sections = [
          'student_info' => 'Student Information',
          'subjects_table' => 'Subjects / Marks Table',
          'attendance_summary' => 'Attendance Summary',
          'gpa' => 'GPA',
          'class_rank' => 'Class Rank',
          'grade_scale_legend' => 'Grading Scale Legend',
          'signature_lines' => 'Signature Lines',
        ];
      @endphp
      <form method="POST" action="{{ route('settings.report_card_template.store', ['branch_id'=>$branch_id]) }}" id="report_card_template_form" class="space-y-6">
        @csrf
        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <h3 class="font-semibold text-slate-900 dark:text-white mb-3">Front Page Controls</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach(['show_gpa'=>'Show GPA','show_class_rank'=>'Show Class Rank','show_attendance_summary'=>'Show Attendance Summary','show_vpa_signature_line'=>'Show VPA Signature Line','show_principal_signature_line'=>'Show Principal Signature Line','grading_scale_legend'=>'Show Grading Scale Legend'] as $key=>$label)
              <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="{{ $key }}" value="1" @checked(!empty($front_settings[$key])) class="rounded border-slate-300 text-[#2563eb] focus:ring-[#2563eb]">
                {{ $label }}
              </label>
            @endforeach
          </div>
        </div>

        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <h3 class="font-semibold text-slate-900 dark:text-white mb-2">Section Ordering</h3>
          <p class="text-xs text-slate-500 mb-3">Drag and drop sections to reorder the front page.</p>
          <ul id="rc-sections" class="space-y-2">
            @foreach($sections as $key=>$label)
              <li class="flex items-center justify-between rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 cursor-move" data-key="{{ $key }}">
                <span class="flex items-center gap-2 text-sm"><i class="fas fa-arrows-alt-v text-slate-400"></i> {{ $label }}</span>
                <input type="hidden" name="order_{{ $key }}" value="{{ $order_values[$key] ?? '' }}">
              </li>
            @endforeach
          </ul>
        </div>

        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <h3 class="font-semibold text-slate-900 dark:text-white mb-3">Section Titles</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($sections as $key=>$label)
              <div>
                <label class="text-xs font-medium text-slate-500">{{ $label }} Title</label>
                <input type="text" name="title_{{ $key }}" value="{{ old('title_'.$key, $titles[$key] ?? $label) }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] outline-none">
              </div>
            @endforeach
          </div>
        </div>

        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <label class="flex items-center gap-2 text-sm font-semibold">
            <input type="checkbox" name="enable_back_page" value="1" @checked(!empty($template->enable_back_page)) class="rounded border-slate-300 text-[#2563eb]"> Enable Back Page Content for Two-Sided Printing
          </label>
          <div class="mt-3">
            <label class="text-xs font-medium text-slate-500">Back Page Content</label>
            <textarea name="back_page_content" id="back_page_content" class="summernote w-full rounded-xl border border-slate-200 dark:border-slate-700 mt-1">{{ old('back_page_content', $template->back_page_content) }}</textarea>
            <p class="text-xs text-slate-400 mt-1">School policies, promotion criteria, or announcements printed on the back.</p>
          </div>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition">
            <i class="fas fa-save mr-2"></i> Save Template
          </button>
        </div>
      </form>
    </div>

    {{-- CANVAS DESIGNER (Fabric.js) — fixed toolbar with Heroicons --}}
    <div x-show="tab==='canvas'" class="p-0">
      <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
        <div class="flex flex-wrap items-center gap-2">
          <div class="inline-flex rounded-xl border border-slate-200 dark:border-slate-600 overflow-hidden bg-white dark:bg-slate-800">
            <button type="button" id="btn-front-page" class="px-4 py-2 text-xs font-bold bg-[#2563eb] text-white">Front Page</button>
            <button type="button" id="btn-back-page" class="px-4 py-2 text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50">Back Page</button>
          </div>
          <select id="template-type-selector" class="rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
            <option value="semester">Semester Template</option>
            <option value="period">Period Template</option>
            <option value="end_of_year">End of Year Template</option>
          </select>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <div class="inline-flex rounded-xl border border-slate-200 dark:border-slate-600 overflow-hidden bg-white dark:bg-slate-800">
            <button type="button" id="btn-portrait" class="px-3 py-2 text-xs font-bold bg-[#2563eb] text-white flex items-center gap-1.5" title="Portrait"><x-heroicon-o-device-phone-mobile class="size-4" /> Portrait</button>
            <button type="button" id="btn-landscape" class="px-3 py-2 text-xs font-bold bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 flex items-center gap-1.5" title="Landscape"><x-heroicon-o-computer-desktop class="size-4" /> Landscape</button>
          </div>
          <div class="flex items-center gap-1 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 p-1">
            <button type="button" id="btn-zoom-out" class="grid size-7 place-items-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300"><x-heroicon-o-magnifying-glass-minus class="size-4" /></button>
            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 min-w-[40px] text-center" id="btn-zoom-reset">100%</span>
            <button type="button" id="btn-zoom-in" class="grid size-7 place-items-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300"><x-heroicon-o-magnifying-glass-plus class="size-4" /></button>
          </div>
          <button type="button" id="btn-delete" class="rounded-xl bg-red-600 px-3.5 py-2 text-xs font-bold text-white disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1.5" disabled><x-heroicon-o-trash class="size-4" /> Delete</button>
          <button type="button" id="btn-clear" class="rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50">Clear</button>
          <button type="button" id="btn-save" class="rounded-xl bg-[#2563eb] px-5 py-2 text-xs font-bold text-white shadow hover:opacity-90 flex items-center gap-1.5"><x-heroicon-o-check class="size-4" /> Save All Designs</button>
        </div>
      </div>
      <div id="text-properties-toolbar" class="hidden flex-wrap items-center gap-3 px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
        <span class="text-xs font-bold tracking-wide text-slate-500 uppercase">Text Properties</span>
        <select id="text-font-family" class="rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-2.5 py-1.5 text-xs text-slate-700 dark:text-slate-200">
          <option>Arial</option><option>Times New Roman</option><option>Courier New</option><option>Georgia</option><option>Verdana</option>
        </select>
        <input type="number" id="text-font-size" value="20" min="8" max="120" class="w-20 rounded-xl border border-slate-200 dark:border-slate-600 px-2.5 py-1.5 text-xs text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800">
        <div class="flex gap-1">
          <button type="button" id="btn-text-bold" class="grid size-8 place-items-center rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200"><span class="text-xs font-black">B</span></button>
          <button type="button" id="btn-text-italic" class="grid size-8 place-items-center rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200"><span class="text-xs italic">I</span></button>
          <button type="button" id="btn-text-underline" class="grid size-8 place-items-center rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200"><span class="text-xs underline">U</span></button>
        </div>
        <input type="color" id="text-color" value="#000000" class="h-8 w-10 rounded-lg border border-slate-200 dark:border-slate-600 p-0.5 bg-white dark:bg-slate-800">
      </div>
      <div class="designer-container">
        <div class="designer-toolbox">
          <div class="toolbox-heading">Components</div>
          <div class="toolbox-item" data-type="text" data-text="[Student Name]"><x-heroicon-o-user class="size-4" /> Student Name</div>
          <div class="toolbox-item" data-type="text" data-text="[Class & Section]"><x-heroicon-o-users class="size-4" /> Class & Section</div>
          <div class="toolbox-item" data-type="text" data-text="[Roll Number]"><x-heroicon-o-hashtag class="size-4" /> Roll Number</div>
          <div class="toolbox-item" data-type="text" data-text="[Academic Year]"><x-heroicon-o-calendar-days class="size-4" /> Academic Year</div>
          <hr class="border-slate-200 dark:border-slate-700 my-2">
          <div class="toolbox-heading">Academic</div>
          <div class="toolbox-item" data-type="grades_table" data-label="Grades Table"><x-heroicon-o-table-cells class="size-4" /> Grades Table</div>
          <div class="toolbox-item" data-type="attendance_table" data-label="Attendance Table"><x-heroicon-o-clipboard-document-check class="size-4" /> Attendance Table</div>
          <div class="toolbox-item" data-type="text" data-text="[GPA / Final Grade]"><x-heroicon-o-star class="size-4" /> GPA</div>
          <hr class="border-slate-200 dark:border-slate-700 my-2">
          <div class="toolbox-heading">Elements</div>
          <div class="toolbox-item" data-type="logo"><x-heroicon-o-photo class="size-4" /> School Logo</div>
          <div class="toolbox-item" data-type="signature" data-text="Principal Signature"><x-heroicon-o-pencil-square class="size-4" /> Principal Signature</div>
          <div class="toolbox-item" data-type="text-custom"><x-heroicon-o-pencil class="size-4" /> Custom Text</div>
        </div>
        <div class="designer-canvas-wrapper">
          <canvas id="gradeSheetCanvas" width="794" height="1123"></canvas>
        </div>
      </div>
    </div>

    {{-- GRAPEJS --}}
    <div x-show="tab==='grapejs'" class="p-4">
      <div class="mb-3 flex items-center justify-between">
        <p class="text-sm text-slate-500">Drag & drop builder — saves to same template (branch {{ $branch_id }}).</p>
        <button type="button" id="gjs-save" class="rounded-xl bg-[#2563eb] px-4 py-2 text-xs font-semibold text-white">Save GrapeJS</button>
      </div>
      <div id="gjs" style="min-height:560px;"></div>
      <textarea id="gjs-html" class="hidden">{{ $template->grapejs_html ?? '' }}</textarea>
    </div>

    {{-- PREVIEW — live updating from Classic + Canvas + GrapeJS --}}
    <div x-show="tab==='preview'" class="p-6 space-y-6">
      <div class="flex flex-wrap justify-between items-center gap-3">
        <h3 class="font-semibold flex items-center gap-2"><span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Preview (mock data) <span class="text-xs font-normal text-slate-400">updates as you edit</span></h3>
        <a href="{{ route('settings.report_card_template.preview', ['branchId'=>$branch_id]) }}" target="_blank" class="rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-1.5 text-xs">Open printable preview</a>
      </div>
      <div id="live-preview-classic" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6">
        @include('settings.report_card_preview_inline', ['template'=>$template])
      </div>
      <div id="live-preview-canvas-wrap" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 hidden">
        <h4 class="text-xs font-bold tracking-widest uppercase text-slate-500 mb-3">Canvas Snapshot</h4>
        <img id="live-preview-canvas-img" class="w-full max-w-2xl mx-auto border border-slate-200 dark:border-slate-700 rounded-lg" alt="Canvas preview">
        <p class="text-xs text-slate-400 mt-2 text-center">Live render of the Fabric canvas (front page, semester)</p>
      </div>
      <div id="live-preview-grapejs-wrap" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 hidden">
        <h4 class="text-xs font-bold tracking-widest uppercase text-slate-500 mb-3">GrapeJS Snapshot</h4>
        <div id="live-preview-grapejs" class="prose prose-sm max-w-none"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script src="https://unpkg.com/grapesjs@0.21.10/dist/grapes.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Sortable classic
  if (window.$ && $('#rc-sections').length) {
    $('#rc-sections').sortable({ placeholder:'ui-state-highlight' });
    $('#report_card_template_form').on('submit', function(){
      $('#rc-sections li').each(function(i){ $(this).find('input').val(i+1); });
    });
  }
  // Summernote
  if (window.$ && $('.summernote').length) $('.summernote').summernote({height:180});

  // GrapeJS init (lazy when tab shown)
  let gjsEditor = null;
  function initGjs(){
    if (gjsEditor) return;
    gjsEditor = grapesjs.init({
      container:'#gjs', height:'560px', storageManager:false,
      fromElement:false,
      components: document.getElementById('gjs-html')?.value || '<div style="padding:20px"><h2>Report Card</h2><p>[Student Name] — [Class]</p><div data-type="grades_table">[Grades Table]</div></div>',
      style: '',
      panels:{defaults:[]},
    });
  }
  document.querySelector('[x-data]')?.addEventListener('click', ()=> setTimeout(()=>{ if(document.getElementById('gjs')?.offsetParent) initGjs(); },100));
  document.getElementById('gjs-save')?.addEventListener('click', function(){
    if(!gjsEditor) initGjs();
    const html = gjsEditor.getHtml();
    const css = gjsEditor.getCss();
    const json = JSON.stringify(gjsEditor.getProjectData());
    const btn=this, orig=btn.innerHTML; btn.innerHTML='Saving...'; btn.disabled=true;
    fetch('{{ route('settings.report_card_template.grapejs') }}', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
      credentials:'same-origin',
      body: JSON.stringify({branch_id: {{ $branch_id }}, design_data: JSON.stringify({html,css, json}), grapejs_html: html, grapejs_css: css})
    }).then(async r=>{
      const txt=await r.text(); let j; try{j=JSON.parse(txt);}catch(e){throw new Error('Server error ('+r.status+'): '+txt.substring(0,300));}
      if(!r.ok) throw new Error(j.message||'HTTP '+r.status);
      return j;
    }).then(j=>{
      btn.innerHTML=orig; btn.disabled=false;
      alert(j.message || 'Saved');
    }).catch(e=>{ btn.innerHTML=orig; btn.disabled=false; alert('Save failed: '+e.message); console.error(e); });
  });

  // Fabric canvas — reuse logic from legacy with branchId
  const branchId = {{ $branch_id }};
  const canvasEl = document.getElementById('gradeSheetCanvas');
  if (!canvasEl) return;
  const canvas = new fabric.Canvas('gradeSheetCanvas', {backgroundColor:'#ffffff'});
  @php
    $front_raw = $template->front_canvas_layout ?? null;
    $back_raw = $template->back_canvas_layout ?? null;
    $front_settings_orientation = $template->front_layout_settings['orientation'] ?? 'portrait';
  @endphp
  let currentPage='front', currentTemplateType='semester', currentOrientation='{{ $front_settings_orientation }}';
  let pageData={semester:{front:null,back:null},period:{front:null,back:null},end_of_year:{front:null,back:null}};
  let frontRaw = @json($front_raw);
  let backRaw = @json($back_raw);
  function parseLayouts(raw, side){
    if(!raw) return;
    let parsed = typeof raw==='string'? JSON.parse(raw):raw;
    if(parsed && (parsed.semester||parsed.period||parsed.end_of_year)){
      if(parsed.semester) pageData.semester[side]=parsed.semester;
      if(parsed.period) pageData.period[side]=parsed.period;
      if(parsed.end_of_year) pageData.end_of_year[side]=parsed.end_of_year;
    } else if(parsed && Object.keys(parsed).length){ pageData.semester[side]=parsed; }
  }
  parseLayouts(frontRaw,'front'); parseLayouts(backRaw,'back');
  const dimensions={portrait:{width:794,height:1123},landscape:{width:1123,height:794}};
  function applyOrientation(o){
    currentOrientation=o; canvas.setWidth(dimensions[o].width); canvas.setHeight(dimensions[o].height); canvas.renderAll();
    document.getElementById('btn-portrait').className = o==='portrait' ? 'px-3 py-2 text-xs font-bold bg-[#2563eb] text-white flex items-center gap-1.5' : 'px-3 py-2 text-xs font-bold bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 flex items-center gap-1.5';
    document.getElementById('btn-landscape').className = o==='landscape' ? 'px-3 py-2 text-xs font-bold bg-[#2563eb] text-white flex items-center gap-1.5' : 'px-3 py-2 text-xs font-bold bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 flex items-center gap-1.5';
  }
  applyOrientation(currentOrientation);
  fabric.Object.prototype.set({transparentCorners:false,cornerColor:'rgba(29,78,216,.8)',cornerStrokeColor:'#1d4ed8',borderColor:'rgba(29,78,216,.8)',cornerSize:10,padding:5});
  let currentZoom=1;
  function setZoom(z){ currentZoom=Math.max(.2,Math.min(3,z)); canvas.setZoom(currentZoom); canvas.setWidth(dimensions[currentOrientation].width*currentZoom); canvas.setHeight(dimensions[currentOrientation].height*currentZoom); document.getElementById('btn-zoom-reset').textContent=Math.round(currentZoom*100)+'%'; }
  document.getElementById('btn-zoom-in')?.addEventListener('click',()=>setZoom(currentZoom+.1));
  document.getElementById('btn-zoom-out')?.addEventListener('click',()=>setZoom(currentZoom-.1));
  document.getElementById('btn-zoom-reset')?.addEventListener('click',()=>setZoom(1));
  function loadPageData(){
    canvas.clear(); canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
    let data=pageData[currentTemplateType][currentPage];
    if(data){ let j=data; if(typeof j==='string') try{j=JSON.parse(j)}catch(e){}; canvas.loadFromJSON(j, ()=>{ setZoom(1); canvas.renderAll(); }); }
  }
  loadPageData();
  document.getElementById('btn-front-page')?.addEventListener('click', function(){
    if(currentPage==='front') return;
    pageData[currentTemplateType][currentPage]=canvas.toJSON();
    currentPage='front'; loadPageData();
    this.classList.add('bg-[#2563eb]','text-white'); document.getElementById('btn-back-page').classList.remove('bg-[#2563eb]','text-white');
  });
  document.getElementById('btn-back-page')?.addEventListener('click', function(){
    if(currentPage==='back') return;
    pageData[currentTemplateType][currentPage]=canvas.toJSON();
    currentPage='back'; loadPageData();
    this.classList.add('bg-[#2563eb]','text-white'); document.getElementById('btn-front-page').classList.remove('bg-[#2563eb]','text-white');
  });
  document.getElementById('template-type-selector')?.addEventListener('change', function(){
    pageData[currentTemplateType][currentPage]=canvas.toJSON();
    currentTemplateType=this.value; loadPageData();
  });
  document.getElementById('btn-portrait')?.addEventListener('click', ()=>{ if(currentOrientation!=='portrait' && confirm('Resize canvas?')) applyOrientation('portrait'); });
  document.getElementById('btn-landscape')?.addEventListener('click', ()=>{ if(currentOrientation!=='landscape' && confirm('Resize canvas?')) applyOrientation('landscape'); });
  document.getElementById('btn-delete')?.addEventListener('click', ()=>{
    const objs=canvas.getActiveObjects(); if(objs.length){ canvas.discardActiveObject(); objs.forEach(o=>canvas.remove(o)); }
  });
  canvas.on('selection:created', ()=> document.getElementById('btn-delete').disabled=false);
  canvas.on('selection:cleared', ()=> document.getElementById('btn-delete').disabled=true);
  document.getElementById('btn-clear')?.addEventListener('click', ()=>{ if(confirm('Clear this page?')){ canvas.clear(); canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas)); }});
  document.querySelectorAll('.toolbox-item').forEach(el=>{
    el.addEventListener('click', ()=>{
      const type=el.dataset.type, text=el.dataset.text, center=canvas.getCenter();
      if(type==='text' || type==='text-custom'){
        const t=new fabric.IText(text||'Double click to edit',{left:center.left,top:center.top,fontFamily:'Arial',fontSize:20,fill:'#333',originX:'center',originY:'center'});
        canvas.add(t); canvas.setActiveObject(t);
      } else if(type==='grades_table' || type==='attendance_table'){
        const rect=new fabric.Rect({fill:'#f1f5f9',stroke:'#94a3b8',strokeWidth:2,strokeDashArray:[5,5],width:600,height:250,originX:'center',originY:'center'});
        const tt=new fabric.Text('['+(el.dataset.label||'Table')+']',{fontSize:24,fill:'#64748b',originX:'center',originY:'center'});
        const g=new fabric.Group([rect,tt],{left:center.left,top:center.top,originX:'center',originY:'center'}); canvas.add(g); canvas.setActiveObject(g);
      } else if(type==='signature'){
        const line=new fabric.Line([-100,0,100,0],{stroke:'#000',strokeWidth:2});
        const tt=new fabric.Text(text,{fontSize:16,fill:'#000',originX:'center',originY:'top',top:10});
        const g=new fabric.Group([line,tt],{left:center.left,top:center.top,originX:'center',originY:'center'}); canvas.add(g); canvas.setActiveObject(g);
      } else if(type==='logo'){
        fabric.Image.fromURL('{{ asset('uploads/app_image/report-card-logo.png') }}', img=>{ img.set({left:center.left,top:center.top,originX:'center',originY:'center'}); if(img.width>150) img.scaleToWidth(150); canvas.add(img); canvas.setActiveObject(img); });
      }
    });
  });
  document.getElementById('btn-save')?.addEventListener('click', function(){
    const prev=currentZoom; setZoom(1);
    pageData[currentTemplateType][currentPage]=canvas.toJSON();
    setZoom(prev);
    const designData={front:{semester:pageData.semester.front,period:pageData.period.front,end_of_year:pageData.end_of_year.front},back:{semester:pageData.semester.back,period:pageData.period.back,end_of_year:pageData.end_of_year.back}};
    const btn=this, orig=btn.innerHTML; btn.innerHTML='Saving...'; btn.disabled=true;
    fetch('{{ route('settings.report_card_template.canvas') }}',{
      method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
      credentials:'same-origin',
      body: JSON.stringify({branch_id: branchId, front_canvas_layout: JSON.stringify(designData.front), back_canvas_layout: JSON.stringify(designData.back)})
    }).then(async r=>{
      const txt=await r.text(); let j; try{j=JSON.parse(txt);}catch(e){throw new Error('Server error ('+r.status+'): '+txt.substring(0,400));}
      if(!r.ok) throw new Error(j.message||'HTTP '+r.status);
      return j;
    }).then(j=>{
      btn.innerHTML=orig; btn.disabled=false;
      alert(j.message||'Saved');
    }).catch(e=>{ btn.innerHTML=orig; btn.disabled=false; alert('Save failed: '+e.message); console.error('Canvas save error:', e); });
  });

  // ── LIVE PREVIEW (mock data updates as you edit) ──
  const mockSubjects = [{name:'Mathematics',marks:85,grade:'A'},{name:'Science',marks:78,grade:'B+'},{name:'English',marks:92,grade:'A+'}];
  function getClassicSettings(){
    const order = [];
    document.querySelectorAll('#rc-sections li').forEach(li=>{
      const k = li.dataset.key;
      const v = parseInt(li.querySelector('input').value||0,10);
      order.push({k,v});
    });
    order.sort((a,b)=>a.v-b.v);
    const titles = {};
    ['student_info','subjects_table','attendance_summary','gpa','class_rank','grade_scale_legend','signature_lines'].forEach(k=>{
      const el = document.querySelector('[name="title_'+k+'"]');
      titles[k] = el ? el.value : k;
    });
    return {
      show_gpa: document.querySelector('[name="show_gpa"]')?.checked ? 1:0,
      show_class_rank: document.querySelector('[name="show_class_rank"]')?.checked ? 1:0,
      show_attendance_summary: document.querySelector('[name="show_attendance_summary"]')?.checked ? 1:0,
      show_vpa_signature_line: document.querySelector('[name="show_vpa_signature_line"]')?.checked ? 1:0,
      show_principal_signature_line: document.querySelector('[name="show_principal_signature_line"]')?.checked ? 1:0,
      grading_scale_legend: document.querySelector('[name="grading_scale_legend"]')?.checked ? 1:0,
      order: order.filter(o=>o.v>0).map(o=>o.k),
      titles,
      enable_back_page: document.querySelector('[name="enable_back_page"]')?.checked ? 1:0,
      back_page_content: document.querySelector('[name="back_page_content"]')?.value || ''
    };
  }
  function renderClassicLive(){
    const fs = getClassicSettings();
    const order = fs.order.length ? fs.order : ['student_info','subjects_table','attendance_summary','gpa','class_rank','grade_scale_legend','signature_lines'];
    const wrap = document.getElementById('live-preview-classic');
    if(!wrap) return;
    let html = '<div class="space-y-4 text-sm">';
    order.forEach(key=>{
      const title = fs.titles[key] || key.replace(/_/g,' ');
      if(key==='student_info'){
        html += `<div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4"><h4 class="font-bold mb-2">${title}</h4><div class="grid grid-cols-2 gap-2 text-slate-600 dark:text-slate-300"><div><span class="font-semibold">Student:</span> John Doe</div><div><span class="font-semibold">Class:</span> Grade 10 - A</div><div><span class="font-semibold">Roll:</span> 001</div><div><span class="font-semibold">Year:</span> ${new Date().getFullYear()}</div></div></div>`;
      } else if(key==='subjects_table'){
        html += `<div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4"><h4 class="font-bold mb-2">${title}</h4><table class="w-full text-left text-xs"><thead><tr class="bg-slate-50 dark:bg-slate-800"><th class="px-2 py-1">Subject</th><th class="px-2 py-1">Marks</th><th class="px-2 py-1">Grade</th></tr></thead><tbody>`;
        mockSubjects.forEach(s=>{ html += `<tr class="border-t border-slate-100 dark:border-slate-700"><td class="px-2 py-1">${s.name}</td><td class="px-2 py-1">${s.marks}</td><td class="px-2 py-1">${s.grade}</td></tr>`; });
        html += `</tbody></table></div>`;
      } else if(key==='gpa' && fs.show_gpa){
        html += `<div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 text-center"><h4 class="font-bold">${title}</h4><p class="text-2xl font-extrabold text-[#2563eb] mt-1">3.85</p></div>`;
      } else if(key==='class_rank' && fs.show_class_rank){
        html += `<div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 text-center"><h4 class="font-bold">${title}</h4><p class="font-semibold mt-1">5th / 45</p></div>`;
      } else if(key==='attendance_summary' && fs.show_attendance_summary){
        html += `<div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4"><h4 class="font-bold">${title}</h4><p class="mt-1">Attendance: <span class="font-bold text-emerald-600">96%</span></p></div>`;
      } else if(key==='grade_scale_legend' && fs.grading_scale_legend){
        html += `<div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4"><h4 class="font-bold">${title}</h4><p class="text-xs text-slate-500">A+ (90-100), A (80-89), B+ (70-79), B (60-69), C (50-59)</p></div>`;
      } else if(key==='signature_lines'){
        let sig=''; if(fs.show_vpa_signature_line) sig+=`<div class="text-center text-xs"><div class="w-32 border-t border-slate-900 dark:border-white pt-1">VPA Signature</div></div>`; if(fs.show_principal_signature_line) sig+=`<div class="text-center text-xs"><div class="w-32 border-t border-slate-900 dark:border-white pt-1">Principal Signature</div></div>`;
        if(sig) html += `<div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-600 p-4 flex justify-between pt-8">${sig}</div>`;
      }
    });
    if(fs.enable_back_page && fs.back_page_content){
      html += `<div class="rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-950/20 p-4"><h4 class="font-bold text-amber-800 dark:text-amber-300 text-xs uppercase">Back Page</h4><div class="prose prose-sm max-w-none mt-2 text-slate-700 dark:text-slate-300">${fs.back_page_content}</div></div>`;
    }
    html += `</div>`;
    wrap.innerHTML = html;
  }
  // bind live updates
  document.querySelectorAll('#report_card_template_form input, #report_card_template_form textarea').forEach(el=>{
    el.addEventListener('input', renderClassicLive);
    el.addEventListener('change', renderClassicLive);
  });
  if(window.$ && $('#rc-sections').length){
    $('#rc-sections').on('sortupdate sortstop', ()=> setTimeout(renderClassicLive, 50));
  }
  // update when switching to preview tab
  document.querySelectorAll('button').forEach(btn=>{
    if(btn.textContent.trim()==='Preview'){
      btn.addEventListener('click', ()=> setTimeout(renderClassicLive, 80));
    }
  });
  // canvas live snapshot
  function updateCanvasPreview(){
    const wrap=document.getElementById('live-preview-canvas-wrap'), img=document.getElementById('live-preview-canvas-img');
    if(!wrap||!img||!canvas) return;
    try{
      if(canvas.getObjects().length){
        wrap.classList.remove('hidden');
        img.src = canvas.toDataURL({format:'png', multiplier:0.35});
      } else {
        wrap.classList.add('hidden');
      }
    }catch(e){}
  }
  canvas.on('object:added', updateCanvasPreview);
  canvas.on('object:modified', updateCanvasPreview);
  canvas.on('object:removed', updateCanvasPreview);
  canvas.on('path:created', updateCanvasPreview);
  // grapejs live
  function updateGjsPreview(){
    const wrap=document.getElementById('live-preview-grapejs-wrap'), target=document.getElementById('live-preview-grapejs');
    if(!wrap||!target||!gjsEditor) return;
    try{
      const html=gjsEditor.getHtml(), css=gjsEditor.getCss();
      if(html && html.trim().length>20){
        wrap.classList.remove('hidden');
        target.innerHTML = html + `<style>${css}</style>`;
      }
    }catch(e){}
  }
  setInterval(()=>{ if(gjsEditor && document.getElementById('gjs')?.offsetParent) updateGjsPreview(); }, 1500);
  // initial
  setTimeout(renderClassicLive, 300);
  setTimeout(updateCanvasPreview, 600);
});
</script>
@endpush
