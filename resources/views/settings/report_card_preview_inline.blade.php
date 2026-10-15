@php
  $fs = $template->front_layout_settings ?? \App\Models\ReportCardTemplate::defaultLayout();
  if (is_string($fs)) $fs = json_decode($fs, true);
  $order = $fs['order'] ?? [];
  $titles = $fs['titles'] ?? [];
  $mock = $mock ?? ['student_name'=>'John Doe','class'=>'Grade 10 - A','roll'=>'001','gpa'=>'3.85','rank'=>'5th / 45','attendance'=>'96%','subjects'=>[['name'=>'Mathematics','marks'=>85,'grade'=>'A'],['name'=>'Science','marks'=>78,'grade'=>'B+'],['name'=>'English','marks'=>92,'grade'=>'A+']]];
@endphp
<div class="space-y-4 text-sm">
  @foreach($order as $key)
    @php $title = $titles[$key] ?? ucwords(str_replace('_',' ', $key)); @endphp
    @if($key==='student_info')
      <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
        <h4 class="font-bold text-slate-900 dark:text-white mb-2">{{ $title }}</h4>
        <div class="grid grid-cols-2 gap-2 text-slate-600 dark:text-slate-300">
          <div><span class="font-semibold">Student:</span> {{ $mock['student_name'] }}</div>
          <div><span class="font-semibold">Class:</span> {{ $mock['class'] }}</div>
          <div><span class="font-semibold">Roll:</span> {{ $mock['roll'] }}</div>
          <div><span class="font-semibold">Year:</span> {{ date('Y') }}</div>
        </div>
      </div>
    @elseif($key==='subjects_table')
      <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
        <h4 class="font-bold mb-2">{{ $title }}</h4>
        <table class="w-full text-left text-xs">
          <thead><tr class="bg-slate-50 dark:bg-slate-800"><th class="px-2 py-1">Subject</th><th class="px-2 py-1">Marks</th><th class="px-2 py-1">Grade</th></tr></thead>
          <tbody>
            @foreach(($mock['subjects'] ?? []) as $s)
              <tr class="border-t border-slate-100 dark:border-slate-700"><td class="px-2 py-1">{{ $s['name'] }}</td><td class="px-2 py-1">{{ $s['marks'] }}</td><td class="px-2 py-1">{{ $s['grade'] }}</td></tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @elseif($key==='gpa' && !empty($fs['show_gpa']))
      <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 text-center">
        <h4 class="font-bold">{{ $title }}</h4>
        <p class="text-2xl font-extrabold text-[#2563eb] mt-1">{{ $mock['gpa'] }}</p>
      </div>
    @elseif($key==='class_rank' && !empty($fs['show_class_rank']))
      <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 text-center">
        <h4 class="font-bold">{{ $title }}</h4>
        <p class="font-semibold mt-1">{{ $mock['rank'] }}</p>
      </div>
    @elseif($key==='attendance_summary' && !empty($fs['show_attendance_summary']))
      <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
        <h4 class="font-bold">{{ $title }}</h4>
        <p class="mt-1">Attendance: <span class="font-bold text-emerald-600">{{ $mock['attendance'] }}</span></p>
      </div>
    @elseif($key==='grade_scale_legend' && !empty($fs['grading_scale_legend']))
      <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
        <h4 class="font-bold">{{ $title }}</h4>
        <p class="text-xs text-slate-500">A+ (90-100), A (80-89), B+ (70-79), B (60-69), C (50-59)</p>
      </div>
    @elseif($key==='signature_lines')
      <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-600 p-4 flex justify-between pt-8">
        @if(!empty($fs['show_vpa_signature_line']))<div class="text-center text-xs"><div class="w-32 border-t border-slate-900 dark:border-white pt-1">VPA Signature</div></div>@endif
        @if(!empty($fs['show_principal_signature_line']))<div class="text-center text-xs"><div class="w-32 border-t border-slate-900 dark:border-white pt-1">Principal Signature</div></div>@endif
      </div>
    @endif
  @endforeach
  @if(!empty($template->enable_back_page) && !empty($template->back_page_content))
    <div class="rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-950/20 p-4">
      <h4 class="font-bold text-amber-800 dark:text-amber-300 text-xs uppercase">Back Page</h4>
      <div class="prose prose-sm max-w-none mt-2 text-slate-700 dark:text-slate-300">{!! $template->back_page_content !!}</div>
    </div>
  @endif
</div>
