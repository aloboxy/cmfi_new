<?php

namespace App\Http\Controllers;

use App\Models\GlobalSetting;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function universal(Request $request)
    {
        abort_unless(get_permission('global_settings', 'is_view'), 403, translate('access_denied'));

        if ($request->isMethod('post')) {
            $submit = $request->input('submit');
            if ($submit === 'setting') {
                abort_unless(get_permission('global_settings', 'is_edit'), 403, translate('access_denied'));
                $data = $request->except(['_token','submit','school_image']);
                if (empty($data['reg_prefix'])) $data['reg_prefix'] = '';
                if ($request->hasFile('school_image')) {
                    $request->validate(['school_image'=>'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096']);
                    $dir = public_path('uploads/school_image');
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    // remove old
                    $old = GlobalSetting::where('id',1)->value('school_image');
                    if ($old && file_exists(public_path($old))) @unlink(public_path($old));
                    $file = $request->file('school_image');
                    $name = 'school_'.time().'.'.$file->getClientOriginalExtension();
                    $file->move($dir, $name);
                    $data['school_image'] = 'uploads/school_image/'.$name;
                    // make it the system-wide placeholder so all `defualt.png` refs show the new school image
                    @copy(public_path($data['school_image']), public_path('uploads/app_image/defualt.png'));
                    // also update the fallback used by direct asset calls with cache bust
                    @copy(public_path($data['school_image']), public_path('uploads/app_image/school_placeholder'. '.' . $file->getClientOriginalExtension()));
                }
                GlobalSetting::where('id',1)->update($data);
                set_alert('success', translate('the_configuration_has_been_updated'));
                return redirect()->back();
            }
            if ($submit === 'theme') {
                $data = $request->except(['_token','submit']);
                ThemeSetting::where('id',1)->update($data);
                set_alert('success', translate('the_configuration_has_been_updated'));
                session()->flash('active', 2);
                return redirect()->back();
            }
            if ($submit === 'logo') {
                $map = [
                    'logo_file' => 'uploads/app_image/logo.png',
                    'text_logo' => 'uploads/app_image/logo-small.png',
                    'idcard_bg' => 'uploads/app_image/idcard_bg.png',
                    'slider_1' => 'uploads/login_image/slider_1.jpg',
                    'slider_2' => 'uploads/login_image/slider_2.jpg',
                    'slider_3' => 'uploads/login_image/slider_3.jpg',
                ];
                foreach ($map as $field => $dest) {
                    if ($request->hasFile($field)) {
                        $dir = dirname(public_path($dest));
                        if (!is_dir($dir)) mkdir($dir, 0755, true);
                        $request->file($field)->move(dirname(public_path($dest)), basename($dest));
                        if ($field === 'logo_file') {
                            @copy(public_path('uploads/app_image/logo.png'), public_path('uploads/app_image/printing-logo.png'));
                            @copy(public_path('uploads/app_image/logo.png'), public_path('uploads/app_image/report-card-logo.png'));
                        }
                    }
                }
                set_alert('success', translate('the_configuration_has_been_updated'));
                session()->flash('active', 3);
                return redirect()->back();
            }
        }

        return view('settings.universal', [
            'title'=>translate('global_settings'),
            'global'=> GlobalSetting::find(1),
            'theme'=> ThemeSetting::find(1),
        ]);
    }
}
