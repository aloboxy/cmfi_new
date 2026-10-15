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
            <form method="POST" action="{{ route('parents.disable_authentication') }}">
                @csrf
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><th class="px-4 py-3"><input type="checkbox" id="selectAll"></th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('email') }}</th><th class="px-4 py-3">{{ translate('mobile_no') }}</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($parentslist as $p)
                            <tr><td class="px-4 py-2"><input type="checkbox" name="views_bulk_operations[]" value="{{ $p->id }}" class="rounded"></td><td class="px-4 py-2 font-medium">{{ $p->name }}</td><td class="px-4 py-2">{{ $p->email }}</td><td class="px-4 py-2">{{ $p->mobileno }}</td></tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(count($parentslist)>0)
                <div class="mt-4 flex justify-end">
                    <button type="submit" name="auth" value="1" class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">{{ translate('activate') }}</button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>document.getElementById('selectAll')?.addEventListener('change', function(){document.querySelectorAll('input[name="views_bulk_operations[]"]').forEach(cb=>cb.checked=this.checked);});</script>
@endpush
@endsection
