@extends('layouts.app')
@section('title', translate('event_type'))
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
@endpush
@section('content')
<div class="space-y-5">
 <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border"><x-heroicon-o-home class="size-4"/></a><span>/</span><span class="font-semibold text-slate-800 dark:text-slate-200">{{ translate('event_type') }}</span></div>
 <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">
  <div class="lg:col-span-2"><div class="rounded-2xl bg-white dark:bg-black border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm"><div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm bg-white dark:bg-black text-slate-800 dark:text-white">{{ translate('add').' '.translate('event_type') }}</div>
  <form method="POST" action="{{ route('events.type_store') }}" class="p-5 space-y-4 bg-white dark:bg-black">@csrf
   @if(is_superadmin_loggedin())<div><label class="block text-sm font-medium mb-1.5 text-slate-700 dark:text-slate-300">{{ translate('branch') }} <span class="text-red-500">*</span></label><select name="branch_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-900 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-[var(--primary)]/20"><option value="">{{ translate('select') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>@endif
   <div><label class="block text-sm font-medium mb-1.5 text-slate-700 dark:text-slate-300">{{ translate('name') }} <span class="text-red-500">*</span></label><input type="text" name="type_name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-900 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-[var(--primary)]/20" required></div>
   <div>
     <label class="block text-sm font-medium mb-1.5 text-slate-700 dark:text-slate-300">Icon <span class="text-xs text-slate-400">(select one)</span></label>
     <div class="flex flex-wrap gap-2.5">
       @php $icons = [
         'bullhorn'=>'fa-bullhorn','flag-checkered'=>'fa-flag-checkered','comments'=>'fa-comments','concierge-bell'=>'fa-concierge-bell',
         'envelope'=>'fa-envelope','comment-alt'=>'fa-comment-alt','users'=>'fa-users','star'=>'fa-star','stamp'=>'fa-stamp','search'=>'fa-search'
       ]; @endphp
       @foreach($icons as $val=>$cls)
         <label class="cursor-pointer">
           <input type="radio" name="event_icon" value="{{ $val }}" @if($loop->first) checked @endif class="peer sr-only">
           <span class="grid size-9 place-items-center rounded-full border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-900 text-slate-700 dark:text-slate-300
                        peer-checked:border-[var(--primary)] peer-checked:bg-[var(--primary)]/10 peer-checked:text-[var(--primary)]
                        dark:peer-checked:border-white dark:peer-checked:bg-white dark:peer-checked:text-black
                        hover:border-slate-300 dark:hover:border-slate-600 transition">
             <i class="fas {{ $cls }} text-sm"></i>
           </span>
         </label>
       @endforeach
     </div>
   </div>
   <button type="submit" name="save" class="w-full rounded-xl bg-[var(--primary)] text-white py-2.5 text-sm font-semibold shadow hover:opacity-90 flex items-center justify-center gap-2"><x-heroicon-o-check class="size-4"/> {{ translate('save') }}</button>
  </form></div></div>
  <div class="lg:col-span-3"><div class="rounded-2xl bg-white dark:bg-black border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm"><div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm bg-white dark:bg-black text-slate-800 dark:text-white">{{ translate('event_type').' '.translate('list') }}</div>
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 dark:bg-zinc-900 text-xs uppercase text-slate-500 dark:text-slate-400"><th class="px-4 py-3">#</th><th class="px-4 py-3">{{ translate('name') }}</th><th class="px-4 py-3">{{ translate('icon') }}</th><th class="px-4 py-3">{{ translate('branch') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y divide-slate-200 dark:divide-slate-800">
   @forelse($typelist as $i=>$row)<tr class="hover:bg-slate-50 dark:hover:bg-zinc-900 bg-white dark:bg-black"><td class="px-4 py-3 text-slate-900 dark:text-white">{{ $i+1 }}</td><td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $row->name }}</td><td class="px-4 py-3"><i class="fas fa-{{ $row->icon }} text-slate-700 dark:text-slate-200"></i></td><td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $row->branch_name ?? '-' }}</td><td class="px-4 py-3"><div class="flex justify-end gap-1.5"><button onclick="editEvType({{ $row->id }}, '{{ addslashes($row->name) }}', '{{ addslashes($row->icon) }}', {{ $row->branch_id }})" class="grid size-8 place-items-center rounded-lg bg-white dark:bg-zinc-900 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-[var(--primary)]"><x-heroicon-o-pencil-square class="size-4"/></button><form method="POST" action="{{ route('events.type_delete', $row->id) }}" onsubmit="return confirm('{{ translate('are_you_sure') }}')">@csrf @method('DELETE')<button class="grid size-8 place-items-center rounded-lg bg-white dark:bg-zinc-900 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-red-600"><x-heroicon-o-trash class="size-4"/></button></form></div></td></tr>@empty<tr><td colspan="5" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">{{ translate('no_information_available') }}</td></tr>@endforelse
  </tbody></table></div></div></div>
 </div>
</div>
<div id="editEvTypeModal" class="hidden fixed inset-0 bg-black/40 z-50 grid place-items-center p-4"><div class="bg-white dark:bg-black rounded-2xl w-full max-w-md p-5 border border-slate-200 dark:border-slate-800"><h3 class="font-bold mb-4 text-slate-900 dark:text-white">{{ translate('edit') }}</h3><form method="POST" action="{{ route('events.type_edit') }}" class="frm-submit">@csrf<input type="hidden" name="type_id" id="edit_evtype_id">@if(is_superadmin_loggedin())<div class="mb-3"><label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">{{ translate('branch') }}</label><select name="branch_id" id="edit_evtype_branch" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-900 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white">@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>@endif<div class="mb-3"><label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">{{ translate('name') }}</label><input type="text" name="type_name" id="edit_evtype_name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-900 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white" required></div><div class="mb-3"><label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Icon</label><div class="flex flex-wrap gap-2">
  @foreach(['bullhorn'=>'fa-bullhorn','flag-checkered'=>'fa-flag-checkered','comments'=>'fa-comments','concierge-bell'=>'fa-concierge-bell','envelope'=>'fa-envelope','comment-alt'=>'fa-comment-alt','users'=>'fa-users','star'=>'fa-star','stamp'=>'fa-stamp','search'=>'fa-search'] as $val=>$cls)
    <label class="cursor-pointer"><input type="radio" name="event_icon" value="{{ $val }}" class="peer sr-only"><span class="grid size-9 place-items-center rounded-full border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-900 text-slate-700 dark:text-slate-300 peer-checked:border-[var(--primary)] peer-checked:bg-[var(--primary)]/10 peer-checked:text-[var(--primary)] dark:peer-checked:border-white dark:peer-checked:bg-white dark:peer-checked:text-black hover:border-slate-300 transition"><i class="fas {{ $cls }} text-sm"></i></span></label>
  @endforeach
</div></div><div class="flex justify-end gap-2"><button type="button" onclick="document.getElementById('editEvTypeModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm bg-white dark:bg-zinc-900 text-slate-700 dark:text-white">Cancel</button><button class="px-4 py-2 rounded-xl bg-[var(--primary)] text-white text-sm">Update</button></div></form></div></div>
@push('scripts')<script>function editEvType(id,name,icon,branch){ document.getElementById('edit_evtype_id').value=id; document.getElementById('edit_evtype_name').value=name; document.getElementById('edit_evtype_icon').value=icon; document.getElementById('edit_evtype_branch') && (document.getElementById('edit_evtype_branch').value=branch); document.getElementById('editEvTypeModal').classList.remove('hidden'); }</script>@endpush
@endsection
