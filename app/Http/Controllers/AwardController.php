<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AwardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(get_permission('award','is_view'),403, translate('access_denied'));
        if ($request->isMethod('post')) {
            abort_unless(get_permission('award','is_add'),403);
            $validated=$this->validateAward($request);
            $data=$request->all();
            $this->saveAward($data);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()) return response()->json(['status'=>'success','url'=>route('award.index')]);
            return redirect()->route('award.index');
        }
        $awardlist = $this->getList();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $roles = DB::table('roles')->whereNotIn('id',[1,6])->pluck('name','id'); // legacy getRoles([1,6]) excludes superadmin and parent? But include others
        // Actually original: getRoles([1,6]) - allow all except 1,6? So keep all except superadmin and parent
        $rolesAll = DB::table('roles')->whereNotIn('id',[1])->pluck('name','id');
        return view('award.index', compact('awardlist','branches','roles','rolesAll'));
    }

    public function edit($id, Request $request)
    {
        abort_unless(get_permission('award','is_edit'),403);
        $award = $this->getList($id,true);
        abort_if(!$award,404);
        if ($request->isMethod('post')) {
            $validated=$this->validateAward($request);
            $data=$request->all();
            $data['award_id']=$id;
            $this->saveAward($data);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()) return response()->json(['status'=>'success','url'=>route('award.index')]);
            return redirect()->route('award.index');
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $roles = DB::table('roles')->whereNotIn('id',[1])->pluck('name','id');
        return view('award.edit', compact('award','branches','roles'));
    }

    public function delete(Request $request, $id)
    {
        abort_unless(get_permission('award','is_delete'),403);
        $q=DB::table('award')->where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('award.index');
    }

    private function getList($id='', $row=false)
    {
        $q=DB::table('award')->select('award.*','roles.name as role_name')->leftJoin('roles','roles.id','=','award.role_id');
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->where('session_id', get_session_id());
        if($row==false) return $q->orderBy('award.id','desc')->get();
        $q->where('award.id',$id);
        return $q->first() ? (array)$q->first() : null;
    }

    private function saveAward($data)
    {
        $insertData = [
            'name'=> $data['award_name'],
            'user_id'=> $data['user_id'],
            'role_id'=> $data['role_id'],
            'gift_item'=> $data['gift_item'],
            'award_amount'=> $data['cash_price'] ?? 0,
            'award_reason'=> $data['award_reason'],
            'given_date'=> date('Y-m-d', strtotime($data['given_date'])),
            'session_id'=> get_session_id(),
            'branch_id'=> $this->resolveBranchId(request()),
        ];
        $award_id = $data['award_id'] ?? null;
        if (empty($award_id)) {
            DB::table('award')->insert($insertData);
        } else {
            DB::table('award')->where('id',$award_id)->update($insertData);
        }
    }

    private function validateAward(Request $request)
    {
        $rules=[
            'role_id'=>['required','integer'],
            'user_id'=>['required','integer'],
            'award_name'=>['required','string','max:255'],
            'gift_item'=>['required','string','max:255'],
            'award_reason'=>['required','string'],
            'given_date'=>['required','date'],
            'cash_price'=>['nullable','numeric'],
        ];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer'];
        if($request->input('role_id')==7) $rules['class_id']=['required'];
        return $request->validate($rules);
    }

    private function resolveBranchId(Request $request): int
    {
        if(is_superadmin_loggedin()){
            $bid=$request->input('branch_id');
            if(!empty($bid)) return (int)$bid;
            return (int)(get_loggedin_branch_id() ?? 0);
        }
        return (int)get_loggedin_branch_id();
    }
}
