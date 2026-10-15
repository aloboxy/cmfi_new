@extends('layouts.app')
@section('title', translate('add_parent'))
@section('content')
<div class="space-y-5">
    <div class="flex items-center gap-2 text-[13px] text-slate-500">
        <a href="{{ route('parents.index') }}" class="grid size-7 place-items-center rounded-lg bg-white border border-slate-200"><x-heroicon-o-arrow-left class="size-4"/></a>
        <span class="font-semibold text-slate-800">{{ translate('add_parent') }}</span>
    </div>
    <form method="POST" action="{{ route('parents.create') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 font-bold text-sm text-slate-800">{{ translate('parents_details') }}</div>
            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                @if(is_superadmin_loggedin())
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('branch') }} <span class="text-red-500">*</span></label>
                    <select name="branch_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-white">
                        <option value="">{{ translate('select') }}</option>
                        @foreach($branches as $b)<option value="{{ $b->id }}" @selected(old('branch_id')==$b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                    @error('branch_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                @endif
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('name') }} <span class="text-red-500">*</span></label><input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('relation') }} <span class="text-red-500">*</span></label><input type="text" name="relation" value="{{ old('relation') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('relation')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('occupation') }} <span class="text-red-500">*</span></label><input type="text" name="occupation" value="{{ old('occupation') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('occupation')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('father_name') }}</label><input type="text" name="father_name" value="{{ old('father_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mother_name') }}</label><input type="text" name="mother_name" value="{{ old('mother_name') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('income') }}</label><input type="text" name="income" value="{{ old('income') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('income')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('education') }}</label><input type="text" name="education" value="{{ old('education') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('city') }}</label><input type="text" name="city" value="{{ old('city') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('state') }}</label><input type="text" name="state" value="{{ old('state') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('mobile_no') }} <span class="text-red-500">*</span></label><input type="text" name="mobileno" value="{{ old('mobileno') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('mobileno')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div class="sm:col-span-3"><label class="block text-sm font-medium text-slate-700 mb-1.5">{{ translate('address') }}</label><textarea name="address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('address') }}</textarea></div>
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
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Facebook</label><input type="url" name="facebook" value="{{ old('facebook') }}" placeholder="https://facebook.com/..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('facebook')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Twitter</label><input type="url" name="twitter" value="{{ old('twitter') }}" placeholder="https://twitter.com/..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('twitter')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Linkedin</label><input type="url" name="linkedin" value="{{ old('linkedin') }}" placeholder="https://linkedin.com/..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">@error('linkedin')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            </div>
        </div>
        <div class="flex justify-end"><button type="submit" class="rounded-xl bg-[#2563eb] px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700">{{ translate('save') }}</button></div>
    </form>
</div>
@endsection
