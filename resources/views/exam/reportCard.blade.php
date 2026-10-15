<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ translate('report_card') }} — Print</title>
<style>
  @media print { .page-break{page-break-after:always} .no-print{display:none} }
  @page { size: A4; margin: 12mm; }
  body{font-family:Inter, ui-sans-serif, system-ui; color:#0f172a; background:#fff; margin:0}
  .report-page{width:100%; min-height:11in; margin:0 auto 20px; padding:28px 32px; background:#fff; box-sizing:border-box}
  .report-page.landscape{min-height:8.5in}
  table{border-collapse:collapse; width:100%}
  .table-bordered th,.table-bordered td{border:1px solid #e2e8f0; padding:6px 8px; font-size:12px}
  .table-bordered th{background:#f8fafc; text-align:left; font-weight:600}
  .text-right{text-align:right}
  .text-center{text-align:center}
  .mt-lg{margin-top:14px}
  .no-print-bar{position:sticky; top:0; z-index:50; background:#0f172a; color:#fff; padding:10px 16px; display:flex; justify-content:space-between; align-items:center}
  .no-print-bar button, .no-print-bar a{background:#2563eb; color:#fff; padding:8px 14px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:600}
</style>
</head>
<body>
<div class="no-print-bar no-print">
  <span>{{ count($student_array) }} {{ translate(count($student_array)===1 ? 'report_card' : 'report_cards') }} — {{ translate('bulk_and_individual') }} • {{ $template_type ?? 'semester' }}</span>
  <div style="display:flex; gap:8px">
    <a href="{{ route('exam.report_card') }}">Back</a>
    <button onclick="window.print()">Print / Save PDF</button>
  </div>
</div>

@php
  $frontSettings = [];
  if (!empty($template['front_layout_settings'])) {
      $frontSettings = is_array($template['front_layout_settings']) ? $template['front_layout_settings'] : json_decode($template['front_layout_settings'], true);
  }
  $frontSettings = is_array($frontSettings) ? $frontSettings : [];
  $order = $frontSettings['order'] ?? ['student_info','subjects_table','attendance_summary','gpa','class_rank','grade_scale_legend','signature_lines'];
  $titles = array_merge(['student_info'=>'Student Information','subjects_table'=>'Academic Performance','attendance_summary'=>'Attendance Summary','gpa'=>'GPA','class_rank'=>'Class Rank','grade_scale_legend'=>'Grading Scale','signature_lines'=>'Signature Lines'], $frontSettings['titles'] ?? []);
  $show_gpa = $frontSettings['show_gpa'] ?? 1;
  $show_class_rank = $frontSettings['show_class_rank'] ?? 0;
  $show_attendance_summary = $frontSettings['show_attendance_summary'] ?? $attendance;
  $show_vpa = $frontSettings['show_vpa_signature_line'] ?? 0;
  $show_principal = $frontSettings['show_principal_signature_line'] ?? 0;
  $show_grade_scale = $frontSettings['grading_scale_legend'] ?? $grade_scale;
  $enable_back = $template['enable_back_page'] ?? 0;
  $back_content = $template['back_page_content'] ?? '';

  $hasGrape = !empty($grapejs_design['front']) || !empty($grapejs_design['front']['html']);
  $hasCanvas = !empty($canvas_layouts['front_canvas_layout']);
  $grapeFrontHtml = '';
  $grapeFrontCss = '';
  $grapeOrientation = 'portrait';
  if ($hasGrape) {
      $gf = $grapejs_design['front'];
      if (is_array($gf) && isset($gf['html'])) { $grapeFrontHtml = $gf['html']; $grapeFrontCss = $gf['css'] ?? ''; $grapeOrientation = $grapejs_design['orientation'] ?? 'portrait'; }
      elseif (is_string($gf)) { $grapeFrontHtml = $gf; }
      elseif (is_array($gf) && isset($grapejs_design['orientation'])) { $grapeOrientation = $grapejs_design['orientation']; }
  }
  $orientationClass = ($grapeOrientation === 'landscape') ? 'landscape' : '';

  // Helper to replace placeholders in GrapeJS html per student
  function replacePlaceholders($html, $student, $school, $year){
      $map = [
          '[Student Name]' => ($student['first_name'] ?? '').' '.($student['last_name'] ?? ''),
          '[Class & Section]' => ($student['class_name'] ?? '').' '.(!empty($student['section_name'])?'('.$student['section_name'].')':''),
          '[Roll Number]' => $student['roll'] ?? '',
          '[Academic Year]' => $year,
          '[Promoted To Class]' => 'N/A',
          '[GPA / Final Grade]' => '—',
          '[Register No]' => $student['register_no'] ?? '',
      ];
      return str_replace(array_keys($map), array_values($map), $html);
  }
  $schoolYear = \Illuminate\Support\Facades\DB::table('schoolyear')->where('id',$sessionID)->value('school_year') ?? date('Y');
  $examRow = \Illuminate\Support\Facades\DB::table('exam')->where('id',$examID)->first();
  $branchRow = $examRow ? \Illuminate\Support\Facades\DB::table('branch')->where('id',$examRow->branch_id)->first() : null;
@endphp

@if($hasGrape)
  {{-- GrapeJS priority --}}
  @foreach($student_array as $sc => $studentID)
    @php
      $res = (new \App\Http\Controllers\ExamController)->getStudentReportCard($studentID, $examID, $sessionID);
      // Access private method via reflection or replicate logic here — fallback to direct query
      // Instead we re-query here for student
      $student = \Illuminate\Support\Facades\DB::table('enroll as en')
          ->join('student as s','s.id','=','en.student_id')
          ->leftJoin('class as c','c.id','=','en.class_id')
          ->leftJoin('section as se','se.id','=','en.section_id')
          ->leftJoin('parent as p','p.id','=','s.parent_id')
          ->where('en.student_id',$studentID)
          ->select('en.roll','s.*','c.name as class_name','se.name as section_name','p.father_name','p.mother_name')
          ->first();
      $studentArr = $student ? (array)$student : ['first_name'=>'','last_name'=>'','register_no'=>'','roll'=>''];
      $html = replacePlaceholders($grapeFrontHtml, $studentArr, (array)$branchRow, $schoolYear);
    @endphp
    <div class="report-page {{ $orientationClass }}">
      <style>{!! $grapeFrontCss !!}</style>
      {!! $html !!}
      @if(!empty($remarks_array[$studentID]))
        <div style="margin-top:12px; padding:10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px"><strong>Remarks:</strong> {{ $remarks_array[$studentID] }}</div>
      @endif
    </div>
    @if(!empty($grapejs_design['back']))
      <div class="report-page {{ $orientationClass }}">{!! is_array($grapejs_design['back']) ? ($grapejs_design['back']['html'] ?? $grapejs_design['back']) : $grapejs_design['back'] !!}</div>
    @endif
    @if($sc < count($student_array)-1)<div class="page-break"></div>@endif
  @endforeach
@elseif($hasCanvas)
  {{-- Canvas layouts (Fabric) — render as HTML fallback classic + canvas image note --}}
  @include('exam.reportCard_canvas', ['template'=>$template,'canvas_layouts'=>$canvas_layouts,'student_array'=>$student_array,'remarks_array'=>$remarks_array,'grade_scale'=>$grade_scale,'attendance'=>$attendance,'print_date'=>$print_date,'examID'=>$examID,'sessionID'=>$sessionID,'student_ranks'=>$student_ranks])
@else
  {{-- Classic sequential layout --}}
  @foreach($student_array as $sc => $studentID)
    @php
      $res = app(\App\Http\Controllers\ExamController::class)->getStudentReportCard($studentID, $examID, $sessionID);
      // Fallback if method is private — do direct query
      if(empty($res['student'])){
          $st = \Illuminate\Support\Facades\DB::table('enroll as en')
              ->join('student as s','s.id','=','en.student_id')
              ->leftJoin('class as c','c.id','=','en.class_id')
              ->leftJoin('section as se','se.id','=','en.section_id')
              ->leftJoin('parent as p','p.id','=','s.parent_id')
              ->where('en.student_id',$studentID)
              ->select('en.roll','s.*','c.name as class_name','se.name as section_name','en.class_id','p.father_name','p.mother_name','s.admission_date','s.birthday','s.gender')
              ->first();
          $res['student'] = $st ? (array)$st : [];
          $marks = \Illuminate\Support\Facades\DB::table('mark as m')
              ->leftJoin('subject as sub','sub.id','=','m.subject_id')
              ->leftJoin('timetable_exam as te', function($j){ $j->on('te.exam_id','=','m.exam_id')->on('te.class_id','=','m.class_id')->on('te.section_id','=','m.section_id')->on('te.subject_id','=','m.subject_id'); })
              ->where('m.exam_id',$examID)->where('m.student_id',$studentID)->where('m.session_id',$sessionID)
              ->select('m.mark as get_mark','m.absent as get_abs','sub.name as subject_name','te.mark_distribution')->get()->map(fn($r)=>(array)$r)->toArray();
          $res['exam'] = $marks;
      }
      $student = $res['student'];
      $getMarksList = $res['exam'];
      $rankLabel = $student_ranks[$studentID] ?? 'N/A';
      $exam = $examRow ? (array)$examRow : [];
      $branch = $branchRow ? (array)$branchRow : [];
      $markDistribution = !empty($exam['mark_distribution']) ? json_decode($exam['mark_distribution'], true) : [];
    @endphp
    <div class="report-page {{ $orientationClass }}">
      @foreach($order as $section)
        @if($section==='student_info')
          <table border="0" style="margin-top: 6px; width:100%">
            <tr>
              <td style="width:40%; vertical-align:top"><img style="max-width:180px" src="{{ asset('uploads/app_image/report-card-logo.png') }}" onerror="this.style.display='none'"></td>
              <td style="width:60%; vertical-align:top; text-align:right">
                <div style="font-size:22px; font-weight:800">{{ $branch['school_name'] ?? $branch['name'] ?? '' }}</div>
                <div style="font-size:12px; color:#475569">Academic Session: {{ $schoolYear }}</div>
                <div style="font-size:11px; color:#64748b">{{ $branch['address'] ?? '' }} • {{ $branch['mobileno'] ?? '' }} • {{ $branch['email'] ?? '' }}</div>
              </td>
            </tr>
          </table>
          <table class="table-bordered mt-lg" style="margin-top:14px">
            <tr><th>Name</th><td>{{ ($student['first_name'] ?? '').' '.($student['last_name'] ?? '') }}</td><th>Register No</th><td>{{ $student['register_no'] ?? '' }}</td><th>Roll</th><td>{{ $student['roll'] ?? '' }}</td></tr>
            <tr><th>Father</th><td>{{ $student['father_name'] ?? '' }}</td><th>Admission</th><td>{{ !empty($student['admission_date']) ? date('d/m/Y', strtotime($student['admission_date'])) : '' }}</td><th>DOB</th><td>{{ !empty($student['birthday']) ? date('d/m/Y', strtotime($student['birthday'])) : '' }}</td></tr>
            <tr><th>Mother</th><td>{{ $student['mother_name'] ?? '' }}</td><th>Class</th><td>{{ ($student['class_name'] ?? '').' '.(!empty($student['section_name'])?'('.$student['section_name'].')':'') }}</td><th>Gender</th><td>{{ ucfirst($student['gender'] ?? '') }}</td></tr>
          </table>
        @elseif($section==='subjects_table')
          <table class="table-bordered mt-lg" style="margin-top:14px">
            <thead><tr><th>{{ $titles['subjects_table'] ?? 'Academic Performance' }}</th>
              @foreach($markDistribution as $mid)<th>{{ \Illuminate\Support\Facades\DB::table('exam_mark_distribution')->where('id',$mid)->value('name') ?? $mid }}</th>@endforeach
              @if(($exam['type_id'] ?? 1)==1)<th>Total</th>@elseif(($exam['type_id'] ?? 1)==2)<th>Grade</th><th>Point</th>@elseif(($exam['type_id'] ?? 1)==3)<th>Total</th><th>Grade</th><th>Point</th>@endif
            </tr></thead>
            <tbody>
              @php $grandObtain=0; $grandFull=0; $totalGradePoint=0; $resultStatus=1; @endphp
              @foreach($getMarksList as $row)
                @php
                  $fullDist = json_decode($row['mark_distribution'] ?? '[]', true) ?: [];
                  $obtained = json_decode($row['get_mark'] ?? '[]', true);
                  if(!is_array($obtained)) $obtained = [$obtained];
                  $totalObtain=0; $totalFull=0;
                @endphp
                <tr>
                  <td style="width:32%">{{ $row['subject_name'] }}</td>
                  @foreach($fullDist as $i=>$val)
                    @php
                      $full = (float)($val['full_mark'] ?? 0);
                      $pass = (float)($val['pass_mark'] ?? 0);
                      $obt = (float)($obtained[$i] ?? 0);
                      if($row['get_abs']==='on'){} else { if($obt < $pass) $resultStatus=0; $totalObtain+=$obt; }
                      $totalFull+=$full;
                    @endphp
                    @if(($exam['type_id'] ?? 1)==1 || ($exam['type_id'] ?? 1)==3)
                      <td>{{ ($row['get_abs']==='on' ? 'Absent' : $obt.'/'.$full) }}</td>
                    @elseif(($exam['type_id'] ?? 1)==2)
                      @php $pct=$full>0?($obt*100)/$full:0; $gradeRec = \Illuminate\Support\Facades\DB::table('grade')->where('branch_id',$branchID)->where('lower_mark','<=',$pct)->where('upper_mark','>=',$pct)->first(); @endphp
                      <td>{{ $row['get_abs']==='on' ? 'Absent' : ($gradeRec->name ?? '-') }}</td>
                    @endif
                  @endforeach
                  @php $grandObtain+=$totalObtain; $grandFull+=$totalFull; @endphp
                  @if(($exam['type_id'] ?? 1)==1 || ($exam['type_id'] ?? 1)==3)
                    <td>{{ $totalObtain.'/'.$totalFull }}</td>
                  @endif
                  @if(($exam['type_id'] ?? 1)==2)
                    @php $pct=$totalFull>0?($totalObtain*100)/$totalFull:0; $gr=\Illuminate\Support\Facades\DB::table('grade')->where('branch_id',$branchID)->where('lower_mark','<=',$pct)->where('upper_mark','>=',$pct)->first(); if($gr) $totalGradePoint+=$gr->grade_point; @endphp
                    <td>{{ $gr->name ?? '-' }}</td><td>{{ isset($gr->grade_point)?number_format($gr->grade_point,2):'-' }}</td>
                  @endif
                  @if(($exam['type_id'] ?? 1)==3)
                    @php $pct=$totalFull>0?($totalObtain*100)/$totalFull:0; $gr=\Illuminate\Support\Facades\DB::table('grade')->where('branch_id',$branchID)->where('lower_mark','<=',$pct)->where('upper_mark','>=',$pct)->first(); if($gr) $totalGradePoint+=$gr->grade_point; @endphp
                    <td>{{ $gr->name ?? '-' }}</td><td>{{ isset($gr->grade_point)?number_format($gr->grade_point,2):'-' }}</td>
                  @endif
                </tr>
              @endforeach
              @if(($exam['type_id'] ?? 1)==1 || ($exam['type_id'] ?? 1)==3)
                <tr style="font-weight:700"><td>GRAND TOTAL</td><td colspan="{{ count($markDistribution)+1 }}">{{ $grandObtain.'/'.$grandFull }}, Avg: {{ $grandFull>0?number_format(($grandObtain*100)/$grandFull,2):0 }}%</td></tr>
                <tr style="font-weight:700"><td>RESULT</td><td colspan="{{ count($markDistribution)+1 }}">{{ $resultStatus==0?'Fail':'Pass' }}</td></tr>
              @endif
              @if(($exam['type_id'] ?? 1)==2)
                <tr style="font-weight:700"><td>GPA</td><td colspan="{{ count($markDistribution)+2 }}">{{ count($getMarksList)>0?number_format($totalGradePoint/count($getMarksList),2):'0.00' }}</td></tr>
              @endif
              @if(($exam['type_id'] ?? 1)==3)
                <tr style="font-weight:700"><td>GPA</td><td colspan="{{ count($markDistribution)+3 }}">{{ count($getMarksList)>0?number_format($totalGradePoint/count($getMarksList),2):'0.00' }}</td></tr>
              @endif
            </tbody>
          </table>
          @if(!empty($remarks_array[$studentID]))
            <div style="margin-top:8px; padding:8px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; font-size:12px"><strong>Remarks:</strong> {{ $remarks_array[$studentID] }}</div>
          @endif
        @elseif($section==='attendance_summary' && $show_attendance_summary)
          @php $year = explode('-', $schoolYear)[0] ?? date('Y'); $totalWorking = \Illuminate\Support\Facades\DB::table('student_attendance')->where('student_id',$studentID)->where('status','!=','H')->whereYear('date',$year)->count(); $totalPresent = \Illuminate\Support\Facades\DB::table('student_attendance')->where('student_id',$studentID)->where('status','P')->whereYear('date',$year)->count(); $pct = $totalWorking>0?($totalPresent*100)/$totalWorking:0; @endphp
          <table class="table-bordered" style="margin-top:12px; width:50%"><tr><th colspan="2" style="text-align:center">{{ $titles['attendance_summary'] ?? 'Attendance Summary' }}</th></tr><tr><th>Working days</th><td>{{ $totalWorking }}</td></tr><tr><th>Present</th><td>{{ $totalPresent }}</td></tr><tr><th>Percentage</th><td>{{ number_format($pct,2) }}%</td></tr></table>
        @elseif($section==='gpa' && $show_gpa)
          @php $gpa = ($exam['type_id'] ?? 1)==1 ? 'N/A' : (count($getMarksList)>0?number_format($totalGradePoint/count($getMarksList),2):'0.00'); @endphp
          <table class="table-bordered" style="margin-top:12px"><tr><th style="width:35%">{{ $titles['gpa'] ?? 'GPA' }}</th><td>{{ $gpa }}</td></tr></table>
        @elseif($section==='class_rank' && $show_class_rank)
          <table class="table-bordered" style="margin-top:12px"><tr><th style="width:35%">{{ $titles['class_rank'] ?? 'Class Rank' }}</th><td>{{ $rankLabel }}</td></tr></table>
        @elseif($section==='grade_scale_legend' && $show_grade_scale)
          <table class="table-bordered" style="margin-top:12px"><tr><th colspan="3" style="text-align:center">{{ $titles['grade_scale_legend'] ?? 'Grading Scale' }}</th></tr><tr><th>Grade</th><th>Min %</th><th>Max %</th></tr>
          @php $grades = \Illuminate\Support\Facades\DB::table('grade')->where('branch_id',$branchID)->get(); @endphp
          @foreach($grades as $g)<tr><td>{{ $g->name }}</td><td>{{ $g->lower_mark }}%</td><td>{{ $g->upper_mark }}%</td></tr>@endforeach
          </table>
        @elseif($section==='signature_lines')
          <div style="display:flex; justify-content:space-between; margin-top:32px; gap:12px">
            @if($show_principal)<div style="border-top:1px solid #cbd5e1; padding-top:6px; font-size:12px; text-align:center; min-width:120px">Principal Signature</div>@endif
            <div style="border-top:1px solid #cbd5e1; padding-top:6px; font-size:12px; text-align:center; min-width:120px">Class Teacher</div>
            @if($show_vpa)<div style="border-top:1px solid #cbd5e1; padding-top:6px; font-size:12px; text-align:center; min-width:120px">VPA Signature</div>@endif
          </div>
        @endif
      @endforeach
      <table class="table-bordered" style="margin-top:12px"><tr><th style="width:180px">Print Date</th><td>{{ $print_date }}</td></tr></table>
    </div>
    @if($enable_back)
      <div class="page-break"></div>
      <div class="report-page"><h3 style="margin-bottom:12px">Back Page</h3><div>{!! $back_content !!}</div></div>
      @if($sc < count($student_array)-1)<div class="page-break"></div>@endif
    @else
      @if($sc < count($student_array)-1)<div class="page-break"></div>@endif
    @endif
  @endforeach
@endif
</body>
</html>
