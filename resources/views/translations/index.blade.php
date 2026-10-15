@extends('layouts.app')
@section('title', translate('translations'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('translations') }}</span></div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <h3 class="font-bold text-sm mb-3">{{ translate('add_language') }}</h3>
        <form method="POST" action="{{ route('translations.store') }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
            @csrf
            <div><label class="block text-sm font-medium mb-1">{{ translate('language_name') }} <span class="text-red-500">*</span></label><input name="name" class="rounded-xl border border-slate-200 px-3 py-2 text-sm" required></div>
            <div><label class="block text-sm font-medium mb-1">Flag</label><input type="file" name="flag" accept="image/*" class="text-sm"></div>
            <button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2 text-sm font-semibold text-white">{{ translate('save') }}</button>
        </form>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200"><h3 class="text-sm font-bold">{{ translate('language_list') }}</h3></div>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('field') }}</th><th class="px-4 py-3">{{ translate('status') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($languages as $i=>$l)<tr><td class="px-4 py-3">{{ $i+1 }}</td><td class="px-4 py-3 font-medium">{{ $l->name }}</td><td class="px-4 py-3 font-mono text-xs">{{ $l->lang_field }}</td><td class="px-4 py-3"><label class="relative inline-flex h-5 w-9 items-center rounded-full transition {{ $l->status ? 'bg-[#2563eb]' : 'bg-slate-200' }}"><input type="checkbox" @checked($l->status) onchange="toggleLang({{ $l->id }}, this.checked)" class="sr-only"><span class="inline-block h-3 w-3 transform rounded-full bg-white transition {{ $l->status ? 'translate-x-5' : 'translate-x-1' }}"></span></label></td><td class="px-4 py-3"><div class="flex justify-end gap-1.5"><a href="{{ route('translations.update',['lang'=>$l->lang_field]) }}" class="rounded-lg bg-[#2563eb] px-3 py-1.5 text-xs font-semibold text-white">{{ translate('edit') }}</a><button onclick="openRename({{ $l->id }}, '{{ addslashes($l->name) }}')" class="rounded-lg bg-white border border-slate-200 px-3 py-1.5 text-xs">{{ translate('rename') }}</button><form method="POST" action="{{ route('translations.destroy',$l->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">@csrf @method('DELETE')<button class="rounded-lg bg-white border border-slate-200 px-3 py-1.5 text-xs text-red-600">{{ translate('delete') }}</button></form></div></td></tr>@empty<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse</tbody></table></div>
    </div>
</div>
<div id="renameModal" class="fixed inset-0 hidden items-center justify-center bg-black/40 p-4 z-50"><div class="w-full max-w-md rounded-2xl bg-white p-5"><h3 class="font-bold mb-3">{{ translate('rename') }}</h3><form id="renameForm" method="POST" enctype="multipart/form-data" class="space-y-3">@csrf<input type="hidden" name="_method" value="POST"><input name="rename" id="renameInput" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required><input type="file" name="flag" accept="image/*" class="text-sm"><div class="flex justify-end gap-2"><button type="button" onclick="closeRename()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">Cancel</button><button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-2 text-sm font-semibold text-white">{{ translate('update') }}</button></div></form></div></div>
@push('scripts')
<script>
function toggleLang(id, checked){ fetch('{{ route('translations.status') }}',{method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams({lang_id:id, status:checked})}).then(r=>r.text()).then(t=>{});}
let renameId=null;
function openRename(id,name){ renameId=id; document.getElementById('renameInput').value=name; document.getElementById('renameForm').action='/translations/rename/'+id; document.getElementById('renameModal').classList.remove('hidden'); document.getElementById('renameModal').classList.add('flex');}
function closeRename(){ document.getElementById('renameModal').classList.add('hidden'); document.getElementById('renameModal').classList.remove('flex');}
</script>
@endpush
@endsection
