@extends('layouts.app')
@section('title', translate('edit') . ' ' . translate('exam'))
@section('content')
<div class="space-y-5">
  <div class="flex items-center gap-2 text-[13px] text-slate-500">
    <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border shadow-xs"><x-heroicon-o-home class="size-4"/></a>
    <span>/</span>
    <a href="{{ route('exam.index') }}" class="hover:text-blue-600 font-medium">{{ translate('exam_list') }}</a>
    <span>/</span>
    <span class="font-semibold text-slate-800">{{ translate('edit') }}</span>
  </div>

  <div class="rounded-2xl bg-white border border-slate-200/80 p-6 max-w-2xl shadow-xs">
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
      <div class="grid size-10 place-items-center rounded-xl bg-blue-50 text-blue-600">
        <x-heroicon-o-pencil-square class="size-5" />
      </div>
      <div>
        <h3 class="font-bold text-slate-800 text-lg">{{ translate('edit') }} {{ translate('exam') }}</h3>
        <p class="text-xs text-slate-500">Update exam settings and grade distribution configuration</p>
      </div>
    </div>

    <form method="POST" action="{{ route('exam.update', $exam->id) }}" class="space-y-4">
      @csrf 
      @method('PUT')
      @if(is_superadmin_loggedin())
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('branch') }} <span class="text-red-500">*</span></label>
          <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">{{ translate('select') }}</option>
            @foreach($branches as $b)
              <option value="{{ $b->id }}" @selected($b->id == $exam->branch_id)>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
      @endif

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('name') }} <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ $exam->name }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none" required>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('exam_term') }} <span class="text-red-500">*</span></label>
        <select name="term_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none" required>
          @foreach($terms as $t)
            <option value="{{ $t->id }}" @selected($t->id == $exam->term_id)>{{ $t->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('exam_type') }} <span class="text-red-500">*</span></label>
        <select name="type_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="1" @selected($exam->type_id == 1)>Marks Only</option>
          <option value="2" @selected($exam->type_id == 2)>GPA Only</option>
          <option value="3" @selected($exam->type_id == 3)>Both Marks & GPA</option>
        </select>
      </div>

      @php $selected = json_decode($exam->mark_distribution ?? '[]', true) ?: []; @endphp
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
          {{ translate('grade_distribution') }} 
          <span class="text-xs font-normal text-slate-400">({{ translate('optional') }})</span>
        </label>
        <p class="text-xs text-slate-500 mb-2">Select distributed point categories or unselect all if exam has no distribution (single full mark).</p>
        <select name="mark_distribution[]" multiple class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm min-h-[95px] bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
          @foreach($distributions as $d)
            <option value="{{ $d->id }}" @selected(in_array($d->id, (array)$selected))>{{ $d->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ translate('remark') }}</label>
        <textarea name="remark" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">{{ $exam->remark }}</textarea>
      </div>

      <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
        <a href="{{ route('exam.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-xs">{{ translate('update') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection

