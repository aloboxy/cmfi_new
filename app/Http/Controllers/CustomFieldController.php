<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(get_permission('custom_field','is_view'), 403, translate('access_denied'));
        $query = CustomField::query();
        if (!is_superadmin_loggedin()) $query->where('branch_id', get_loggedin_branch_id());
        $customfield = $query->orderBy('field_order')->get();
        return view('custom_field.index', ['title'=>translate('custom_field'),'customfield'=>$customfield]);
    }

    public function edit(int $id)
    {
        abort_unless(get_permission('custom_field','is_edit'), 403, translate('access_denied'));
        $q = CustomField::where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $field = $q->firstOrFail();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('custom_field.edit', ['title'=>translate('custom_field'),'customfield'=>$field,'branches'=>$branches]);
    }

    public function save(Request $request)
    {
        $isEdit = $request->filled('custom_field_id');
        if ($isEdit) abort_unless(get_permission('custom_field','is_edit'), 403, translate('access_denied'));
        else abort_unless(get_permission('custom_field','is_add'), 403, translate('access_denied'));

        $validated = $request->validate([
            'branch_id'=> is_superadmin_loggedin() ? 'required|integer|exists:branch,id' : 'nullable',
            'belongs_to'=>'required|string|max:50',
            'field_label'=>'required|string|max:100',
            'field_type'=>'required|in:text,textarea,dropdown,date,checkbox,number,url,email',
            'bs_column'=>'required|integer',
            'field_order'=>'required|numeric',
            'dropdown_default_value'=>'required_if:field_type,dropdown|nullable|string',
            'checkbox_default_value'=>'nullable|string',
            'com_default_value'=>'nullable|string',
        ]);
        $field_type = $validated['field_type'];
        if ($field_type==='dropdown') $defaultValue = $request->input('dropdown_default_value');
        elseif ($field_type==='checkbox') $defaultValue = $request->input('checkbox_default_value');
        else $defaultValue = $request->input('com_default_value');

        $branchID = is_superadmin_loggedin() ? $validated['branch_id'] : get_loggedin_branch_id();
        // fallback if superadmin not providing branch
        if (!$branchID) $branchID = Branch::orderBy('id')->value('id');

        $data = [
            'form_to'=>$validated['belongs_to'],
            'field_label'=>$validated['field_label'],
            'field_type'=>$validated['field_type'],
            'default_value'=>$defaultValue,
            'required'=> $request->has('chk_required') ? '1' : '0',
            'status'=> $request->has('chk_active') ? 1 : 0,
            'show_on_table'=> $request->has('chk_show_table') ? '1' : '0',
            'field_order'=>$validated['field_order'],
            'bs_column'=>$validated['bs_column'],
            'branch_id'=>$branchID,
        ];
        if ($isEdit) {
            $field = CustomField::findOrFail($request->input('custom_field_id'));
            if (!is_superadmin_loggedin()) abort_unless($field->branch_id == get_loggedin_branch_id(), 403);
            $field->update($data);
        } else {
            CustomField::create($data);
        }
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status'=>'success','url'=>route('customfield.index')]);
        }
        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('customfield.index');
    }

    public function delete(int $id)
    {
        abort_unless(get_permission('custom_field','is_delete'), 403, translate('access_denied'));
        $q = CustomField::where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        \Illuminate\Support\Facades\DB::table('custom_fields_values')->where('field_id',$id)->delete();
        if (request()->expectsJson() || request()->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('customfield.index');
    }

    public function status(Request $request)
    {
        abort_unless(get_permission('custom_field','is_edit'), 403, translate('access_denied'));
        $request->validate(['id'=>'required|integer|exists:custom_field,id','status'=>'required']);
        $status = $request->input('status') === 'true' || $request->input('status') === true || $request->input('status') == 1 ? 1 : 0;
        $q = CustomField::where('id',$request->input('id'));
        if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->update(['status'=>$status]);
        return response()->json(['msg'=>translate('information_has_been_updated_successfully'),'status'=>true]);
    }
}
