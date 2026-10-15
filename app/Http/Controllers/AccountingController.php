<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\VoucherHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountingController extends Controller
{
    private function branchId(Request $r): int { return is_superadmin_loggedin() ? (int) ($r->input('branch_id') ?: get_loggedin_branch_id()) : (int) get_loggedin_branch_id(); }

    // index -> accounts list + create
    public function index(Request $request)
    {
        abort_unless(get_permission('accounting','is_view') || get_permission('account','is_view'),403);
        if($request->isMethod('post')){
            abort_unless(get_permission('account','is_add') || get_permission('accounting','is_add'),403);
            $branchId=$this->branchId($request);
            $request->validate([
                'branch_id'=> is_superadmin_loggedin()?['required']:['nullable'],
                'account_name'=>['required','string','max:255', function($a,$v,$fail) use($request){
                    $q=Account::where('name',$v);
                    if($request->input('account_id')) $q->where('id','!=',$request->input('account_id'));
                    if($q->exists()) $fail(translate('already_taken'));
                }],
                'opening_balance'=>['nullable','numeric'],
            ]);
            $data=['branch_id'=>$branchId,'name'=>$request->input('account_name'),'number'=>$request->input('account_number',''),'description'=>$request->input('description','')];
            if($request->input('account_id')){
                Account::where('id',$request->input('account_id'))->update($data);
                if(!is_superadmin_loggedin()) DB::table('transactions')->where('account_id',$request->input('account_id'))->update(['branch_id'=>$branchId]);
                set_alert('success', translate('information_has_been_updated_successfully'));
            } else {
                $data['balance']= $request->input('opening_balance',0) ?: 0;
                $acc = Account::create($data);
                if(($data['balance']??0)>0){
                    DB::table('transactions')->insert(['account_id'=>$acc->id,'voucher_head_id'=>0,'type'=>'deposit','amount'=>$data['balance'],'dr'=>0,'cr'=>$data['balance'],'bal'=>$data['balance'],'date'=>date('Y-m-d'),'description'=>'Opening Balance','branch_id'=>$branchId]);
                }
                set_alert('success', translate('information_has_been_saved_successfully'));
            }
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
            return redirect()->route('accounting.index');
        }
        $list = DB::table('accounts as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        $branches = is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        return view('accounting.index', ['title'=>translate('office_accounting'),'accountslist'=>$list,'branches'=>$branches]);
    }

    public function edit(Request $request, $id)
    {
        abort_unless(get_permission('account','is_edit'),403);
        $account = DB::table('accounts as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->where('t.id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->first();
        abort_if(!$account,404);
        if($request->isMethod('post')){
            $request->validate([
                'account_name'=>['required','string', function($a,$v,$fail) use($id){ $q=Account::where('name',$v)->where('id','!=',$id); if($q->exists()) $fail(translate('already_taken')); }],
                'opening_balance'=>['nullable','numeric'],
            ]);
            Account::where('id',$id)->update(['name'=>$request->input('account_name'),'number'=>$request->input('account_number',''),'description'=>$request->input('description','')]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('accounting.index')]);
            return redirect()->route('accounting.index');
        }
        $branches = is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        return view('accounting.edit', ['title'=>translate('office_accounting'),'account'=>(array)$account,'branches'=>$branches]);
    }

    public function delete(Request $request,$id){
        abort_unless(get_permission('account','is_delete'),403);
        $q=Account::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        if($q->delete()) DB::table('transactions')->where('account_id',$id)->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    // voucher_head
    public function voucherHead(Request $request)
    {
        if($request->isMethod('post')){
            abort_unless(get_permission('voucher_head','is_add'),403);
            if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
            $branchId=$this->branchId($request);
            $request->validate([
                'voucher_head'=>['required','string', function($a,$v,$fail) use($request,$branchId){
                    $q=VoucherHead::where('name',$v)->where('branch_id',$branchId);
                    if($request->input('voucher_head_id')) $q->where('id','!=',$request->input('voucher_head_id'));
                    if($q->exists()) $fail(translate('already_taken'));
                }],
                'type'=>['required'],
            ]);
            VoucherHead::create(['branch_id'=>$branchId,'name'=>$request->input('voucher_head'),'type'=>$request->input('type'),'system'=>0]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('accounting.voucher_head');
        }
        $list = DB::table('voucher_head as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->where('system',0)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->get();
        $branches = is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        return view('accounting.voucher_head', ['title'=>translate('voucher_head'),'productlist'=>$list,'branches'=>$branches]);
    }

    public function voucherHeadEdit(Request $request)
    {
        abort_unless(get_permission('voucher_head','is_edit'),403);
        $request->validate([
            'voucher_head'=>['required', function($a,$v,$fail) use($request){
                $branchId = is_superadmin_loggedin()? $request->input('branch_id'): get_loggedin_branch_id();
                $q=VoucherHead::where('name',$v)->where('branch_id',$branchId)->where('id','!=',$request->input('voucher_head_id'));
                if($q->exists()) $fail(translate('already_taken'));
            }],
        ]);
        VoucherHead::where('id',$request->input('voucher_head_id'))->update(['name'=>$request->input('voucher_head')]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('accounting.voucher_head')]);
        return redirect()->route('accounting.voucher_head');
    }

    public function voucherHeadDetails(Request $request){
        $id=$request->input('id');
        $row=VoucherHead::where('id',$id)->first();
        return response()->json($row);
    }

    public function voucherHeadDelete(Request $request,$id){
        abort_unless(get_permission('voucher_head','is_delete'),403);
        $q=VoucherHead::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function voucherDeposit(Request $request){
        abort_unless(get_permission('deposit','is_view') || get_permission('accounting','is_view'),403);
        $branchId=$this->branchId($request);
        $list = $this->getVoucherList('deposit');
        return view('accounting.voucher_deposit', ['title'=>translate('deposit'),'voucherlist'=>$list,'branch_id'=>$branchId,'accounts'=>Account::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get(),'voucherHeads'=>VoucherHead::where('type','income')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get()]);
    }

    public function voucherExpense(Request $request){
        abort_unless(get_permission('expense','is_view') || get_permission('accounting','is_view'),403);
        $branchId=$this->branchId($request);
        $list = $this->getVoucherList('expense');
        return view('accounting.voucher_expense', ['title'=>translate('expense'),'voucherlist'=>$list,'branch_id'=>$branchId,'accounts'=>Account::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get(),'voucherHeads'=>VoucherHead::where('type','expense')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get()]);
    }

    public function voucherSave(Request $request){
        $type=$request->input('voucher_type');
        if($type=='deposit') abort_unless(get_permission('deposit','is_add'),403);
        if($type=='expense') abort_unless(get_permission('expense','is_add'),403);
        if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
        $request->validate(['account_id'=>['required'],'voucher_head_id'=>['required'],'amount'=>['required','numeric'],'date'=>['required', function($a,$v,$fail){ if(strtotime($v) > strtotime(date('Y-m-d'))) $fail('Please Enter Correct Date'); }]]);
        $branchId=$this->branchId($request);
        $account = Account::where('id',$request->input('account_id'))->firstOrFail();
        $amount=(float)$request->input('amount');
        $cbal=(float)$account->balance;
        if($type=='deposit'){ $cr=$amount; $dr=0; $bal=$cbal+$amount; } else { $cr=0; $dr=$amount; $bal=$cbal-$amount; }
        $data=['account_id'=>$request->input('account_id'),'voucher_head_id'=>$request->input('voucher_head_id'),'type'=>$type,'ref'=>$request->input('ref_no',''),'amount'=>$amount,'dr'=>$dr,'cr'=>$cr,'bal'=>$bal,'date'=>date('Y-m-d',strtotime($request->input('date'))),'pay_via'=>$request->input('pay_via',''),'description'=>$request->input('description',''),'branch_id'=>$branchId];
        $id = DB::table('transactions')->insertGetId($data);
        $account->update(['balance'=>$bal]);
        if($request->hasFile('attachment_file')){
            $ext=$request->file('attachment_file')->getClientOriginalExtension();
            $fileName=$id.'.'.$ext;
            $request->file('attachment_file')->move(public_path('uploads/attachments/voucher'), $fileName);
            DB::table('transactions')->where('id',$id)->update(['attachments'=>$fileName]);
        }
        set_alert('success', translate('information_has_been_saved_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function voucherDepositEdit(Request $request,$id){
        abort_unless(get_permission('deposit','is_edit'),403);
        $row=DB::table('transactions')->where('id',$id)->first();
        abort_if(!$row,404);
        if($request->isMethod('post')){
            $request->validate(['voucher_head_id'=>['required'],'date'=>['required']]);
            DB::table('transactions')->where('id',$id)->update(['voucher_head_id'=>$request->input('voucher_head_id'),'ref'=>$request->input('ref_no',''),'date'=>date('Y-m-d',strtotime($request->input('date'))),'pay_via'=>$request->input('pay_via',''),'description'=>$request->input('description','')]);
            if($request->hasFile('attachment_file')){
                $ext=$request->file('attachment_file')->getClientOriginalExtension();
                $fileName=$id.'.'.$ext;
                $request->file('attachment_file')->move(public_path('uploads/attachments/voucher'), $fileName);
                DB::table('transactions')->where('id',$id)->update(['attachments'=>$fileName]);
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('accounting.deposit_voucher')]);
            return redirect()->route('accounting.deposit_voucher');
        }
        return view('accounting.voucher_deposit_edit', ['title'=>translate('deposit'),'deposit'=>(array)$row,'voucherHeads'=>VoucherHead::where('type','income')->get()]);
    }

    public function voucherExpenseEdit(Request $request,$id){
        abort_unless(get_permission('expense','is_edit'),403);
        $row=DB::table('transactions')->where('id',$id)->first();
        abort_if(!$row,404);
        if($request->isMethod('post')){
            $request->validate(['voucher_head_id'=>['required'],'date'=>['required']]);
            DB::table('transactions')->where('id',$id)->update(['voucher_head_id'=>$request->input('voucher_head_id'),'ref'=>$request->input('ref_no',''),'date'=>date('Y-m-d',strtotime($request->input('date'))),'pay_via'=>$request->input('pay_via',''),'description'=>$request->input('description','')]);
            if($request->hasFile('attachment_file')){
                $ext=$request->file('attachment_file')->getClientOriginalExtension();
                $fileName=$id.'.'.$ext;
                $request->file('attachment_file')->move(public_path('uploads/attachments/voucher'), $fileName);
                DB::table('transactions')->where('id',$id)->update(['attachments'=>$fileName]);
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('accounting.expense_voucher')]);
            return redirect()->route('accounting.expense_voucher');
        }
        return view('accounting.voucher_expense_edit', ['title'=>translate('expense'),'expense'=>(array)$row,'voucherHeads'=>VoucherHead::where('type','expense')->get()]);
    }

    public function voucherDelete(Request $request,$id){
        $q=DB::table('transactions')->where('id',$id)->first();
        if(!$q) return response()->json(['status'=>'fail']);
        if($q->type=='expense'){ abort_unless(get_permission('expense','is_delete'),403); DB::table('accounts')->where('id',$q->account_id)->increment('balance',$q->amount); }
        elseif($q->type=='deposit'){ abort_unless(get_permission('deposit','is_delete'),403); DB::table('accounts')->where('id',$q->account_id)->decrement('balance',$q->amount); }
        if(!empty($q->attachments) && file_exists(public_path('uploads/attachments/voucher/'.$q->attachments))) @unlink(public_path('uploads/attachments/voucher/'.$q->attachments));
        DB::table('transactions')->where('id',$id)->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    private function getVoucherList($type=''){
        $q=DB::table('transactions')->leftJoin('accounts','accounts.id','=','transactions.account_id')->leftJoin('voucher_head','voucher_head.id','=','transactions.voucher_head_id')->leftJoin('payment_types','payment_types.id','=','transactions.pay_via')->select('transactions.*','accounts.name as ac_name','voucher_head.name as v_head','payment_types.name as via_name');
        if(!empty($type)) $q->where('transactions.type',$type);
        if(!is_superadmin_loggedin()) $q->where('transactions.branch_id',get_loggedin_branch_id());
        return $q->get();
    }

    public function accountStatement(Request $request){
        abort_unless(get_permission('accounting_reports','is_view'),403);
        $results=null; $daterange=null;
        if($request->isMethod('post')){
            $request->validate(['account_id'=>['required'],'daterange'=>['required']]);
            $parts=explode(' - ',$request->input('daterange'));
            $start=date('Y-m-d',strtotime($parts[0])); $end=date('Y-m-d',strtotime($parts[1]));
            $type=$request->input('type','all');
            $q=DB::table('transactions')->leftJoin('voucher_head','voucher_head.id','=','transactions.voucher_head_id')->where('transactions.account_id',$request->input('account_id'))->where('transactions.date','>=',$start)->where('transactions.date','<=',$end);
            if($type!='all') $q->where('transactions.type',$type);
            $results=$q->orderBy('transactions.id')->select('transactions.*','voucher_head.name as v_head')->get();
            $daterange=$parts;
        }
        return view('accounting.account_statement', ['title'=>translate('financial_reports'),'results'=>$results,'daterange'=>$daterange,'accounts'=>Account::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get()]);
    }

    public function incomeReport(Request $request){
        abort_unless(get_permission('accounting_reports','is_view'),403);
        $results=null; $daterange=null;
        if($request->isMethod('post')){
            $parts=explode(' - ',$request->input('daterange'));
            $start=date('Y-m-d',strtotime($parts[0])); $end=date('Y-m-d',strtotime($parts[1]));
            $branchId = is_superadmin_loggedin()? $request->input('branch_id') ?? get_loggedin_branch_id() : get_loggedin_branch_id();
            $results=DB::table('transactions')->leftJoin('accounts','accounts.id','=','transactions.account_id')->leftJoin('voucher_head','voucher_head.id','=','transactions.voucher_head_id')->leftJoin('payment_types','payment_types.id','=','transactions.pay_via')->where('transactions.type','deposit')->where('transactions.branch_id',$branchId)->where('transactions.date','>=',$start)->where('transactions.date','<=',$end)->orderBy('transactions.id')->select('transactions.*','accounts.name as ac_name','voucher_head.name as v_head','payment_types.name as via_name')->get();
            $daterange=$parts;
        }
        return view('accounting.income_report', ['title'=>translate('financial_reports'),'results'=>$results,'daterange'=>$daterange]);
    }

    public function expenseReport(Request $request){
        abort_unless(get_permission('accounting_reports','is_view'),403);
        $results=null; $daterange=null;
        if($request->isMethod('post')){
            $parts=explode(' - ',$request->input('daterange'));
            $start=date('Y-m-d',strtotime($parts[0])); $end=date('Y-m-d',strtotime($parts[1]));
            $branchId = is_superadmin_loggedin()? $request->input('branch_id') ?? get_loggedin_branch_id() : get_loggedin_branch_id();
            $results=DB::table('transactions')->leftJoin('accounts','accounts.id','=','transactions.account_id')->leftJoin('voucher_head','voucher_head.id','=','transactions.voucher_head_id')->leftJoin('payment_types','payment_types.id','=','transactions.pay_via')->where('transactions.type','expense')->where('transactions.branch_id',$branchId)->where('transactions.date','>=',$start)->where('transactions.date','<=',$end)->orderBy('transactions.id')->select('transactions.*','accounts.name as ac_name','voucher_head.name as v_head','payment_types.name as via_name')->get();
            $daterange=$parts;
        }
        return view('accounting.expense_report', ['title'=>translate('financial_reports'),'results'=>$results,'daterange'=>$daterange]);
    }

    public function balanceSheet(Request $request){
        abort_unless(get_permission('accounting_reports','is_view'),403);
        $branchId = is_superadmin_loggedin()? ($request->input('branch_id') ?? get_loggedin_branch_id()) : get_loggedin_branch_id();
        $results = DB::table('accounts')->leftJoin('transactions','transactions.account_id','=','accounts.id')->select('transactions.*',DB::raw('IFNULL(SUM(transactions.dr),0) as total_dr'),DB::raw('IFNULL(SUM(transactions.cr),0) as total_cr'),'accounts.name as ac_name','accounts.balance as fbalance')->where('accounts.branch_id',$branchId)->groupBy('accounts.id')->orderBy('accounts.balance','DESC')->get();
        return view('accounting.balance_sheet', ['title'=>translate('financial_reports'),'results'=>$results,'branch_id'=>$branchId]);
    }

    public function getVoucherHead(Request $request){
        $branchId=$this->branchId($request);
        $type=$request->input('type');
        if(empty($branchId)) return response('<option value="">'.translate('select_branch_first').'</option>');
        $result=VoucherHead::where('branch_id',$branchId)->where('type',$type)->get(['id','name']);
        if($result->count()){
            $html="<option value=''>".translate('select')."</option>";
            foreach($result as $row) $html.='<option value="'.$row->id.'">'.$row->name.'</option>';
            return response($html);
        }
        return response('<option value="">'.translate('no_information_available').'</option>');
    }
}
