<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\LanguageList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TranslationsController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(get_permission('translations','is_view'), 403, translate('access_denied'));
        $languages = LanguageList::orderBy('id')->get();
        return view('translations.index', ['title'=>translate('translations'),'languages'=>$languages]);
    }

    public function update(Request $request)
    {
        abort_unless(get_permission('translations','is_edit'), 403, translate('access_denied'));
        $lang = e($request->query('lang') ?? $request->input('lang'));
        abort_unless($lang, 302, '');
        if (!$request->has('submit') || $request->input('submit') !== 'update') {
            // show edit page with all words
            if (!Schema::hasColumn('languages', $lang)) abort(404);
            $words = DB::table('languages')->select('id','word',$lang)->orderBy('word')->get();
            return view('translations.edit', ['title'=>translate('translations'),'select_language'=>$lang,'query_language'=>$words]);
        }
        // save
        $words = DB::table('languages')->select('id','word')->get();
        foreach ($words as $row) {
            $word = $request->input('word_'.$row->word);
            if ($word !== null && $word !== '') {
                DB::table('languages')->where('word',$row->word)->update([$lang=>$word]);
            }
        }
        DB::table('language_list')->where('lang_field',$lang)->update(['updated_at'=>now()]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return redirect()->route('translations.index');
    }

    public function store(Request $request)
    {
        abort_unless(get_permission('translations','is_add'), 403, translate('access_denied'));
        $request->validate(['name'=>'required|string|max:255']);
        $name = ucfirst($request->input('name'));
        $id = DB::table('language_list')->insertGetId(['name'=>$name,'lang_field'=>'','status'=>1,'created_at'=>now(),'updated_at'=>now()]);
        if ($request->hasFile('flag')) {
            $dir = public_path('uploads/language_flags');
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $request->file('flag')->move($dir, 'flag_'.$id.'.png');
            $this->createThumb($dir.'/flag_'.$id.'.png');
        }
        $langField = 'lang_'.$id;
        DB::table('language_list')->where('id',$id)->update(['lang_field'=>$langField]);
        if (!Schema::hasColumn('languages', $langField)) {
            Schema::table('languages', function($table) use ($langField){
                $table->longText($langField)->nullable()->default('');
            });
        }
        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('translations.index');
    }

    public function rename(Request $request, int $id)
    {
        abort_unless(get_permission('translations','is_edit'), 403, translate('access_denied'));
        $request->validate(['rename'=>'required|string|max:255']);
        DB::table('language_list')->where('id',$id)->update(['name'=>$request->input('rename')]);
        if ($request->hasFile('flag')) {
            $dir = public_path('uploads/language_flags');
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $request->file('flag')->move($dir, 'flag_'.$id.'.png');
            $this->createThumb($dir.'/flag_'.$id.'.png');
        }
        set_alert('success', translate('information_has_been_updated_successfully'));
        return redirect()->route('translations.index');
    }

    public function destroy(int $id)
    {
        abort_unless(get_permission('translations','is_delete'), 403, translate('access_denied'));
        $row = DB::table('language_list')->where('id',$id)->first();
        if ($row && Schema::hasColumn('languages', $row->lang_field)) {
            Schema::table('languages', function($table) use ($row){ $table->dropColumn($row->lang_field); });
        }
        DB::table('language_list')->where('id',$id)->delete();
        @unlink(public_path('uploads/language_flags/flag_'.$id.'.png'));
        @unlink(public_path('uploads/language_flags/flag_'.$id.'_thumb.png'));
        if (request()->expectsJson() || request()->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('translations.index');
    }

    public function status(Request $request)
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
        $request->validate(['lang_id'=>'required|integer','status'=>'required']);
        $status = $request->input('status') === 'true' || $request->input('status') == 1 ? 1 : 0;
        DB::table('language_list')->where('id',$request->input('lang_id'))->update(['status'=>$status]);
        $msg = $status ? translate('language_published') : translate('language_unpublished');
        return response($msg);
    }

    public function getDetails(Request $request)
    {
        $id = $request->input('id');
        $row = DB::table('language_list')->where('id',$id)->first();
        return response()->json((array)$row);
    }

    private function createThumb(string $source): void
    {
        if (!extension_loaded('gd') || !file_exists($source)) return;
        try {
            $info = getimagesize($source);
            if (!$info) return;
            $src = imagecreatefrompng($source);
            if (!$src) return;
            $thumb = imagescale($src, 16, 12);
            if ($thumb) {
                imagepng($thumb, str_replace('.png','_thumb.png',$source));
                imagedestroy($thumb);
            }
            imagedestroy($src);
        } catch (\Throwable $e) {}
    }
}
