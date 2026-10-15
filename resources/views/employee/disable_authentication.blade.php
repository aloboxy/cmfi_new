@extends('layouts.app')
@section('title', translate('deactivate_account'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('deactivate_account') }}</span>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('deactivate_account') }}</div>
        <div class="p-5">
            <form method="POST" action="{{ route('employee.disable_authentication') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('role') }}</label>
                        <select name="staff_role" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                            <option value="">{{ translate('select') }}</option>
                            @foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" name="search" value="1" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('search') }}</button>
            </form>
            @if(isset($stafflist))
            <form method="POST" action="{{ route('employee.disable_authentication') }}" class="mt-6 space-y-4">
                @csrf
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-4 py-3"><input type="checkbox" id="selectAll"></th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('email') }}</th><th class="px-4 py-3">{{ translate('branch') }}</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($stafflist as $s)
                            <tr><td class="px-4 py-2"><input type="checkbox" name="views_bulk_operations[]" value="{{ $s->id }}" class="rounded"></td><td class="px-4 py-2 font-medium">{{ $s->name }}</td><td class="px-4 py-2">{{ $s->email }}</td><td class="px-4 py-2">{{ get_type_name_by_id('branch',$s->branch_id) }}</td></tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($stafflist && count($stafflist)>0)
                <button type="submit" name="auth" value="1" class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">{{ translate('activate') }}</button>
                @endif
            </form>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>document.getElementById('selectAll')?.addEventListener('change', function(){document.querySelectorAll('input[name="views_bulk_operations[]"]').forEach(cb=>cb.checked=this.checked);});</script>
@endpush
@endsection
