@extends('layouts.app')
@section('title', translate('create_admission'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('dashboard') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-home class="size-4"/></a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ translate('create_admission') }}</span>
    </div>

    <form method="POST" action="{{ route('student.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        {{-- Academic details --}}
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-[#2563eb]/10 text-[#2563eb]"><x-heroicon-o-academic-cap class="size-4"/></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('academic_details') }}</h3>
            </div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('academic_year') }} <span class="text-red-500">*</span></label>
                    <select name="year_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach(DB::table('schoolyear')->get() as $y)
                            <option value="{{ $y->id }}" @selected(old('year_id', get_session_id())==$y->id)>{{ $y->school_year }}</option>
                        @endforeach
                    </select>
                    @error('year_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('register_no') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="register_no" value="{{ old('register_no', $register_id) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                    @error('register_no')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('admission_date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 outline-none">
                    @error('admission_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                @if(is_superadmin_loggedin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                    <select name="branch_id" id="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($branches as $b)<option value="{{ $b->id }}" @selected(old('branch_id')==$b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                    @error('branch_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class') }} <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($classes as $c)<option value="{{ $c->id }}" @selected(old('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                    @error('class_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }} <span class="text-red-500">*</span></label>
                    <select name="section_id" id="section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                    </select>
                    @error('section_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('category') }} <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($categories as $cat)<option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>{{ $cat->name }}</option>@endforeach
                    </select>
                    @error('category_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('roll') }}</label>
                    <input type="text" name="roll" value="{{ old('roll') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
            </div>
        </div>

        {{-- Student details --}}
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600"><x-heroicon-o-user class="size-4"/></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('student_details') }}</h3>
            </div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('first_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    @error('first_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('last_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    @error('last_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('gender') }}</label>
                    <select name="gender" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="male" @selected(old('gender')=='male')>{{ translate('male') }}</option>
                        <option value="female" @selected(old('gender')=='female')>{{ translate('female') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('blood_group') }}</label>
                    <select name="blood_group" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)<option value="{{ $bg }}" @selected(old('blood_group')==$bg)>{{ $bg }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('birthday') }}</label>
                    <input type="date" name="birthday" value="{{ old('birthday') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>


                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="mobileno" value="{{ old('mobileno') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    @error('mobileno')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('city') }}</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('state') }}</label>
                    <input type="text" name="state" value="{{ old('state') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('present_address') }}</label>
                    <textarea name="current_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('current_address') }}</textarea>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('permanent_address') }}</label>
                    <textarea name="permanent_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('permanent_address') }}</textarea>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('profile_picture') }}</label>
                    <input type="file" name="user_photo" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                    @error('user_photo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Login details --}}
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-amber-500/10 text-amber-600"><x-heroicon-o-lock-closed class="size-4"/></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('login_details') }}</h3>
            </div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('email') }} <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('retype_password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="retype_password" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
            </div>
        </div>

        {{-- Guardian details --}}
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-lg bg-purple-500/10 text-purple-600"><x-heroicon-o-users class="size-4"/></span>
                <h3 class="text-sm font-bold text-slate-800">{{ translate('guardian_details') }}</h3>
            </div>
            <div class="p-5 space-y-4">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="guardian_chk" value="1" id="chkGuardian" @checked(old('guardian_chk')) class="rounded border-slate-300 text-[#2563eb]">
                    {{ translate('guardian_already_exist') }}
                </label>
                <div id="exist_list" class="{{ old('guardian_chk') ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('guardian') }} <span class="text-red-500">*</span></label>
                    <select name="parent_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($parents as $p)<option value="{{ $p->id }}" @selected(old('parent_id')==$p->id)>{{ $p->name }} - {{ $p->email }}</option>@endforeach
                    </select>
                    @error('parent_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div id="guardian_form" class="{{ old('guardian_chk') ? 'hidden' : '' }} grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="grd_name" value="{{ old('grd_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                        @error('grd_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('relation') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="grd_relation" value="{{ old('grd_relation') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                        @error('grd_relation')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('occupation') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="grd_occupation" value="{{ old('grd_occupation') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                        @error('grd_occupation')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('father_name') }}</label>
                        <input type="text" name="father_name" value="{{ old('father_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mother_name') }}</label>
                        <input type="text" name="mother_name" value="{{ old('mother_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('income') }}</label>
                        <input type="text" name="grd_income" value="{{ old('grd_income') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('education') }}</label>
                        <input type="text" name="grd_education" value="{{ old('grd_education') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="grd_mobileno" value="{{ old('grd_mobileno') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                        @error('grd_mobileno')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="grd_email" value="{{ old('grd_email') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                        @error('grd_email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('password') }} <span class="text-red-500">*</span></label>
                        <input type="password" name="grd_password" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('retype_password') }} <span class="text-red-500">*</span></label>
                        <input type="password" name="grd_retype_password" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('address') }}</label>
                        <textarea name="grd_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('grd_address') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('city') }}</label>
                        <input type="text" name="grd_city" value="{{ old('grd_city') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('state') }}</label>
                        <input type="text" name="grd_state" value="{{ old('grd_state') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                <x-heroicon-o-check-circle class="size-4"/> {{ translate('save') }}
            </button>
        </div>
    </form>
</div>
@push('scripts')
<script>
document.getElementById('chkGuardian')?.addEventListener('change', function(){
    document.getElementById('exist_list').classList.toggle('hidden', !this.checked);
    document.getElementById('guardian_form').classList.toggle('hidden', this.checked);
});
document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid=this.value;
    fetch('{{ route('ajax.getClassByBranch') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({branch_id:bid})})
    .then(r=>r.text()).then(html=>{ document.getElementById('class_id').innerHTML=html; document.getElementById('section_id').innerHTML='<option value="">{{ translate('select') }}</option>'; });
    fetch('{{ route('ajax.getDataByBranch') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({branch_id:bid,table:'student_category'})})
    .then(r=>r.text()).then(html=>{ document.getElementById('category_id').innerHTML=html; });
});
document.getElementById('class_id')?.addEventListener('change', function(){
    const cid=this.value;
    fetch('{{ route('ajax.getSectionByClass') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({class_id:cid})})
    .then(r=>r.text()).then(html=>{ document.getElementById('section_id').innerHTML=html; });
});
</script>
@endpush
@endsection
