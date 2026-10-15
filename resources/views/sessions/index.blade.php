@extends('layouts.app')
@section('title', translate('session_settings'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('session_settings') }}</span></div>
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <h3 class="font-bold text-sm mb-3">{{ translate('add_session') }}</h3>
            <form method="POST" action="{{ route('sessions.store') }}" class="space-y-3">
                @csrf
                <input name="session" placeholder="{{ translate('session') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm @error('session') border-red-300 @enderror" required value="{{ old('session') }}">
                @error('session')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="w-full rounded-xl bg-[#2563eb] py-2.5 text-sm font-semibold text-white">{{ translate('save') }}</button>
            </form>
        </div>
        <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200"><h3 class="text-sm font-bold">{{ translate('session_list') }}</h3></div>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('session') }}</th><th class="px-4 py-3">{{ translate('created_by') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($sessions as $i=>$s)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $s->school_year }}</td><td class="px-4 py-3">{{ get_type_name_by_id('staff', $s->created_by) ?: $s->created_by }}</td><td class="px-4 py-3"><div class="flex justify-end gap-1.5"><button onclick="openEdit({{ $s->id }}, '{{ addslashes($s->school_year) }}')" class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-pencil-square class="size-4"/></button><form method="POST" action="{{ route('sessions.destroy',$s->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">@csrf @method('DELETE')<button class="grid size-8 place-items-center rounded-lg bg-white border border-slate-200 text-red-600"><x-heroicon-o-trash class="size-4"/></button></form><a href="{{ route('sessions.set',$s->id) }}" class="rounded-lg bg-[#2563eb] px-3 py-1.5 text-xs font-semibold text-white">{{ translate('activate') }}</a></div></td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse</tbody></table></div>
        </div>
    </div>
</div>
<div id="editModal" class="fixed inset-0 hidden items-center justify-center bg-black/40 p-4 z-50"><div class="w-full max-w-md rounded-2xl bg-white p-5"><h3 class="font-bold mb-3">{{ translate('edit_session') }}</h3><form id="editForm" class="space-y-3">@csrf<input type="hidden" name="schoolyear_id" id="edit_id"><input name="session" id="edit_session" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" required><div class="flex justify-end gap-2"><button type="button" onclick="closeEdit()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">Cancel</button><button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white">{{ translate('update') }}</button></div></form></div></div>
@push('scripts')
<script>
function openEdit(id, name){ document.getElementById('edit_id').value=id; document.getElementById('edit_session').value=name; document.getElementById('editModal').classList.remove('hidden'); document.getElementById('editModal').classList.add('flex'); }
function closeEdit(){ document.getElementById('editModal').classList.add('hidden'); document.getElementById('editModal').classList.remove('flex'); }
document.getElementById('editForm').addEventListener('submit', function(e){
    e.preventDefault(); const fd=new FormData(this);
    fetch('{{ route('sessions.update') }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd}).then(r=>r.json()).then(j=>{ if(j.status==='success') location.reload(); else alert(Object.values(j.error||{}).join('\n')); });
});
</script>
@endpush
@endsection
