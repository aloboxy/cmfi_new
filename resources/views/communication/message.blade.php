@extends('layouts.app')
@section('title', translate('mailbox'))
@section('content')
<div class="space-y-5">
 <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border"><x-heroicon-o-home class="size-4"/></a><span>/</span><span class="font-semibold text-slate-800">{{ translate('mailbox') }}</span></div>
 <div class="flex gap-2 flex-wrap">
  <a href="{{ route('communication.messages',['action'=>'inbox']) }}" class="rounded-xl px-4 py-2 text-sm font-medium border {{ ($inside_subview=='message_inbox') ? 'bg-[#2563eb] text-white' : 'bg-white text-slate-600' }}">{{ translate('inbox') }}</a>
  <a href="{{ route('communication.messages',['action'=>'sent']) }}" class="rounded-xl px-4 py-2 text-sm font-medium border {{ ($inside_subview=='message_sent') ? 'bg-[#2563eb] text-white' : 'bg-white text-slate-600' }}">{{ translate('sent') }}</a>
  <a href="{{ route('communication.messages',['action'=>'compose']) }}" class="rounded-xl px-4 py-2 text-sm font-medium border {{ ($inside_subview=='message_compose') ? 'bg-[#2563eb] text-white' : 'bg-white text-slate-600' }}">{{ translate('compose') }}</a>
  <a href="{{ route('communication.messages',['action'=>'important']) }}" class="rounded-xl px-4 py-2 text-sm font-medium border {{ ($inside_subview=='message_important') ? 'bg-[#2563eb] text-white' : 'bg-white text-slate-600' }}">{{ translate('important') }}</a>
  <a href="{{ route('communication.messages',['action'=>'trash']) }}" class="rounded-xl px-4 py-2 text-sm font-medium border {{ ($inside_subview=='message_trash') ? 'bg-[#2563eb] text-white' : 'bg-white text-slate-600' }}">{{ translate('trash') }}</a>
 </div>
 <div class="rounded-2xl bg-white border p-5">
 @if($inside_subview=='message_compose')
  <h3 class="font-bold mb-4">{{ translate('compose') }}</h3>
  <form method="POST" action="{{ route('communication.message_send') }}" enctype="multipart/form-data" class="space-y-4">@csrf
   @if(is_superadmin_loggedin())<div><label class="block text-sm font-medium mb-1">{{ translate('branch') }}</label><select name="branch_id" id="msg_branch" class="w-full rounded-xl border px-3.5 py-2.5 text-sm"><option value="">{{ translate('select') }}</option>@php $branches=DB::table('branch')->orderBy('name')->get(); @endphp @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>@endif
   <div class="grid grid-cols-2 gap-3"><div><label class="block text-sm font-medium mb-1">{{ translate('role') }} <span class="text-red-500">*</span></label><select name="role_id" id="msg_role" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required><option value="">{{ translate('select') }}</option>@php $roles=DB::table('roles')->whereNotIn('id',[1])->get(); @endphp @foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div><div><label class="block text-sm font-medium mb-1">{{ translate('receiver') }} <span class="text-red-500">*</span></label><select name="receiver_id" id="msg_receiver" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required><option value="">{{ translate('select') }}</option></select></div></div>
   <div><label class="block text-sm font-medium mb-1">{{ translate('subject') }} <span class="text-red-500">*</span></label><input type="text" name="subject" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required></div>
   <div><label class="block text-sm font-medium mb-1">{{ translate('message') }} <span class="text-red-500">*</span></label><textarea name="message_body" rows="4" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required></textarea></div>
   <div><label class="block text-sm font-medium mb-1">{{ translate('attachment') }}</label><input type="file" name="attachment_file" class="w-full rounded-xl border px-3.5 py-2.5 text-sm"></div>
   <div class="flex justify-end"><button class="rounded-xl bg-[#2563eb] text-white px-6 py-2.5 text-sm font-semibold">{{ translate('send') }}</button></div>
  </form>
 @elseif($inside_subview=='message_read')
  @php $msg=DB::table('message')->where('id',$message_id)->first(); $replies=DB::table('message_reply')->where('message_id',$message_id)->orderBy('id','asc')->get(); @endphp
  @if($msg)
   <h3 class="font-bold mb-2">{{ $msg->subject }}</h3><p class="text-sm text-slate-600 mb-4">{!! nl2br(e($msg->body)) !!}</p>
   @if($msg->enc_name)<a href="{{ route('communication.download',['file'=>$msg->enc_name,'type'=>'']) }}" class="text-sm text-[#2563eb]">Download: {{ $msg->file_name }}</a>@endif
   <div class="mt-6 space-y-3">@foreach($replies as $rep)<div class="rounded-xl border p-3 bg-slate-50"><p class="text-sm">{{ $rep->body }}</p>@if($rep->enc_name)<a href="{{ route('communication.download',['file'=>$rep->enc_name,'type'=>'reply']) }}" class="text-xs text-[#2563eb]">{{ $rep->file_name }}</a>@endif</div>@endforeach</div>
   <form method="POST" action="{{ route('communication.message_reply') }}" enctype="multipart/form-data" class="mt-6 space-y-3">@csrf<input type="hidden" name="message_id" value="{{ $message_id }}"><input type="hidden" name="user_identity" value="{{ $msg->sender==$active_user ? 'sender' : 'receiver' }}"><textarea name="message" rows="3" class="w-full rounded-xl border px-3.5 py-2.5 text-sm" required placeholder="Reply..."></textarea><input type="file" name="attachment_file" class="w-full rounded-xl border px-3.5 py-2.5 text-sm"><div class="flex justify-end"><button class="rounded-xl bg-[#2563eb] text-white px-6 py-2 text-sm">{{ translate('reply') }}</button></div></form>
  @else <p class="text-slate-500">Message not found.</p> @endif
 @else
  @php
   $activeUser=$active_user; $action=str_replace('message_','',$inside_subview);
   if($action=='inbox') $list=DB::table('message')->where('reciever',$activeUser)->where('trash_inbox',0)->orderBy('id','desc')->get();
   elseif($action=='sent') $list=DB::table('message')->where('sender',$activeUser)->where('trash_sent',0)->orderBy('id','desc')->get();
   elseif($action=='important') $list=DB::table('message')->where(function($q) use($activeUser){ $q->where('sender',$activeUser)->where('fav_sent',1)->orWhere(function($q2) use($activeUser){ $q2->where('reciever',$activeUser)->where('fav_inbox',1); }); })->orderBy('id','desc')->get();
   elseif($action=='trash') $list=DB::table('message')->where(function($q) use($activeUser){ $q->where('sender',$activeUser)->where('trash_sent',1)->orWhere(function($q2) use($activeUser){ $q2->where('reciever',$activeUser)->where('trash_inbox',1); }); })->orderBy('id','desc')->get();
   else $list=collect();
  @endphp
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-slate-50 text-xs uppercase text-slate-500"><th class="px-4 py-3">{{ translate('subject') }}</th><th class="px-4 py-3">{{ translate('from') }}</th><th class="px-4 py-3">{{ translate('date') }}</th><th class="px-4 py-3 text-right">{{ translate('action') }}</th></tr></thead><tbody class="divide-y">
   @forelse($list as $row)<tr class="hover:bg-slate-50"><td class="px-4 py-3 font-medium"><a href="{{ route('communication.messages',['action'=>'read']) }}?id={{ $row->id }}" class="hover:text-[#2563eb]">{{ $row->subject }}</a></td><td class="px-4 py-3">{{ $row->sender }}</td><td class="px-4 py-3">{{ $row->created_at }}</td><td class="px-4 py-3 text-right"><a href="{{ route('communication.messages',['action'=>'read']) }}?id={{ $row->id }}" class="text-[#2563eb] text-xs">Read</a></td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-slate-500">{{ translate('no_information_available') }}</td></tr>@endforelse
  </tbody></table></div>
 @endif
 </div>
</div>
@push('scripts')<script>
document.getElementById('msg_role')?.addEventListener('change', function(){
 const role=this.value; const branch=document.getElementById('msg_branch')?.value || '';
 if(role==7){ const cls=prompt('Enter class_id for student lookup (or leave empty)'); if(cls) fetch('{{ route('communication.getStudentByClass') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({class_id: cls})}).then(r=>r.text()).then(html=>{ document.getElementById('msg_receiver').innerHTML=html; }); }
 else if(role==6){ fetch('{{ route('communication.getParentListBranch') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({})}).then(r=>r.text()).then(html=>{ document.getElementById('msg_receiver').innerHTML=html; }); }
 else { fetch('{{ route('communication.getStafflistRole') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify({role_id: role})}).then(r=>r.text()).then(html=>{ document.getElementById('msg_receiver').innerHTML=html; }); }
});
</script>@endpush
@endsection
