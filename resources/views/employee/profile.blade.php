@extends('layouts.app')
@section('title', translate('employee_profile'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('employee.index') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-left class="size-4"/></a>
        <span class="font-semibold text-slate-800">{{ $staff['name'] }} - {{ translate('employee_profile') }}</span>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5 flex items-center gap-4">
        <img src="{{ get_image_url('staff',$staff['photo']) }}" class="size-16 rounded-full border object-cover">
        <div>
            <h3 class="font-bold text-slate-800">{{ $staff['name'] }} ({{ $staff['staff_id'] }})</h3>
            <p class="text-sm text-slate-500">{{ $staff['designation_name'] }} - {{ $staff['department_name'] }} | {{ $staff['role'] }}</p>
        </div>
    </div>
    <form method="POST" action="{{ route('employee.profile',$staff['id']) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <input type="hidden" name="submit" value="update">
        <input type="hidden" name="staff_id" value="{{ $staff['id'] }}">
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('employee_details') }}</div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                @if(is_superadmin_loggedin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                    <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        @foreach($branches as $b)<option value="{{ $b->id }}" @selected($staff['branch_id']==$b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('role') }} <span class="text-red-500">*</span></label>
                    <select name="user_role" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        @foreach($roles as $r)<option value="{{ $r->id }}" @selected($staff['role_id']==$r->id)>{{ $r->name }}</option>@endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('joining_date') }} <span class="text-red-500">*</span></label><input type="date" name="joining_date" value="{{ old('joining_date',$staff['joining_date']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('designation') }} <span class="text-red-500">*</span></label><select name="designation_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">@foreach($designations as $d)<option value="{{ $d->id }}" @selected($staff['designation']==$d->id)>{{ $d->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('department') }} <span class="text-red-500">*</span></label><select name="department_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">@foreach($departments as $d)<option value="{{ $d->id }}" @selected($staff['department']==$d->id)>{{ $d->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('qualification') }} <span class="text-red-500">*</span></label><input type="text" name="qualification" value="{{ old('qualification',$staff['qualification']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('name') }} <span class="text-red-500">*</span></label><input type="text" name="name" value="{{ old('name',$staff['name']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('gender') }}</label><select name="sex" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="male" @selected($staff['sex']=='male')>Male</option><option value="female" @selected($staff['sex']=='female')>Female</option></select></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('birthday') }}</label><input type="date" name="birthday" value="{{ old('birthday',$staff['birthday']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('religion') }}</label><input type="text" name="religion" value="{{ old('religion',$staff['religion']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('blood_group') }}</label><select name="blood_group" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option>@foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)<option value="{{ $bg }}" @selected($staff['blood_group']==$bg)>{{ $bg }}</option>@endforeach</select></div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label><input type="text" name="mobile_no" value="{{ old('mobile_no',$staff['mobileno']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('present_address') }} <span class="text-red-500">*</span></label><textarea name="present_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('present_address',$staff['present_address']) }}</textarea></div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('permanent_address') }}</label><textarea name="permanent_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('permanent_address',$staff['permanent_address']) }}</textarea></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('email') }} <span class="text-red-500">*</span></label><input type="email" name="email" value="{{ old('email',$staff['email']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div class="sm:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('profile_picture') }}</label><input type="file" name="user_photo" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><input type="hidden" name="old_user_photo" value="{{ $staff['photo'] }}"></div>
            </div>
        </div>
        <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white">{{ translate('update') }}</button></div>
    </form>
</div>
@endsection
