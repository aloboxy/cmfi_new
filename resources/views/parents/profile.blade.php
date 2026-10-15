@extends('layouts.app')
@section('title', translate('parents_profile'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('parents.index') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-left class="size-4"/></a>
        <span class="font-semibold text-slate-800">{{ $parent['name'] }} - {{ translate('parents_profile') }}</span>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 p-5 flex items-center gap-4">
        <img src="{{ get_image_url('parent',$parent['photo']) }}" class="size-16 rounded-full border object-cover">
        <div>
            <h3 class="font-bold text-slate-800">{{ $parent['name'] }}</h3>
            <p class="text-sm text-slate-500">{{ $parent['email'] }} | {{ $parent['mobileno'] }}</p>
        </div>
    </div>
    @if(isset($children) && count($children)>0)
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('children') ?? 'Children' }}</div>
        <div class="p-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
            @foreach($children as $ch)
            <div class="rounded-xl border border-slate-200 p-3 flex items-center gap-3">
                <img src="{{ get_image_url('student',$ch->photo) }}" class="size-10 rounded-full">
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ $ch->fullname }}</p>
                    <p class="text-xs text-slate-500">{{ $ch->class_name }} - {{ $ch->section_name }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    <form method="POST" action="{{ route('parents.profile',$parent['id']) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('parents_details') }}</div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                @if(is_superadmin_loggedin())
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                    <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        @foreach($branches as $b)<option value="{{ $b->id }}" @selected($parent['branch_id']==$b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                @endif
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('name') }} <span class="text-red-500">*</span></label><input type="text" name="name" value="{{ old('name',$parent['name']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('relation') }} <span class="text-red-500">*</span></label><input type="text" name="relation" value="{{ old('relation',$parent['relation']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('occupation') }} <span class="text-red-500">*</span></label><input type="text" name="occupation" value="{{ old('occupation',$parent['occupation']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('father_name') }}</label><input type="text" name="father_name" value="{{ old('father_name',$parent['father_name']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mother_name') }}</label><input type="text" name="mother_name" value="{{ old('mother_name',$parent['mother_name']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('income') }}</label><input type="text" name="income" value="{{ old('income',$parent['income']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('education') }}</label><input type="text" name="education" value="{{ old('education',$parent['education']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('city') }}</label><input type="text" name="city" value="{{ old('city',$parent['city']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('state') }}</label><input type="text" name="state" value="{{ old('state',$parent['state']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label><input type="text" name="mobileno" value="{{ old('mobileno',$parent['mobileno']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('address') }}</label><textarea name="address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('address',$parent['address']) }}</textarea></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('email') }} <span class="text-red-500">*</span></label><input type="email" name="email" value="{{ old('email',$parent['email']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div class="sm:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('profile_picture') }}</label><input type="file" name="user_photo" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Facebook</label><input type="url" name="facebook" value="{{ old('facebook',$parent['facebook_url']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Twitter</label><input type="url" name="twitter" value="{{ old('twitter',$parent['twitter_url']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Linkedin</label><input type="url" name="linkedin" value="{{ old('linkedin',$parent['linkedin_url']) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
            </div>
        </div>
        <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700">{{ translate('update') }}</button></div>
    </form>
</div>
@endsection
