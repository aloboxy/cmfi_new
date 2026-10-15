<?php

namespace App\Http\Controllers;

use App\Models\ModuleControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ModuleControlController extends Controller
{
    public function index()
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        $this->syncFromNavigation();
        $modules = ModuleControl::orderByRaw("CASE WHEN parent_key IS NULL THEN 0 ELSE 1 END")->orderBy('display_name')->get()->groupBy('parent_key');
        $parents = $modules->get('', collect());
        return view('settings.modules', [
            'title' => translate('module_control'),
            'parents' => $parents,
            'grouped' => $modules,
        ]);
    }

    public function toggle(Request $request)
    {
        abort_unless(is_superadmin_loggedin(), 403);
        $request->validate(['module_key' => 'required|string|exists:module_controls,module_key']);
        $module = ModuleControl::where('module_key', $request->input('module_key'))->firstOrFail();
        $module->is_enabled = !$module->is_enabled;
        $module->save();
        // If parent disabled, disable children; if enabled, don't auto-enable children
        if (!$module->is_enabled) {
            ModuleControl::where('parent_key', $module->module_key)->update(['is_enabled' => false]);
        }
        Cache::forget('module_controls_map');
        Cache::forget('module_controls_list');
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'is_enabled' => $module->is_enabled]);
        }
        set_alert('success', translate('information_has_been_updated_successfully'));
        return back();
    }

    public function bulkToggle(Request $request)
    {
        abort_unless(is_superadmin_loggedin(), 403);
        $request->validate(['is_enabled' => 'required|boolean']);
        ModuleControl::query()->update(['is_enabled' => $request->boolean('is_enabled')]);
        Cache::forget('module_controls_map');
        Cache::forget('module_controls_list');
        set_alert('success', translate('information_has_been_updated_successfully'));
        return back();
    }

    private function syncFromNavigation(): void
    {
        $nav = config('navigation', []);
        $flat = $this->flattenNav($nav);
        foreach ($flat as $item) {
            $key = $item['module_key'];
            ModuleControl::firstOrCreate(
                ['module_key' => $key],
                [
                    'display_name' => ucwords(str_replace(['_', '-'], ' ', $item['label'])),
                    'icon' => $item['icon'] ?? null,
                    'parent_key' => $item['parent_key'] ?? null,
                    'is_enabled' => true,
                ]
            );
        }
        // Optionally clean up orphaned keys that no longer exist in navigation
        // ModuleControl::whereNotIn('module_key', collect($flat)->pluck('module_key'))->delete();
    }

    private function flattenNav(array $items, ?string $parent = null): array
    {
        $out = [];
        foreach ($items as $it) {
            $key = $it['label'] ?? $it['route'] ?? null;
            // Use label as key for top-level, but ensure uniqueness
            $moduleKey = $this->labelToKey($it['label'] ?? '');
            if ($moduleKey) {
                $out[] = ['module_key' => $moduleKey, 'label' => $it['label'], 'icon' => $it['icon'] ?? null, 'parent_key' => $parent];
                if (!empty($it['children'])) {
                    $out = array_merge($out, $this->flattenNav($it['children'], $moduleKey));
                }
            } elseif (!empty($it['children'])) {
                $out = array_merge($out, $this->flattenNav($it['children'], $parent));
            }
        }
        return $out;
    }

    private function labelToKey(string $label): string
    {
        return strtolower(str_replace([' ', '-'], '_', trim($label)));
    }
}
