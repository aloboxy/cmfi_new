<?php
namespace App\Http\Controllers;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CommunicationController extends Controller
{
    private function resolveBranchId(Request $request): ?int
    {
        if(is_superadmin_loggedin()){ $bid=$request->input('branch_id'); if($bid) return (int)$bid; return (int)(DB::table('branch')->value('id') ?? 0);}
        return (int)get_loggedin_branch_id();
    }
    public function mailbox(Request $request,string $action='inbox')
    {
        $activeUser=loggedin_role_id().'-'.get_loggedin_user_id();
        $branchId=$this->resolveBranchId($request);
        $inside='message_inbox';
        if($action=='compose') $inside='message_compose';
        elseif($action=='inbox') $inside='message_inbox';
        elseif($action=='sent') $inside='message_sent';
        elseif($action=='important') $inside='message_important';
        elseif($action=='trash') $inside='message_trash';
        elseif($action=='read'){
            $id=$request->query('id');
            // mark read
            DB::table('message')->where('reciever',$activeUser)->where('id',$id)->update(['read_status'=>1]);
            DB::table('message')->where('sender',$activeUser)->where('id',$id)->update(['reply_status'=>0]);
            $inside='message_read';
            $messageId=$id;
        }
        return view('communication.message',['title'=>translate('mailbox'),'inside_subview'=>$inside,'active_user'=>$activeUser,'branch_id'=>$branchId,'message_id'=>$messageId ?? $request->query('id')]);
    }
    public function messageSend(Request $request)
    {
        $rules=['role_id'=>['required'],'receiver_id'=>['required'],'subject'=>['required'],'message_body'=>['required']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required'];
        if($request->hasFile('attachment_file')) $rules['attachment_file']=['file','max:2048','mimes:pdf,csv,doc,xls,docx,xlsx,jpg,jpeg,png,gif,bmp'];
        $validated=$request->validate($rules);
        $sender=loggedin_role_id().'-'.get_loggedin_user_id();
        $reciever=$validated['role_id'].'-'.$validated['receiver_id'];
        $arrayMsg=['body'=>$validated['message_body'],'subject'=>$validated['subject'],'sender'=>$sender,'reciever'=>$reciever];
        if($request->hasFile('attachment_file')){
            $file=$request->file('attachment_file');
            $enc=uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/attachments'), $enc);
            $arrayMsg['file_name']=$file->getClientOriginalName();
            $arrayMsg['enc_name']=$enc;
        }
        DB::table('message')->insert($arrayMsg);
        set_alert('success', translate('message_sent_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return redirect()->route('communication.messages',['action'=>'sent']);
    }
    public function messageReply(Request $request)
    {
        $request->validate(['message_id'=>['required','integer','exists:message,id'],'message'=>['required']]);
        $messageId=$request->input('message_id');
        if($request->input('user_identity')=='sender'){
            DB::table('message')->where('id',$messageId)->update(['read_status'=>0]);
            $identity=1;
        } else {
            DB::table('message')->where('id',$messageId)->update(['reply_status'=>1]);
            $identity=0;
        }
        $arrayMsg=['message_id'=>$messageId,'body'=>$request->input('message'),'identity'=>$identity];
        if($request->hasFile('attachment_file')){
            $file=$request->file('attachment_file');
            $enc=uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/attachments'), $enc);
            $arrayMsg['file_name']=$file->getClientOriginalName();
            $arrayMsg['enc_name']=$enc;
        }
        DB::table('message_reply')->insert($arrayMsg);
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }
    public function download(Request $request)
    {
        $enc=$request->query('file'); $type=$request->query('type');
        $table=$type=='reply'?'message_reply':'message';
        $row=DB::table($table)->where('enc_name',$enc)->first();
        abort_if(!$row,404);
        $path=public_path('uploads/attachments/'.$enc);
        abort_if(!file_exists($path),404);
        return response()->download($path, $row->file_name);
    }
    public function deleteMail(Request $request)
    {
        $arrayID=$request->input('arrayID',[]); $mode=$request->input('mode','inbox');
        foreach($arrayID as $value){
            DB::table('message')->where('id',$value)->update(['trash_'.$mode=>1]);
        }
        set_alert('success', translate('message_has_been_deleted'));
        return response()->json(['status'=>'success']);
    }
    public function setFavourite(Request $request)
    {
        $messageID=$request->input('messageID'); $status=$request->input('status');
        $activeUser=loggedin_role_id().'-'.get_loggedin_user_id();
        $query=DB::table('message')->where('id',$messageID)->first();
        if($activeUser==$query->sender) $data['fav_sent']=($status=='false'?0:1);
        elseif($activeUser==$query->reciever) $data['fav_inbox']=($status=='false'?0:1);
        else $data=[];
        if($data) DB::table('message')->where('id',$messageID)->update($data);
        return response()->json(['msg'=>translate('information_has_been_updated_successfully'),'status'=>true]);
    }
    public function trashObserve(Request $request)
    {
        $arrayID=$request->input('array_id',[]); $mode=$request->input('mode','restore');
        $status=match($mode){'restore'=>0,'delete'=>1,'forever'=>2, default=>0};
        foreach($arrayID as $id){
            $get=DB::table('message')->where('id',$id)->first();
            if(!$get) continue;
            $activeUser=loggedin_role_id().'-'.get_loggedin_user_id();
            if($get->sender==$activeUser) $array['trash_sent']=$status;
            elseif($get->reciever==$activeUser) $array['trash_inbox']=$status;
            else continue;
            DB::table('message')->where('id',$id)->update($array);
        }
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }
    public function getStafflistRole(Request $request)
    {
        $branchId=$this->resolveBranchId($request);
        $roleId=$request->input('role_id');
        $result=DB::table('staff')->join('login_credential as lc', function($j){ $j->on('lc.user_id','=','staff.id')->whereNotIn('lc.role',[6,7]); })->where('lc.role',$roleId)->where('staff.branch_id',$branchId)->orderBy('staff.id','asc')->select('staff.id','staff.name','staff.staff_id')->get();
        if($result->isEmpty()) return response('<option value="">'.translate('no_information_available').'</option>');
        $html="<option value=''>".translate('select')."</option>";
        foreach($result as $staff){
            if($staff->id==get_loggedin_user_id()) continue;
            $html.="<option value='".$staff->id."'>".e($staff->name)." (".e($staff->staff_id).")</option>";
        }
        return response($html);
    }
    public function getStudentByClass(Request $request)
    {
        $classId=$request->input('class_id'); $branchId=$this->resolveBranchId($request);
        $result=DB::table('enroll as e')->join('student as s','s.id','=','e.student_id')->join('login_credential as l', function($j){ $j->on('l.user_id','=','e.student_id')->where('l.role',7); })->where('l.active',1)->where('e.class_id',$classId)->where('e.branch_id',$branchId)->select('e.student_id','e.roll', DB::raw('CONCAT(s.first_name," ",s.last_name) as fullname'))->get();
        if($result->isEmpty()) return response('<option value="">'.translate('no_information_available').'</option>');
        $html="<option value=''>".translate('select')."</option>";
        foreach($result as $row){
            if($row->student_id==get_loggedin_user_id()) continue;
            $html.='<option value="'.$row->student_id.'">'.e($row->fullname).' ( Roll : '.e($row->roll).')</option>';
        }
        return response($html);
    }
    public function getParentListBranch(Request $request)
    {
        $branchId=$this->resolveBranchId($request);
        $result=DB::table('parent')->where('branch_id',$branchId)->orderBy('id')->select('id','name')->get();
        if($result->isEmpty()) return response('<option value="">'.translate('no_information_available').'</option>');
        $html="<option value=''>".translate('select')."</option>";
        foreach($result as $row){
            if($row->id==get_loggedin_user_id()) continue;
            $html.="<option value='".$row->id."'>".e($row->name)."</option>";
        }
        return response($html);
    }
}
