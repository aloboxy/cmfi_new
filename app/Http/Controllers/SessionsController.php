<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SessionsController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        $sessions = SchoolYear::orderBy('id','desc')->get();
        return view('sessions.index', [
            'title'=>translate('session_settings'),
            'sessions'=>$sessions,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        $validated = $request->validate([
            'session'=>['required','string','max:255','unique:schoolyear,school_year'],
        ]);
        SchoolYear::create(['school_year'=>$validated['session'],'created_by'=> get_loggedin_user_id()]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('sessions.index');
    }

    public function update(Request $request)
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        $validated = $request->validate([
            'schoolyear_id'=>'required|integer|exists:schoolyear,id',
            'session'=>['required','string','max:255', Rule::unique('schoolyear','school_year')->ignore($request->input('schoolyear_id'))],
        ]);
        SchoolYear::where('id',$validated['schoolyear_id'])->update(['school_year'=>$validated['session']]);
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status'=>'success']);
        }
        set_alert('success', translate('information_has_been_updated_successfully'));
        return redirect()->route('sessions.index');
    }

    public function destroy(int $id)
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        SchoolYear::where('id',$id)->delete();
        if (request()->expectsJson() || request()->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('sessions.index');
    }
}
