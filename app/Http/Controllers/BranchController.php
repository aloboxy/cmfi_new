<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index()
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        abort_unless(get_permission('branch', 'is_view'), 403, translate('access_denied'));

        $branches = Branch::orderBy('id', 'asc')->get();

        return view('branch.index', [
            'title' => translate('branch'),
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        abort_unless(get_permission('branch', 'is_view'), 403, translate('access_denied'));

        $validated = $request->validate([
            'branch_name'    => ['required', 'string', 'max:255', 'unique:branch,name'],
            'school_name'    => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:100'],
            'mobileno'       => ['required', 'string', 'max:100'],
            'currency'       => ['required', 'string', 'max:100'],
            'currency_symbol'=> ['required', 'string', 'max:25'],
            'city'           => ['nullable', 'string', 'max:255'],
            'state'          => ['nullable', 'string', 'max:255'],
            'address'        => ['nullable', 'string'],
        ]);

        Branch::create([
            'name'        => $validated['branch_name'],
            'school_name' => $validated['school_name'],
            'email'       => $validated['email'],
            'mobileno'    => $validated['mobileno'],
            'currency'    => $validated['currency'],
            'symbol'      => $validated['currency_symbol'],
            'city'        => $validated['city'] ?? '',
            'state'       => $validated['state'] ?? '',
            'address'     => $validated['address'] ?? '',
        ]);

        set_alert('success', translate('information_has_been_saved_successfully'));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('branch.index')]);
        }

        return redirect()->route('branch.index');
    }

    public function edit(int $id)
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        abort_unless(get_permission('branch', 'is_view'), 403, translate('access_denied'));

        $branch = Branch::findOrFail($id);

        return view('branch.edit', [
            'title' => translate('branch'),
            'branch' => $branch,
        ]);
    }

    public function update(Request $request, int $id)
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        abort_unless(get_permission('branch', 'is_view'), 403, translate('access_denied'));

        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'branch_name'    => ['required', 'string', 'max:255', Rule::unique('branch', 'name')->ignore($branch->id)],
            'school_name'    => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:100'],
            'mobileno'       => ['required', 'string', 'max:100'],
            'currency'       => ['required', 'string', 'max:100'],
            'currency_symbol'=> ['required', 'string', 'max:25'],
            'city'           => ['nullable', 'string', 'max:255'],
            'state'          => ['nullable', 'string', 'max:255'],
            'address'        => ['nullable', 'string'],
        ]);

        $branch->update([
            'name'        => $validated['branch_name'],
            'school_name' => $validated['school_name'],
            'email'       => $validated['email'],
            'mobileno'    => $validated['mobileno'],
            'currency'    => $validated['currency'],
            'symbol'      => $validated['currency_symbol'],
            'city'        => $validated['city'] ?? '',
            'state'       => $validated['state'] ?? '',
            'address'     => $validated['address'] ?? '',
        ]);

        set_alert('success', translate('information_has_been_updated_successfully'));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('branch.index')]);
        }

        return redirect()->route('branch.index');
    }

    public function destroy(Request $request, int $id)
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        abort_unless(get_permission('branch', 'is_view'), 403, translate('access_denied'));

        Branch::where('id', $id)->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success']);
        }

        set_alert('success', translate('information_has_been_deleted_successfully'));

        return redirect()->route('branch.index');
    }
}
