<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionsController extends Controller
{
    public function index()
    {
        abort_unless(get_permission('section', 'is_view'), 403, translate('access_denied'));

        $sections = DB::table('section as t')
            ->leftJoin('branch as b', 'b.id', '=', 't.branch_id')
            ->select('t.*', 'b.name as branch_name')
            ->when(! is_superadmin_loggedin(), fn ($q) => $q->where('t.branch_id', get_loggedin_branch_id()))
            ->orderBy('t.id', 'asc')
            ->get();

        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();

        return view('sections.index', [
            'title' => translate('section_control'),
            'sections' => $sections,
            'branches' => $branches,
        ]);
    }

    public function edit(int $id)
    {
        abort_unless(get_permission('section', 'is_edit'), 403, translate('access_denied'));

        $section = DB::table('section as t')
            ->leftJoin('branch as b', 'b.id', '=', 't.branch_id')
            ->select('t.*', 'b.name as branch_name')
            ->where('t.id', $id)
            ->when(! is_superadmin_loggedin(), fn ($q) => $q->where('t.branch_id', get_loggedin_branch_id()))
            ->first();

        abort_if(! $section, 404);

        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();

        return view('sections.edit', [
            'title' => translate('section_control'),
            'section' => (array) $section,
            'branches' => $branches,
        ]);
    }

    public function save(Request $request)
    {
        $isEdit = $request->filled('section_id');

        if (! $isEdit) {
            abort_unless(get_permission('section', 'is_add'), 403, translate('access_denied'));
        } else {
            abort_unless(get_permission('section', 'is_edit'), 403, translate('access_denied'));
        }

        $branchId = $this->resolveBranchId($request);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'numeric'],
        ];
        if (is_superadmin_loggedin()) {
            $rules['branch_id'] = ['required', 'integer', 'exists:branch,id'];
        }

        $validated = $request->validate($rules);

        // unique_name per branch
        $query = Section::where('name', $validated['name'])->where('branch_id', $branchId);
        if ($isEdit) {
            $query->where('id', '!=', $request->input('section_id'));
        }
        if ($query->exists()) {
            $message = translate('already_taken');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'fail', 'error' => ['name' => $message]]);
            }
            return back()->withErrors(['name' => $message])->withInput();
        }

        $data = [
            'name' => $validated['name'],
            'capacity' => $validated['capacity'] ?? null,
            'branch_id' => $branchId,
        ];

        if (! $isEdit) {
            Section::create($data);
            set_alert('success', translate('information_has_been_saved_successfully'));
        } else {
            $q = Section::where('id', $request->input('section_id'));
            if (! is_superadmin_loggedin()) {
                $q->where('branch_id', get_loggedin_branch_id());
            }
            $q->update($data);
            set_alert('success', translate('information_has_been_updated_successfully'));
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('sections.index')]);
        }

        return redirect()->route('sections.index');
    }

    public function destroy(Request $request, int $id)
    {
        abort_unless(get_permission('section', 'is_delete'), 403, translate('access_denied'));

        $q = Section::where('id', $id);
        if (! is_superadmin_loggedin()) {
            $q->where('branch_id', get_loggedin_branch_id());
        }
        $q->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success']);
        }

        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('sections.index');
    }

    private function resolveBranchId(Request $request): int
    {
        if (is_superadmin_loggedin()) {
            return (int) $request->input('branch_id');
        }
        return (int) get_loggedin_branch_id();
    }
}
