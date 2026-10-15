<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibraryController extends Controller
{
    private function branchId(Request $r): int { return is_superadmin_loggedin()? (int)($r->input('branch_id')?:get_loggedin_branch_id()): (int)get_loggedin_branch_id(); }

    public function book(Request $request)
    {
        abort_unless(get_permission('book','is_view'),403);
        if($request->isMethod('post')){
            abort_unless(get_permission('book','is_add'),403);
            $request->validate([
                'branch_id'=> is_superadmin_loggedin()?['required']:['nullable'],
                'book_title'=>['required','string'],
                'purchase_date'=>['required'],
                'category_id'=>['required','integer','exists:book_category,id'],
                'publisher'=>['required'],
                'price'=>['required','numeric'],
                'total_stock'=>['required'],
            ]);
            $branchId=$this->branchId($request);
            $data=['branch_id'=>$branchId,'title'=>$request->input('book_title'),'isbn_no'=>$request->input('isbn_no',''),'author'=>$request->input('author',''),'edition'=>$request->input('edition',''),'purchase_date'=>date('Y-m-d',strtotime($request->input('purchase_date'))),'category_id'=>$request->input('category_id'),'publisher'=>$request->input('publisher'),'description'=>$request->input('description',''),'price'=>$request->input('price'),'total_stock'=>$request->input('total_stock'),'issued_copies'=>0];
            if($request->hasFile('cover_image')){
                $request->validate(['cover_image'=>['image','mimes:jpg,png','max:2048']]);
                $fileName='cover_image_'.bin2hex(random_bytes(8)).'.'.$request->file('cover_image')->getClientOriginalExtension();
                $request->file('cover_image')->move(public_path('uploads/book_cover'), $fileName);
                $data['cover']=$fileName;
            }
            Book::create($data);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('library.book')]);
            return redirect()->route('library.book');
        }
        $list=DB::table('book as t')->leftJoin('branch as b','b.id','=','t.branch_id')->leftJoin('book_category as c','c.id','=','t.category_id')->select('t.*','b.name as branch_name','c.name as category_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        $branches=is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        $categories=BookCategory::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        return view('library.book', ['title'=>translate('books'),'booklist'=>$list,'branches'=>$branches,'categories'=>$categories]);
    }

    public function bookEdit(Request $request,$id)
    {
        abort_unless(get_permission('book','is_edit'),403);
        $book=Book::findOrFail($id);
        if($request->isMethod('post')){
            $request->validate([
                'book_title'=>['required'],'purchase_date'=>['required'],'category_id'=>['required'],'publisher'=>['required'],'price'=>['required','numeric'],'total_stock'=>['required'],
            ]);
            $data=['title'=>$request->input('book_title'),'isbn_no'=>$request->input('isbn_no',''),'author'=>$request->input('author',''),'edition'=>$request->input('edition',''),'purchase_date'=>date('Y-m-d',strtotime($request->input('purchase_date'))),'category_id'=>$request->input('category_id'),'publisher'=>$request->input('publisher'),'description'=>$request->input('description',''),'price'=>$request->input('price'),'total_stock'=>$request->input('total_stock')];
            if(is_superadmin_loggedin() && $request->has('branch_id')) $data['branch_id']=$request->input('branch_id');
            if($request->hasFile('cover_image')){
                if(!empty($book->cover) && file_exists(public_path('uploads/book_cover/'.$book->cover))) @unlink(public_path('uploads/book_cover/'.$book->cover));
                $fileName='cover_image_'.bin2hex(random_bytes(8)).'.'.$request->file('cover_image')->getClientOriginalExtension();
                $request->file('cover_image')->move(public_path('uploads/book_cover'), $fileName);
                $data['cover']=$fileName;
            }
            $book->update($data);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('library.book')]);
            return redirect()->route('library.book');
        }
        $branches=is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        $categories=BookCategory::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $list=DB::table('book as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        return view('library.book_edit', ['title'=>translate('books'),'book'=>$book,'branches'=>$branches,'categories'=>$categories,'booklist'=>$list]);
    }

    public function bookDelete(Request $request,$id){
        abort_unless(get_permission('book','is_delete'),403);
        $cover=Book::where('id',$id)->value('cover');
        if($cover && file_exists(public_path('uploads/book_cover/'.$cover))) @unlink(public_path('uploads/book_cover/'.$cover));
        $q=Book::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function category(Request $request)
    {
        if($request->isMethod('post')){
            abort_unless(get_permission('book_category','is_add'),403);
            if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
            $request->validate(['name'=>['required', function($a,$v,$fail) use($request){
                $branchId = is_superadmin_loggedin()? $request->input('branch_id'): get_loggedin_branch_id();
                $q=BookCategory::where('name',$v)->where('branch_id',$branchId);
                if($request->input('category_id')) $q->where('id','!=',$request->input('category_id'));
                if($q->exists()) $fail(translate('already_taken'));
            }]]);
            $branchId=$this->branchId($request);
            if($request->input('category_id')) BookCategory::where('id',$request->input('category_id'))->update(['name'=>$request->input('name'),'branch_id'=>$branchId]);
            else BookCategory::create(['name'=>$request->input('name'),'branch_id'=>$branchId]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('library.category')]);
            return redirect()->route('library.category');
        }
        $list=DB::table('book_category as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        $branches=is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        return view('library.category', ['title'=>translate('category'),'categorylist'=>$list,'branches'=>$branches]);
    }

    public function categoryEdit(Request $request)
    {
        abort_unless(get_permission('book_category','is_edit'),403);
        if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
        $request->validate(['name'=>['required', function($a,$v,$fail) use($request){
            $branchId = is_superadmin_loggedin()? $request->input('branch_id'): get_loggedin_branch_id();
            $q=BookCategory::where('name',$v)->where('branch_id',$branchId)->where('id','!=',$request->input('category_id'));
            if($q->exists()) $fail(translate('already_taken'));
        }]]);
        $branchId=$this->branchId($request);
        BookCategory::where('id',$request->input('category_id'))->update(['name'=>$request->input('name'),'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success','url'=>route('library.category')]);
    }

    public function categoryDelete(Request $request,$id){
        abort_unless(get_permission('book_category','is_delete'),403);
        $q=BookCategory::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function issue(Request $request)
    {
        abort_unless(get_permission('book_issue','is_view') || get_permission('book_manage','is_view'),403);
        // handle status update and deletion via query params handled elsewhere? legacy book_manage does both.
        $branchId = is_superadmin_loggedin()? get_loggedin_branch_id(): get_loggedin_branch_id();
        if($request->has('update')){
            $id=$request->input('id');
            BookIssue::where('id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->update(['issued_by'=>get_loggedin_user_id(),'status'=>$request->input('status')]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            return redirect()->route('library.issue');
        }
        $list=$this->getBookIssueList();
        return view('library.issue', ['title'=>translate('book_issue_return'),'booklist'=>$list,'branch_id'=>$branchId]);
    }

    public function bookIssued(Request $request)
    {
        abort_unless(get_permission('book_manage','is_add'),403);
        if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
        $request->validate([
            'category_id'=>['required'],'book_id'=>['required', function($a,$v,$fail){ $q=DB::table('book')->where('id',$v)->first(); if(!$q || $q->total_stock==0 || $q->issued_copies >= $q->total_stock) $fail(translate('the_book_is_not_available_in_stock')); }],
            'role_id'=>['required'],'user_id'=>['required'],'date_of_expiry'=>['required', function($a,$v,$fail){ if(strtotime($v) <= strtotime(date('Y-m-d'))) $fail(translate('today_or_the_previous_day_can_not_be_issued')); }],
        ]);
        if($request->input('role_id')==7) $request->validate(['class_id'=>['required']]);
        $branchId=$this->branchId($request);
        $data=['branch_id'=>$branchId,'book_id'=>$request->input('book_id'),'user_id'=>$request->input('user_id'),'role_id'=>$request->input('role_id'),'date_of_issue'=>date('Y-m-d'),'date_of_expiry'=>date('Y-m-d',strtotime($request->input('date_of_expiry'))),'issued_by'=>get_loggedin_user_id(),'status'=>1,'session_id'=>get_session_id()];
        DB::table('book_issues')->insert($data);
        DB::table('book')->where('id',$request->input('book_id'))->increment('issued_copies',1);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return response()->json(['status'=>'success','url'=>route('library.issue')]);
    }

    public function bookReturn(Request $request)
    {
        abort_unless(get_permission('book_manage','is_add'),403);
        $request->validate([
            'date'=>['required', function($a,$v,$fail) use($request){
                $id=$request->input('issue_id');
                $get=DB::table('book_issues')->where('id',$id)->first(['date_of_issue','date_of_expiry']);
                if($get && (strtotime($get->date_of_issue) >= strtotime($v) || strtotime($get->date_of_expiry) > strtotime($v))) $fail(translate('invalid_return_date_entered'));
            }],
            'fine_amount'=>['nullable','numeric'],
        ]);
        $id=$request->input('issue_id');
        $type=$request->input('type');
        $get=DB::table('book_issues')->where('id',$id)->first();
        if($type==1){
            DB::table('book')->where('id',$get->book_id)->decrement('issued_copies',1);
            $upd=['return_by'=>get_loggedin_user_id(),'status'=>3,'fine_amount'=>$request->input('fine_amount',0),'return_date'=>date('Y-m-d',strtotime($request->input('date')))];
        } else {
            $upd=['fine_amount'=>$request->input('fine_amount',0),'date_of_expiry'=>date('Y-m-d',strtotime($request->input('date')))];
        }
        DB::table('book_issues')->where('id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->update($upd);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return response()->json(['status'=>'success','url'=>route('library.issue')]);
    }

    public function issuedBookDelete(Request $request,$id){
        abort_unless(get_permission('book_manage','is_delete'),403);
        $status=DB::table('book_issues')->where('id',$id)->value('status');
        if($status==2 || $status==3){
            $q=DB::table('book_issues')->where('id',$id);
            if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
            $q->delete();
        }
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function getBooksByCategory(Request $request){
        $cat=$request->input('category_id');
        if(empty($cat)) return response('<option value="">'.translate('select_category_first').'</option>');
        $books=DB::table('book')->where('category_id',$cat)->select('id','title')->get();
        if($books->count()){
            $html='<option value="">'.translate('select').'</option>';
            foreach($books as $r) $html.='<option value="'.$r->id.'">'.$r->title.'</option>';
            return response($html);
        }
        return response('<option value="">'.translate('no_information_available').'</option>');
    }

    private function getBookIssueList($id=''){
        $q=DB::table('book_issues as bi')->leftJoin('book as b','b.id','=','bi.book_id')->leftJoin('branch as br','br.id','=','bi.branch_id')->leftJoin('roles','roles.id','=','bi.role_id')->leftJoin('book_category as c','c.id','=','b.category_id')->select('bi.*','b.title','b.cover','b.isbn_no','b.edition','b.author','br.name as branch_name','c.name as category_name','roles.name as role_name');
        if(!is_superadmin_loggedin()) $q->where('bi.branch_id',get_loggedin_branch_id());
        $q->where('bi.session_id',get_session_id());
        if($id!=''){ $q->where('bi.id',$id); return $q->first(); }
        $q->orderBy('bi.id','desc');
        return $q->get();
    }
}
