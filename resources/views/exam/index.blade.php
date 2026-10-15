@extends('layouts.app')
@section('title', translate('exam_list'))
@section('content')
<div class="space-y-5">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
      <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border shadow-xs"><x-heroicon-o-home class="size-4"/></a>
      <span>/</span>
      <span class="font-semibold text-slate-800">{{ translate('exam_master') }}</span>
      <span>/</span>
      <span class="font-semibold text-slate-800">{{ translate('exam_list') }}</span>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('exam.tabulation') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-4 py-2 text-sm font-semibold transition">
        <x-heroicon-o-table-cells class="size-4"/>
        {{ translate('master_gradesheet') }}
      </a>
      <button onclick="document.getElementById('addExamModal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-semibold shadow-xs transition">
        <x-heroicon-o-plus class="size-4"/>
        {{ translate('add').' '.translate('exam') }}
      </button>
    </div>
  </div>

  <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-bold text-slate-800 text-base">{{ translate('exam_list') }}</h3>
      <span class="text-xs text-slate-500">{{ $examlist->count() }} {{ translate('records') }}</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-500 border-b border-slate-100">
            <th class="px-4 py-3.5 text-left">#</th>
            <th class="px-4 py-3.5 text-left">{{ translate('name') }}</th>
            <th class="px-4 py-3.5 text-left">{{ translate('term') }}</th>
            <th class="px-4 py-3.5 text-left">{{ translate('type') }}</th>
            <th class="px-4 py-3.5 text-left">{{ translate('grade_distribution') }}</th>
            @if(is_superadmin_loggedin())<th class="px-4 py-3.5 text-left">{{ translate('branch') }}</th>@endif
            <th class="px-4 py-3.5 text-right">{{ translate('action') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($examlist as $i=>$row)
            @php 
              $distIds = json_decode($row->mark_distribution ?? '[]', true) ?: []; 
            @endphp
            <tr class="hover:bg-slate-50/60 transition">
              <td class="px-4 py-3.5 text-slate-400 font-medium">{{ $i+1 }}</td>
              <td class="px-4 py-3.5 font-bold text-slate-800">{{ $row->name }}</td>
              <td class="px-4 py-3.5 text-slate-600">{{ get_type_name_by_id('exam_term', $row->term_id) }}</td>
              <td class="px-4 py-3.5">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                  {{ ['1'=>'Marks','2'=>'GPA','3'=>'Marks & GPA'][$row->type_id] ?? $row->type_id }}
                </span>
              </td>
              <td class="px-4 py-3.5">
                @if(!empty($distIds))
                  <div class="flex flex-wrap gap-1">
                    @foreach($distIds as $did)
                      <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        {{ get_type_name_by_id('exam_mark_distribution', $did) }}
                      </span>
                    @endforeach
                  </div>
                @else
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="size-1.5 rounded-full bg-amber-500"></span>
                    {{ translate('no_distribution') }} ({{ translate('full_mark_only') }})
                  </span>
                @endif
              </td>
              @if(is_superadmin_loggedin())
                <td class="px-4 py-3.5 text-slate-600">{{ $row->branch_name }}</td>
              @endif
              <td class="px-4 py-3.5 text-right">
                <div class="flex justify-end gap-1.5">
                  <a href="{{ route('exam.marks', ['exam_id' => $row->id]) }}" title="{{ translate('mark_entries') }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-300 transition">
                    <x-heroicon-o-pencil class="size-4"/>
                  </a>
                  @if(get_permission('exam','is_edit'))
                    <a href="{{ route('exam.edit', $row->id) }}" title="{{ translate('edit') }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-300 transition">
                      <x-heroicon-o-pencil-square class="size-4"/>
                    </a>
                  @endif 
                  @if(get_permission('exam','is_delete'))
                    <form method="POST" action="{{ route('exam.destroy', $row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">
                      @csrf @method('DELETE')
                      <button type="submit" title="{{ translate('delete') }}" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-red-600 hover:border-red-300 transition">
                        <x-heroicon-o-trash class="size-4"/>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="{{ is_superadmin_loggedin() ? 7 : 6 }}" class="px-4 py-12 text-center text-slate-400 font-medium">
                <x-heroicon-o-document-text class="mx-auto size-10 text-slate-300 mb-2" />
                {{ translate('no_information_available') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div id="addExamModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs z-50 grid place-items-center p-4">
  <div class="bg-white rounded-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-auto shadow-2xl border border-slate-100">
    <div class="flex items-center justify-between mb-5 border-b pb-3">
      <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
        <x-heroicon-o-academic-cap class="size-5 text-blue-600" />
        {{ translate('add').' '.translate('exam') }}
      </h3>
      <button type="button" onclick="document.getElementById('addExamModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <x-heroicon-o-x-mark class="size-5" />
      </button>
    </div>
    <form method="POST" action="{{ route('exam.store') }}" id="examForm" class="space-y-4">
      @csrf
      @if(is_superadmin_loggedin())
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('branch') }} <span class="text-red-500">*</span></label>
          <select name="branch_id" id="ex_branch" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">{{ translate('select') }}</option>
            @foreach($branches as $b)
              <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
      @endif

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('name') }} <span class="text-red-500">*</span></label>
        <input type="text" name="name" placeholder="e.g. 1st Semester Final Exam" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none" required>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('exam_term') }} <span class="text-red-500">*</span></label>
        <select name="term_id" id="ex_term" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none" required>
          <option value="">{{ translate('select') }}</option>
          @foreach($terms as $t)
            <option value="{{ $t->id }}">{{ $t->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('exam_type') }} <span class="text-red-500">*</span></label>
        <select name="type_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="1">Marks Only</option>
          <option value="2">GPA Only</option>
          <option value="3">Both Marks & GPA</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
          {{ translate('grade_distribution') }} 
          <span class="text-xs font-normal text-slate-400">({{ translate('optional') }})</span>
        </label>
        <p class="text-xs text-slate-500 mb-2">Select distributed point categories (e.g. Theory, Practical). Leave unselected if exam has no distribution (single full mark).</p>
        <select name="mark_distribution[]" multiple class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm min-h-[95px] bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          @foreach($distributions as $d)
            <option value="{{ $d->id }}">{{ $d->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('remark') }}</label>
        <textarea name="remark" rows="2" placeholder="Optional notes regarding this exam..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
      </div>

      <div class="flex justify-end gap-2 pt-2 border-t">
        <button type="button" onclick="document.getElementById('addExamModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-xs">{{ translate('save') }}</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('ex_branch')?.addEventListener('change', function(){
  const bid = this.value;
  fetch('{{ route('ajax.getDataByBranch') }}', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
    body: JSON.stringify({branch_id: bid, table: 'exam_term'})
  }).then(r=>r.text()).then(html=>{ 
    const el = document.getElementById('ex_term'); 
    if(el) el.innerHTML = html; 
  });
});
</script>
@endpush
@endsection

