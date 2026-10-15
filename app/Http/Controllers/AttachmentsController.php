<?php
namespace App\Http\Controllers;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AttachmentsController extends Controller
{
    private function resolveBranchId(Request $request): int
    {
        if(is_superadmin_loggedin()){ $bid=$request->input('branch_id'); if($bid) return (int)$bid; return (int)(DB::table('branch')->value('id') ?? 0);}
        return (int)get_loggedin_branch_id();
    }
    public function index(Request $request)
    {
        abort_unless(get_permission('attachments','is_view'),403);
        $branchId=$this->resolveBranchId($request);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $q=DB::table('attachments as a')->leftJoin('attachments_type as at','at.id','=','a.type_id')->leftJoin('class as c','c.id','=','a.class_id')->leftJoin('branch as b','b.id','=','a.branch_id')->leftJoin('subject as s','s.id','=','a.subject_id')->select('a.*','b.name as branch_name','at.name as type_name','c.name as class_name','s.name as subject_name');
        if(!is_superadmin_loggedin()) $q->where('a.branch_id', get_loggedin_branch_id());
        // student/parent filter simplified
        $attachmentss=$q->orderBy('a.id','desc')->get();
        $types=DB::table('attachments_type')->when(!is_superadmin_loggedin(), fn($qq)=>$qq->where('branch_id', get_loggedin_branch_id()))->get();
        $classes=$branchId?DB::table('class')->where('branch_id',$branchId)->get():collect();
        return view('attachments.index',['title'=>translate('upload_content'),'attachmentss'=>$attachmentss,'branches'=>$branches,'branch_id'=>$branchId,'classes'=>$classes,'types'=>$types]);
    }
    public function edit(int $id)
    {
        abort_unless(get_permission('attachments','is_edit'),403);
        $data=DB::table('attachments')->where('id',$id)->first();
        abort_if(!$data,404);
        if($data->uploader_id != get_loggedin_user_id() && !is_superadmin_loggedin()){
            set_alert('error','You do not have permission to edit');
            return redirect()->route('attachments.index');
        }
        $branchId=$data->branch_id;
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $types=DB::table('attachments_type')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->get();
        $classes=DB::table('class')->where('branch_id',$branchId)->get();
        return view('attachments.edit',['title'=>translate('upload_content'),'data'=>(array)$data,'branches'=>$branches,'types'=>$types,'classes'=>$classes]);
    }
    public function save(Request $request)
    {
        $isEdit=$request->has('attachment_id');
        abort_unless(get_permission('attachments', $isEdit?'is_edit':'is_add'),403);
        $rules=['title'=>['required'],'type_id'=>['required','integer'],'date'=>['required','date']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        if(!$request->has('all_class_set')) $rules['class_id']=['required'];
        if(!$request->has('subject_wise') && !$request->has('all_class_set')) $rules['subject_id']=['required'];
        if($request->hasFile('attachment_file')) $rules['attachment_file']=['file','max:2048','mimes:pdf,doc,xls,docx,xlsx,txt,jpg,jpeg,png,gif,bmp'];
        else if(!$isEdit) $rules['attachment_file']=['required','file'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $classID=$request->has('all_class_set')?'unfiltered':$validated['class_id'] ?? $request->input('class_id');
        $subjectID=($request->has('all_class_set')||$request->has('subject_wise'))?'unfiltered':($validated['subject_id']??$request->input('subject_id'));
        $arrayData=['branch_id'=>$branchId,'title'=>$validated['title'],'type_id'=>$validated['type_id'],'remarks'=>$request->input('remarks',''),'date'=>date("Y-m-d",strtotime($validated['date'])),'session_id'=>get_session_id(),'uploader_id'=>get_loggedin_user_id(),'class_id'=>$classID,'subject_id'=>$subjectID];
        if(!$isEdit){
            if($request->hasFile('attachment_file')){
                $file=$request->file('attachment_file');
                $enc=uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/attachments'), $enc);
                $arrayData['file_name']=$file->getClientOriginalName();
                $arrayData['enc_name']=$enc;
                DB::table('attachments')->insert($arrayData);
            }
        } else {
            $id=$request->input('attachment_id');
            if($request->hasFile('attachment_file')){
                $old=DB::table('attachments')->where('id',$id)->value('enc_name');
                if($old && file_exists(public_path('uploads/attachments/'.$old))) unlink(public_path('uploads/attachments/'.$old));
                $file=$request->file('attachment_file');
                $enc=uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/attachments'), $enc);
                $arrayData['file_name']=$file->getClientOriginalName();
                $arrayData['enc_name']=$enc;
            }
            $q=DB::table('attachments')->where('id',$id);
            if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
            $q->update($arrayData);
        }
        set_alert('success', translate('information_has_been_saved_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('attachments.index')]);
        return redirect()->route('attachments.index');
    }
    public function delete(Request $request,int $id)
    {
        abort_unless(get_permission('attachments','is_delete'),403);
        $row=DB::table('attachments')->where('id',$id)->first();
        if(!$row) return response()->json(['status'=>'fail'],404);
        $q=DB::table('attachments')->where('id',$id);
        if(!is_superadmin_loggedin()){ $q->where('branch_id', get_loggedin_branch_id())->where('uploader_id', get_loggedin_user_id()); }
        $deleted=$q->delete();
        if($deleted && $row->enc_name && file_exists(public_path('uploads/attachments/'.$row->enc_name))) unlink(public_path('uploads/attachments/'.$row->enc_name));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }
    public function download(Request $request)
    {
        $enc=$request->query('file');
        $row=DB::table('attachments')->where('enc_name',$enc)->first();
        abort_if(!$row,404);
        $path=public_path('uploads/attachments/'.$enc);
        abort_if(!file_exists($path),404);
        return response()->download($path, $row->file_name);
    }
    public function type(Request $request)
    {
        abort_unless(get_permission('attachment_type','is_view'),403);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $typelist=DB::table('attachments_type as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id', get_loggedin_branch_id()))->orderBy('t.id','asc')->get();
        return view('attachments.type',['title'=>translate('attachment_type'),'typelist'=>$typelist,'branches'=>$branches]);
    }
    public function typeStore(Request $request)
    {
        abort_unless(get_permission('attachment_type','is_add'),403);
        $rules=['type_name'=>['required','string','max:255']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $exists=DB::table('attachments_type')->where('name',$validated['type_name'])->where('branch_id',$branchId)->exists();
        if($exists) return back()->withErrors(['type_name'=>translate('already_taken')])->withInput();
        DB::table('attachments_type')->insert(['name'=>$validated['type_name'],'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('attachments.type');
    }
    public function typeEdit(Request $request)
    {
        abort_unless(get_permission('attachment_type','is_edit'),403);
        $rules=['type_id'=>['required','integer','exists:attachments_type,id'],'type_name'=>['required','string','max:255']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $exists=DB::table('attachments_type')->where('name',$validated['type_name'])->where('branch_id',$branchId)->where('id','!=',$validated['type_id'])->exists();
        if($exists){
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'fail','error'=>['type_name'=>translate('already_taken')]]);
            return back()->withErrors(['type_name'=>translate('already_taken')]);
        }
        $q=DB::table('attachments_type')->where('id',$validated['type_id']);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->update(['name'=>$validated['type_name'],'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('attachments.type')]);
        return redirect()->route('attachments.type');
    }
    public function typeDelete(Request $request,int $id)
    {
        abort_unless(get_permission('attachment_type','is_delete'),403);
        $q=DB::table('attachments_type')->where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }
}
