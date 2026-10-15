<form method="POST" action="{{ route('classes.teacher_allocation_save') }}" class="p-5 space-y-4">
    @csrf
    <input type="hidden" name="allocation_id" value="{{ $allocation->id }}">
    @if(is_superadmin_loggedin())
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
        <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
            @foreach($branches as $b)<option value="{{ $b->id }}" @selected($b->id==$allocation->branch_id)>{{ $b->name }}</option>@endforeach
        </select>
    </div>
    @endif
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class') }} <span class="text-red-500">*</span></label>
        <select name="class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
            @foreach($classes as $c)<option value="{{ $c->id }}" @selected($c->id==$allocation->class_id)>{{ $c->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }}</label>
        <select name="section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
            @foreach($sections as $s)<option value="{{ $s->id }}" @selected($s->id==$allocation->section_id)>{{ $s->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class_teacher') }} <span class="text-red-500">*</span></label>
        <select name="staff_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
            @foreach($teachers as $id=>$name)<option value="{{ $id }}" @selected($id==$allocation->teacher_id)>{{ $name }}</option>@endforeach
        </select>
    </div>
    <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeModal()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ translate('cancel') }}</button>
        <button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2 text-sm font-semibold text-white hover:bg-blue-700">{{ translate('update') }}</button>
    </div>
</form>
