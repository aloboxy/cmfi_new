@extends('layouts.app')
@section('title', translate('student_profile'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('student.index') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-left class="size-4"/></a>
        <span class="font-semibold text-slate-800">{{ $student['first_name'] }} {{ $student['last_name'] }} - {{ translate('student_profile') }}</span>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="p-5 flex items-center gap-4">
            <img src="{{ get_image_url('student', $student['photo']) }}" class="size-16 rounded-full border border-slate-200 object-cover">
            <div>
                <h3 class="font-bold text-slate-800">{{ $student['first_name'] }} {{ $student['last_name'] }}</h3>
                <p class="text-sm text-slate-500">{{ translate('register_no') }}: {{ $student['register_no'] }} | {{ translate('class') }}: {{ $student['class_name'] }} - {{ $student['section_name'] }}</p>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('student.profile', $student['id']) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('academic_details') }}</div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('academic_year') }} <span class="text-red-500">*</span></label>
                    <select name="year_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        @foreach(DB::table('schoolyear')->get() as $y)<option value="{{ $y->id }}" @selected($student['session_id']==$y->id)>{{ $y->school_year }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('register_no') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="register_no" value="{{ old('register_no',$student['register_no']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    @error('register_no')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('admission_date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="admission_date" value="{{ old('admission_date',$student['admission_date']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
                @if(is_superadmin_loggedin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }}</label>
                    <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        @foreach($branches as $b)<option value="{{ $b->id }}" @selected($student['branch_id']==$b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('class') }} <span class="text-red-500">*</span></label>
                    <select name="class_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        @foreach($classes as $c)<option value="{{ $c->id }}" @selected($student['class_id']==$c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('section') }}</label>
                    <select name="section_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($sections as $s)<option value="{{ $s->id }}" @selected($student['section_id']==$s->id)>{{ $s->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('category') }} <span class="text-red-500">*</span></label>
                    <select name="category_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        @foreach($categories as $cat)<option value="{{ $cat->id }}" @selected($student['category_id']==$cat->id)>{{ $cat->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('roll') }}</label>
                    <input type="text" name="roll" value="{{ old('roll',$student['roll']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('student_details') }}</div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('first_name') }} <span class="text-red-500">*</span></label><input type="text" name="first_name" value="{{ old('first_name',$student['first_name']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('last_name') }} <span class="text-red-500">*</span></label><input type="text" name="last_name" value="{{ old('last_name',$student['last_name']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('gender') }}</label><select name="gender" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="male" @selected($student['gender']=='male')>Male</option><option value="female" @selected($student['gender']=='female')>Female</option></select></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('blood_group') }}</label><select name="blood_group" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><option value="">{{ translate('select') }}</option>@foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)<option value="{{ $bg }}" @selected($student['blood_group']==$bg)>{{ $bg }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('birthday') }}</label><input type="date" name="birthday" value="{{ old('birthday',$student['birthday']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('religion') }}</label><input type="text" name="religion" value="{{ old('religion',$student['religion']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>


                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label><input type="text" name="mobileno" value="{{ old('mobileno',$student['mobileno']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('email') }} <span class="text-red-500">*</span></label><input type="email" name="email" value="{{ old('email',$student['email']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('city') }}</label><input type="text" name="city" value="{{ old('city',$student['city']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('state') }}</label><input type="text" name="state" value="{{ old('state',$student['state']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('present_address') }}</label><textarea name="current_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('current_address',$student['current_address']) }}</textarea></div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('permanent_address') }}</label><textarea name="permanent_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('permanent_address',$student['permanent_address']) }}</textarea></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('guardian') }} <span class="text-red-500">*</span></label><select name="parent_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">@foreach($parents as $p)<option value="{{ $p->id }}" @selected($student['parent_id']==$p->id)>{{ $p->name }}</option>@endforeach</select></div>
                <div class="sm:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('profile_picture') }}</label><input type="file" name="user_photo" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"><input type="hidden" name="old_user_photo" value="{{ $student['photo'] }}"></div>
            </div>
        </div>
        <div class="flex justify-end">
            <button type="submit" name="update" value="1" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700">{{ translate('update') }}</button>
        </div>
    </form>
</div>
@endsection
