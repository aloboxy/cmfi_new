@extends('layouts.app')
@section('title', translate('global_settings'))
@section('content')
<div class="space-y-5" x-data="{ tab: '{{ session('active')==2 ? 'theme' : (session('active')==3 ? 'logo' : 'setting') }}' }">
    <div class="flex items-center gap-2 text-[13px] text-slate-500"><a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a><span class="text-slate-300">/</span><span class="font-semibold text-slate-800">{{ translate('global_settings') }}</span></div>
    <div class="flex gap-2 flex-wrap"><button @click="tab='setting'" :class="tab==='setting' ? 'bg-[#2563eb] text-white shadow' : 'bg-white border border-slate-200 text-slate-700'" class="rounded-xl px-4 py-2 text-sm font-semibold transition">{{ translate('general_settings') }}</button><button @click="tab='theme'" :class="tab==='theme' ? 'bg-[#2563eb] text-white shadow' : 'bg-white border border-slate-200 text-slate-700'" class="rounded-xl px-4 py-2 text-sm font-semibold transition">{{ translate('theme') }}</button><button @click="tab='logo'" :class="tab==='logo' ? 'bg-[#2563eb] text-white shadow' : 'bg-white border border-slate-200 text-slate-700'" class="rounded-xl px-4 py-2 text-sm font-semibold transition">{{ translate('logo') }}</button></div>

    {{-- GENERAL --}}
    <div x-show="tab==='setting'" x-cloak class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 sm:p-6">
        <form method="POST" action="{{ route('settings.universal') }}" enctype="multipart/form-data" class="space-y-5">@csrf<input type="hidden" name="submit" value="setting">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">{{ translate('institute_name') }}</label><input name="institute_name" value="{{ $global->institute_name ?? '' }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--primary)]/20 focus:border-[var(--primary)] outline-none"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('institution_code') }}</label><input name="institution_code" value="{{ $global->institution_code ?? '' }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--primary)]/20 outline-none"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('reg_prefix') }}</label><input name="reg_prefix" value="{{ $global->reg_prefix ?? '' }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('institute_email') }}</label><input name="institute_email" value="{{ $global->institute_email ?? '' }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('currency') }}</label><input name="currency" value="{{ $global->currency ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('currency_symbol') }}</label><input name="currency_symbol" value="{{ $global->currency_symbol ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('session_id') }}</label><select name="session_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm bg-white dark:bg-slate-800">@foreach(App\Models\SchoolYear::orderBy('school_year')->get() as $s)<option value="{{ $s->id }}" @selected(($global->session_id ?? '')==$s->id)>{{ $s->school_year }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('translation') }}</label><input name="translation" value="{{ $global->translation ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></div>
                <div class="sm:col-span-2"><label class="block text-sm font-medium mb-1">{{ translate('address') }}</label><textarea name="address" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">{{ $global->address ?? '' }}</textarea></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('footer_text') }}</label><input name="footer_text" value="{{ $global->footer_text ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('timezone') }}</label><input name="timezone" value="{{ $global->timezone ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">{{ translate('date_format') }}</label><input name="date_format" value="{{ $global->date_format ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1">{{ translate('school_image') }} <span class="text-xs text-slate-400">({{ translate('recommended') }} 1200×600)</span></label>
                    <div class="flex gap-4 items-start">
                        <div class="flex-1">
                            <input type="file" name="school_image" id="general_school_image" accept="image/*" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm bg-white file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium hover:file:bg-slate-200">
                            <p class="text-xs text-slate-400 mt-1">PNG, JPG, WEBP up to 4MB. Shown on login, sidebar and as placeholder.</p>
                        </div>
                        <div class="shrink-0 w-32 h-20 rounded-xl border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center">
                            @if(!empty($global->school_image) && file_exists(public_path($global->school_image)))
                                <img id="general_school_image_preview" src="{{ asset($global->school_image) }}" class="w-full h-full object-cover">
                            @else
                                <img id="general_school_image_preview" src="" class="hidden w-full h-full object-cover">
                                <span id="general_school_image_placeholder" class="text-xs text-slate-400">No image</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[var(--primary)] px-6 py-2.5 text-sm font-semibold text-white shadow hover:opacity-90">Save General</button></div>
        </form>
        <script>document.getElementById('general_school_image')?.addEventListener('change', function(e){ const f=e.target.files[0]; if(!f) return; const r=new FileReader(); r.onload=function(ev){ const img=document.getElementById('general_school_image_preview'); const ph=document.getElementById('general_school_image_placeholder'); if(img){ img.src=ev.target.result; img.classList.remove('hidden'); } if(ph) ph.classList.add('hidden'); }; r.readAsDataURL(f); });</script>
    </div>

    {{-- THEME --}}
    <div x-show="tab==='theme'" x-cloak class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 sm:p-6 space-y-6" x-data="{ darkSkin: '{{ $theme->dark_skin ?? 'false' }}', borderMode: '{{ $theme->border_mode ?? 'true' }}' }" x-effect="document.documentElement.setAttribute('data-border', borderMode); if(darkSkin==='true') document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');">
        <form method="POST" action="{{ route('settings.universal') }}" class="space-y-6">@csrf<input type="hidden" name="submit" value="theme">

            {{-- Theme mode --}}
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3">Appearance</h3>
                <div class="grid grid-cols-2 gap-4 max-w-md">
                    <label class="cursor-pointer rounded-2xl border-2 p-3 flex flex-col items-center gap-2 transition" :class="darkSkin==='false' ? 'border-[var(--primary)] bg-[var(--primary)]/5' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300'">
                        <input type="radio" name="dark_skin" value="false" x-model="darkSkin" class="sr-only">
                        <img src="{{ asset('assets/images/theme/light.png') }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid'" class="w-full h-24 object-cover rounded-xl border border-slate-200">
                        <div class="hidden w-full h-24 rounded-xl bg-white border border-slate-200 place-items-center text-xs">Light Preview</div>
                        <span class="text-sm font-semibold flex items-center gap-1.5"><span class="size-2 rounded-full" :class="darkSkin==='false' ? 'bg-[var(--primary)]' : 'bg-slate-300'"></span> Light</span>
                    </label>
                    <label class="cursor-pointer rounded-2xl border-2 p-3 flex flex-col items-center gap-2 transition" :class="darkSkin==='true' ? 'border-[var(--primary)] bg-[var(--primary)]/5' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300'">
                        <input type="radio" name="dark_skin" value="true" x-model="darkSkin" class="sr-only">
                        <img src="{{ asset('assets/images/theme/dark.png') }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid'" class="w-full h-24 object-cover rounded-xl border border-slate-700 bg-slate-900">
                        <div class="hidden w-full h-24 rounded-xl bg-slate-900 border border-slate-700 place-items-center text-xs text-white">Dark Preview</div>
                        <span class="text-sm font-semibold flex items-center gap-1.5"><span class="size-2 rounded-full" :class="darkSkin==='true' ? 'bg-[var(--primary)]' : 'bg-slate-300'"></span> Dark</span>
                    </label>
                </div>
                <p class="text-xs text-slate-400 mt-2" x-text="darkSkin==='true' ? 'Dark mode will be applied system-wide after Save.' : 'Light mode — clean and bright.'"></p>
            </div>

            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3">Border Style</h3>
                <div class="grid grid-cols-2 gap-4 max-w-md">
                    <label class="cursor-pointer rounded-2xl border-2 p-3 flex flex-col items-center gap-2 transition" :class="borderMode==='true' ? 'border-[var(--primary)] bg-[var(--primary)]/5' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="border_mode" value="true" x-model="borderMode" class="sr-only">
                        <img src="{{ asset('assets/images/theme/rounded.png') }}" onerror="this.parentElement.querySelector('.fallback').style.display='grid'" class="w-full h-20 object-cover rounded-xl">
                        <div class="fallback hidden w-full h-20 rounded-xl bg-slate-100 place-items-center text-xs">Rounded</div>
                        <span class="text-sm font-semibold flex items-center gap-1.5"><span class="size-2 rounded-full" :class="borderMode==='true' ? 'bg-[var(--primary)]' : 'bg-slate-300'"></span> Rounded</span>
                    </label>
                    <label class="cursor-pointer rounded-2xl border-2 p-3 flex flex-col items-center gap-2 transition" :class="borderMode==='false' ? 'border-[var(--primary)] bg-[var(--primary)]/5' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="border_mode" value="false" x-model="borderMode" class="sr-only">
                        <img src="{{ asset('assets/images/theme/square.png') }}" onerror="this.parentElement.querySelector('.fallback').style.display='grid'" class="w-full h-20 object-cover" style="border-radius:0">
                        <div class="fallback hidden w-full h-20 bg-slate-100 place-items-center text-xs" style="border-radius:0">Square</div>
                        <span class="text-sm font-semibold flex items-center gap-1.5"><span class="size-2 rounded-full" :class="borderMode==='false' ? 'bg-[var(--primary)]' : 'bg-slate-300'"></span> Square</span>
                    </label>
                </div>
                <p class="text-xs text-slate-400 mt-2" x-text="borderMode==='true' ? 'Soft rounded corners (default).' : 'Sharp square corners — more formal.'"></p>
            </div>

            {{-- System Colors --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50/50 dark:bg-slate-800/30">
                <h3 class="text-sm font-bold mb-3 flex items-center gap-2"><x-heroicon-o-paint-brush class="size-4 text-[var(--primary)]"/> System Colors</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @php $colors = [
                      'primary_color'=>['Primary/Brand','#1D4ED8'],
                      'sidebar_bg'=>['Sidebar Background','#0D1B3E'],
                      'sidebar_text'=>['Sidebar Text','#C5D0E8'],
                      'sidebar_icon'=>['Sidebar Icons','#C5D0E8'],
                      'sidebar_active'=>['Sidebar Active Item','#F4A61F'],
                      'text_dark'=>['Dark Text','#111827'],
                    ]; @endphp
                    @foreach($colors as $key=>$meta)
                      <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">{{ $meta[0] }}</label>
                        <div class="flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1.5">
                          <input type="color" name="{{ $key }}" value="{{ $theme->$key ?? $meta[1] }}" class="size-8 rounded-lg border-0 p-0 cursor-pointer theme-color-input" data-var="{{ $key }}">
                          <input type="text" value="{{ $theme->$key ?? $meta[1] }}" class="flex-1 text-xs font-mono bg-transparent outline-none" readonly>
                        </div>
                      </div>
                    @endforeach
                </div>
                <p class="text-xs text-slate-400 mt-2">Live preview — changes apply after Save and refresh.</p>
            </div>

            {{-- Login customization --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50/50 dark:bg-slate-800/30">
                <h3 class="text-sm font-bold mb-3 flex items-center gap-2"><x-heroicon-o-computer-desktop class="size-4 text-[var(--primary)]"/> Login Page</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2"><label class="block text-xs font-semibold mb-1">Hero Title (left panel)</label><input name="login_hero_title" value="{{ $theme->login_hero_title ?? '' }}" placeholder="Empowering Education & Administration" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm"></div>
                    <div class="sm:col-span-2"><label class="block text-xs font-semibold mb-1">Hero Text</label><textarea name="login_hero_text" rows="2" placeholder="Access your classes, grades, payroll..." class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm">{{ $theme->login_hero_text ?? '' }}</textarea></div>
                    <div><label class="block text-xs font-semibold mb-1">Login Button Color</label><div class="flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1.5"><input type="color" name="login_button_color" value="{{ $theme->login_button_color ?? $theme->primary_color ?? '#1D4ED8' }}" class="size-8 rounded-lg border-0 p-0"><input type="text" value="{{ $theme->login_button_color ?? '' }}" class="flex-1 text-xs font-mono bg-transparent outline-none" readonly></div></div>
                </div>
            </div>

            {{-- ID Card Designer --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50/50 dark:bg-slate-800/30">
                <h3 class="text-sm font-bold mb-3 flex items-center gap-2"><x-heroicon-o-identification class="size-4 text-[var(--primary)]"/> ID Card Designer</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div><label class="block text-xs font-semibold mb-1">Layout</label><select name="idcard_layout" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm"><option value="vertical" @selected(($theme->idcard_layout ?? 'vertical')=='vertical')>Vertical</option><option value="horizontal" @selected(($theme->idcard_layout ?? '')=='horizontal')>Horizontal</option></select></div>
                    <div><label class="block text-xs font-semibold mb-1">Card Background</label><div class="flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1.5"><input type="color" name="idcard_bg_color" value="{{ $theme->idcard_bg_color ?? '#ffffff' }}" class="size-8 rounded-lg border-0 p-0"><span class="text-xs font-mono">{{ $theme->idcard_bg_color ?? '#ffffff' }}</span></div></div>
                    <div><label class="block text-xs font-semibold mb-1">Text Color</label><div class="flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1.5"><input type="color" name="idcard_text_color" value="{{ $theme->idcard_text_color ?? '#333333' }}" class="size-8 rounded-lg border-0 p-0"><span class="text-xs font-mono">{{ $theme->idcard_text_color ?? '#333333' }}</span></div></div>
                    <div><label class="block text-xs font-semibold mb-1">Border Color</label><div class="flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1.5"><input type="color" name="idcard_border_color" value="{{ $theme->idcard_border_color ?? '#E5E7EB' }}" class="size-8 rounded-lg border-0 p-0"><span class="text-xs font-mono">{{ $theme->idcard_border_color ?? '#E5E7EB' }}</span></div></div>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">Visible Fields</p>
                    <div class="flex flex-wrap gap-4 text-sm">
                        @foreach(['idcard_show_father'=>"Father's Name",'idcard_show_class'=>'Class','idcard_show_section'=>'Section','idcard_show_blood'=>'Blood Group','idcard_show_qrcode'=>'QR Code'] as $k=>$label)
                          <label class="flex items-center gap-1.5"><input type="hidden" name="{{ $k }}" value="0"><input type="checkbox" name="{{ $k }}" value="1" @checked(!empty($theme->$k)) class="rounded border-slate-300 text-[var(--primary)]"> {{ $label }}</label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[var(--primary)] px-6 py-2.5 text-sm font-semibold text-white shadow hover:opacity-90">Save Theme</button></div>
        </form>
        <script>
          document.querySelectorAll('.theme-color-input').forEach(el=>{
            el.addEventListener('input', e=>{
              const v=e.target.value;
              e.target.nextElementSibling.value=v;
              const k=e.target.name;
              if(k==='primary_color') document.documentElement.style.setProperty('--primary', v);
              if(k==='sidebar_bg') document.documentElement.style.setProperty('--sidebar-bg', v);
            });
            el.nextElementSibling?.addEventListener('input', e=>{
              el.value=e.target.value;
              el.dispatchEvent(new Event('input'));
            });
          });
        </script>
    </div>

    {{-- LOGO --}}
    <div x-show="tab==='logo'" x-cloak class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 sm:p-6">
        <form method="POST" action="{{ route('settings.universal') }}" enctype="multipart/form-data" class="space-y-6">@csrf<input type="hidden" name="submit" value="logo">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2"><x-heroicon-o-photo class="size-4 text-[var(--primary)]"/> Logos</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mt-3">
                    @php $logos = [
                      'logo_file'=>['System Logo','uploads/app_image/logo.png','225×60'],
                      'text_logo'=>['Text / Small Logo','uploads/app_image/logo-small.png','120×40'],
                      'idcard_bg'=>['ID Card Background','uploads/app_image/idcard_bg.png','400×250'],
                    ]; @endphp
                    @foreach($logos as $field=>$meta)
                      <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 bg-slate-50/50 dark:bg-slate-800/30">
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $meta[0] }} <span class="text-slate-400 font-normal">({{ $meta[2] }})</span></p>
                        <div class="mt-2 h-24 rounded-xl bg-white dark:bg-slate-800 border border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden">
                          @if(file_exists(public_path($meta[1])))
                            <img src="{{ asset($meta[1].'?v='.filemtime(public_path($meta[1]))) }}" class="max-h-full max-w-full object-contain" id="preview-{{ $field }}">
                          @else
                            <span class="text-xs text-slate-400" id="placeholder-{{ $field }}">No image</span>
                            <img id="preview-{{ $field }}" class="hidden max-h-full max-w-full object-contain">
                          @endif
                        </div>
                        <input type="file" name="{{ $field }}" id="input-{{ $field }}" accept="image/*" class="mt-2 w-full text-xs file:mr-2 file:rounded-lg file:border-0 file:bg-slate-900 file:text-white file:px-3 file:py-1.5 file:text-xs dark:file:bg-slate-700">
                      </div>
                    @endforeach
                </div>
            </div>
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2 mt-2"><x-heroicon-o-photo class="size-4 text-[var(--primary)]"/> Login Sliders</h3>
                <p class="text-xs text-slate-500 mb-3">Shown on the login hero (if uploaded, otherwise gradient). JPG recommended 1200×800.</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @for($i=1;$i<=3;$i++)
                      @php $path="uploads/login_image/slider_{$i}.jpg"; @endphp
                      <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 bg-slate-50/50 dark:bg-slate-800/30">
                        <p class="text-xs font-semibold">Slider {{ $i }}</p>
                        <div class="mt-2 h-32 rounded-xl bg-white dark:bg-slate-800 border border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden">
                          @if(file_exists(public_path($path)))
                            <img src="{{ asset($path.'?v='.filemtime(public_path($path))) }}" class="w-full h-full object-cover" id="preview-slider_{{ $i }}">
                          @else
                            <span class="text-xs text-slate-400" id="placeholder-slider_{{ $i }}">No image</span>
                            <img id="preview-slider_{{ $i }}" class="hidden w-full h-full object-cover">
                          @endif
                        </div>
                        <input type="file" name="slider_{{ $i }}" id="input-slider_{{ $i }}" accept="image/*" class="mt-2 w-full text-xs file:mr-2 file:rounded-lg file:border-0 file:bg-slate-900 file:text-white file:px-3 file:py-1.5 file:text-xs dark:file:bg-slate-700">
                      </div>
                    @endfor
                </div>
            </div>
            <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[var(--primary)] px-6 py-2.5 text-sm font-semibold text-white shadow hover:opacity-90">Upload Logos</button></div>
        </form>
        <script>
          ['logo_file','text_logo','idcard_bg','slider_1','slider_2','slider_3'].forEach(f=>{
            document.getElementById('input-'+f)?.addEventListener('change', e=>{
              const file=e.target.files[0]; if(!file) return;
              const r=new FileReader(); r.onload=ev=>{
                const img=document.getElementById('preview-'+f);
                const ph=document.getElementById('placeholder-'+f);
                if(img){ img.src=ev.target.result; img.classList.remove('hidden'); }
                if(ph) ph.style.display='none';
              }; r.readAsDataURL(file);
            });
          });
        </script>
    </div>
</div>
@endsection
