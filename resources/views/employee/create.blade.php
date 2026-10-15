@extends('layouts.app')
@section('title', translate('add_employee'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('employee.index') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-left class="size-4"/></a>
        <span class="font-semibold text-slate-800">{{ translate('add_employee') }}</span>
    </div>
    <form method="POST" action="{{ route('employee.create') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('academic_details') }}</div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
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
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('role') }} <span class="text-red-500">*</span></label>
                    <select name="user_role" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($roles as $r)<option value="{{ $r->id }}" @selected(old('user_role')==$r->id)>{{ $r->name }}</option>@endforeach
                    </select>
                    @error('user_role')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('joining_date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    @error('joining_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('designation') }} <span class="text-red-500">*</span></label>
                    <select name="designation_id" id="designation_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($designations as $d)<option value="{{ $d->id }}" @selected(old('designation_id')==$d->id)>{{ $d->name }}</option>@endforeach
                    </select>
                    @error('designation_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('department') }} <span class="text-red-500">*</span></label>
                    <select name="department_id" id="department_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($departments as $d)<option value="{{ $d->id }}" @selected(old('department_id')==$d->id)>{{ $d->name }}</option>@endforeach
                    </select>
                    @error('department_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('qualification') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="qualification" value="{{ old('qualification') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    @error('qualification')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('employee_details') }}</div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('name') }} <span class="text-red-500">*</span></label><input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('gender') }}</label><select name="sex" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option><option value="male" @selected(old('sex')=='male')>Male</option><option value="female" @selected(old('sex')=='female')>Female</option></select></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('religion') }}</label><input type="text" name="religion" value="{{ old('religion') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('blood_group') }}</label><select name="blood_group" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option>@foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)<option value="{{ $bg }}" @selected(old('blood_group')==$bg)>{{ $bg }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('birthday') }}</label><input type="date" name="birthday" value="{{ old('birthday') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label><input type="text" name="mobile_no" value="{{ old('mobile_no') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('mobile_no')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('present_address') }} <span class="text-red-500">*</span></label><textarea name="present_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('present_address') }}</textarea>@error('present_address')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('permanent_address') }}</label><textarea name="permanent_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('permanent_address') }}</textarea></div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('profile_picture') }}</label><input type="file" name="user_photo" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">@error('user_photo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('login_details') }}</div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('email') }} <span class="text-red-500">*</span></label><input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('password') }} <span class="text-red-500">*</span></label><input type="password" name="password" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('retype_password') }} <span class="text-red-500">*</span></label><input type="password" name="retype_password" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('social_links') }}</div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Facebook</label><input type="url" name="facebook" value="{{ old('facebook') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" placeholder="https://..."></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Twitter</label><input type="url" name="twitter" value="{{ old('twitter') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" placeholder="https://..."></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Linkedin</label><input type="url" name="linkedin" value="{{ old('linkedin') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm" placeholder="https://..."></div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800 flex items-center gap-2">
                {{ translate('bank_details') }}
                <label class="ml-auto flex items-center gap-2 text-xs font-normal"><input type="checkbox" name="chkskipped" value="1" id="chk_bank_skipped" @checked(old('chkskipped')) class="rounded"> {{ translate('skipped_bank_details') }}</label>
            </div>
            <div id="bank_details_form" class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3 {{ old('chkskipped') ? 'hidden' : '' }}">
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('bank_name') }} <span class="text-red-500">*</span></label><input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('bank_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('holder_name') }} <span class="text-red-500">*</span></label><input type="text" name="holder_name" value="{{ old('holder_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('holder_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('bank_branch') }} <span class="text-red-500">*</span></label><input type="text" name="bank_branch" value="{{ old('bank_branch') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('bank_branch')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('bank_address') }}</label><input type="text" name="bank_address" value="{{ old('bank_address') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">IFSC</label><input type="text" name="ifsc_code" value="{{ old('ifsc_code') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('account_no') }} <span class="text-red-500">*</span></label><input type="text" name="account_no" value="{{ old('account_no') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('account_no')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            </div>
        </div>
        <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700">{{ translate('save') }}</button></div>
    </form>
</div>
@push('scripts')
<script>
document.getElementById('chk_bank_skipped')?.addEventListener('change', function(){document.getElementById('bank_details_form').classList.toggle('hidden', this.checked);});
document.getElementById('branch_id')?.addEventListener('change', function(){
    const bid=this.value;
    fetch('{{ route('ajax.getDataByBranch') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({branch_id:bid,table:'staff_department'})}).then(r=>r.text()).then(html=>{document.getElementById('department_id').innerHTML=html});
    fetch('{{ route('ajax.getDataByBranch') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({branch_id:bid,table:'staff_designation'})}).then(r=>r.text()).then(html=>{document.getElementById('designation_id').innerHTML=html});
});
</script>
@endpush
@endsection
