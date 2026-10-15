@extends('layouts.app')
@section('title', translate('salary_assign'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4" /></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('salary_assign') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-4">{{ translate('select_ground') }}</h3>
        <form method="GET" action="{{ route('payroll.salary_assign') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if(is_superadmin_loggedin())
            <div><label class="block text-sm font-medium mb-1.5">{{ translate('branch') }}</label><select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected(request('branch_id')==$b->id)>{{ $b->name }}</option>@endforeach</select></div>
            @endif
            <div><label class="block text-sm font-medium mb-1.5">{{ translate('role') }} <span class="text-red-500">*</span></label><select name="staff_role" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" required><option value="">{{ translate('select') }}</option>@foreach($roles as $id=>$name)<option value="{{ $id }}" @selected(request('staff_role')==$id)>{{ $name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium mb-1.5">{{ translate('designation') }}</label><select name="designation_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@foreach($designationlist as $id=>$name)<option value="{{ $id }}" @selected(request('designation_id')==$id)>{{ $name }}</option>@endforeach</select></div>
            <div class="flex items-end"><button type="submit" name="search" value="1" class="w-full rounded-xl bg-[#2563eb] px-4 py-2.5 text-sm font-semibold text-white">{{ translate('filter') }}</button></div>
        </form>
    </div>
    @if(isset($stafflist))
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200"><h3 class="text-sm font-bold">{{ translate('staff_list') }}</h3></div>
        <form method="POST" action="{{ route('payroll.salary_assign') }}">
            @csrf
            <input type="hidden" name="staff_role" value="{{ request('staff_role') }}"><input type="hidden" name="designation_id" value="{{ request('designation_id') }}"><input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
            <div class="overflow-x-auto">
                <table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('designation') }}</th><th class="px-4 py-3">{{ translate('department') }}</th><th class="px-4 py-3">{{ translate('salary_template') }}</th></tr></thead><tbody class="divide-y divide-slate-100">
                    @forelse($stafflist as $row)
                    <tr><td class="px-4 py-3">{{ $row->name }}</td><td class="px-4 py-3">{{ $row->designation_name }}</td><td class="px-4 py-3">{{ $row->department_name }}</td><td class="px-4 py-3"><input type="hidden" name="stafflist[{{ $loop->index }}][id]" value="{{ $row->id }}"><select name="stafflist[{{ $loop->index }}][template_id]" class="rounded-lg border border-slate-200 px-3 py-2 text-sm"><option value="">{{ translate('select') }}</option>@foreach($templatelist as $id=>$name)<option value="{{ $id }}" @selected($row->salary_template_id==$id)>{{ $name }}</option>@endforeach</select></td></tr>
                    @empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse
                </tbody></table>
            </div>
            @if($stafflist->count())
            <div class="p-4 flex justify-end"><button type="submit" name="assign" value="1" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button></div>
            @endif
        </form>
    </div>
    @endif
</div>
@endsection
