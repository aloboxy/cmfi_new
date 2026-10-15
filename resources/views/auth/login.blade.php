<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ translate('login') }} | {{ $global_config->institute_name ?? 'CMFI School Management System' }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800 dark:text-gray-200">
<div class="flex min-h-screen">

    {{-- hero panel — customizable via theme_settings: primary_color, login_button_color, slider images --}}
    @php
      $theme = $theme_config ?? (object)[];
      $heroBg = $theme->primary_color ?? '#1D4ED8';
      $heroBg2 = $theme->sidebar_bg ?? '#0D1B3E';
      $heroTitle = $theme->login_hero_title ?? null;
      $heroText = $theme->login_hero_text ?? null;
      $slider1 = file_exists(public_path('uploads/login_image/slider_1.jpg')) ? asset('uploads/login_image/slider_1.jpg') : null;
      $schoolImg = $global_config->school_image ?? null;
      $hasSchoolImg = !empty($schoolImg) && file_exists(public_path($schoolImg));
      $loginLogo = $hasSchoolImg ? asset($schoolImg) : (file_exists(public_path('uploads/app_image/logo-small.png')) ? asset('uploads/app_image/logo-small.png') : (file_exists(public_path('uploads/app_image/logo.png')) ? asset('uploads/app_image/logo.png') : null));
    @endphp
    <aside class="relative hidden lg:flex lg:w-1/2 xl:w-[55%] flex-col justify-between overflow-hidden p-12 text-white" style="background: linear-gradient(135deg, {{ $heroBg }} 0%, {{ $heroBg2 }} 100%)">
        @if($slider1)
          <div class="absolute inset-0 opacity-20" style="background-image:url('{{ $slider1 }}'); background-size:cover; background-position:center;"></div>
          <div class="absolute inset-0" style="background: linear-gradient(135deg, {{ $heroBg }}CC 0%, {{ $heroBg2 }}CC 100%)"></div>
        @endif
        {{-- Dot overlay --}}
        <div class="absolute inset-0 bg-dots opacity-40 pointer-events-none z-0"></div>
        {{-- Glow blobs --}}
        <div class="glow bg-white/10 w-96 h-96 -top-20 -left-20"></div>
        <div class="glow bg-white/5 w-[500px] h-[500px] -bottom-40 -right-20"></div>

        <div class="relative z-10 flex items-center gap-3">
            @if($loginLogo)
              <img src="{{ $loginLogo }}" alt="School Logo" class="size-10 rounded-xl object-cover bg-white p-1.5 shadow">
            @else
              <span class="text-3xl leading-none">&#127891;</span>
            @endif
            <div class="leading-tight">
                <p class="text-lg font-semibold tracking-tight">{{ $global_config->institute_name ?? 'CMFI School' }}</p>
                <p class="text-xs text-blue-200/80 font-medium tracking-wide uppercase">{{ translate('management_portal') }}</p>
            </div>
        </div>

        <div class="relative z-10 max-w-lg">
            @if($heroTitle)
              <h1 class="text-5xl font-extrabold leading-tight tracking-tight text-white">{{ $heroTitle }}</h1>
              @if($heroText)<p class="mt-6 text-lg text-white/90 leading-relaxed font-light">{{ $heroText }}</p>@endif
            @else
              <h1 class="text-5xl font-extrabold leading-tight tracking-tight text-white">
                  {{ translate('empowering') }} <span class="bg-gradient-to-r from-blue-300 to-cyan-200 bg-clip-text text-transparent">{{ translate('education') }}</span><br>
                  &amp; {{ translate('administration') }}
              </h1>
              <p class="mt-6 text-lg text-blue-100/90 leading-relaxed font-light">
                  {{ translate('access_your_classes_grades_payroll_fees_and_records_through_our_unified_school_management_dashboard') }}
              </p>
            @endif
        </div>

        <dl class="relative z-10 grid grid-cols-3 gap-6 text-sm border-t border-white/10 pt-6">
            <div>
                <dt class="font-bold text-white tracking-wide">All-in-One</dt>
                <dd class="text-blue-200/80 mt-1">{{ translate('system') }}</dd>
            </div>
            <div>
                <dt class="font-bold text-white tracking-wide">Real-time</dt>
                <dd class="text-blue-200/80 mt-1">{{ translate('analytics') }}</dd>
            </div>
            <div>
                <dt class="font-bold text-white tracking-wide">Secure</dt>
                <dd class="text-blue-200/80 mt-1">{{ translate('access') }}</dd>
            </div>
        </dl>
    </aside>

    {{-- form panel --}}
    <main class="flex w-full items-center justify-center bg-slate-50 dark:bg-gray-950 p-8 lg:w-1/2">
        <div class="w-full max-w-md">

            <div class="mb-8 text-center">
                @if($loginLogo)
                  <img src="{{ $loginLogo }}" alt="School Logo" class="mx-auto mb-5 h-20 w-auto max-w-[220px] rounded-2xl object-cover bg-white p-1 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700">
                @else
                  <img src="{{ asset('uploads/app_image/logo-small.png') }}"
                       onerror="this.style.display='none'; this.insertAdjacentHTML('afterend','<span style=&quot;font-size:48px&quot;>&#127979;</span>')"
                       alt="logo" class="mx-auto mb-5 h-16 w-auto transition-transform hover:scale-105 duration-300">
                @endif
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $global_config->institute_name ?? 'CMFI School' }}</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-gray-400 font-medium">{{ translate('sign_in_to_your_account_to_continue') }}</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 px-4 py-3.5 text-sm text-red-700 ring-1 ring-red-200/60 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-900/50">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-2"><span class="size-1.5 rounded-full bg-red-500"></span> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" x-data="{ show: false }"
                  class="space-y-6 rounded-2xl bg-white p-8 shadow-xl shadow-slate-200/50 ring-1 ring-slate-100 dark:bg-gray-900 dark:ring-white/5 dark:shadow-none">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">{{ translate('email_address') }}</label>
                    <input type="text" id="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                           placeholder="{{ translate('enter_username_or_email') }}"
                           class="w-full rounded-xl px-4 py-3 text-sm border-0 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all duration-200 ring-1 ring-slate-200 dark:ring-slate-700">
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">{{ translate('password') }}</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                               autocomplete="current-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                               class="w-full rounded-xl px-4 py-3 pr-12 text-sm border-0 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all duration-200 ring-1 ring-slate-200 dark:ring-slate-700">
                        <button type="button" @click="show = ! show"
                                class="absolute inset-y-0 right-0 grid w-12 place-items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                aria-label="Show/hide password">
                            <x-heroicon-o-eye x-show="!show" class="size-5" />
                            <x-heroicon-o-eye-slash x-show="show" x-cloak style="display:none" class="size-5" />
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex select-none items-center gap-2 text-slate-600 dark:text-slate-400 font-medium">
                        <input type="checkbox" name="remember"
                               class="size-4.5 rounded border-slate-300 text-blue-600 focus:ring-blue-600/20 dark:border-slate-700 dark:bg-slate-800">
                        {{ translate('remember_me') }}
                    </label>
                    <a href="#" class="font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400 transition-colors">
                        {{ translate('forgot_password') }}?
                    </a>
                </div>

                @php $btnColor = $theme->login_button_color ?? $theme->primary_color ?? '#1D4ED8'; @endphp
                <button type="submit"
                        class="w-full rounded-xl px-4 py-3 text-sm font-bold text-white shadow-md hover:opacity-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 transition-all"
                        style="background: {{ $btnColor }}; box-shadow: 0 4px 15px {{ $btnColor }}33; --tw-ring-color: {{ $btnColor }}">
                    {{ translate('login') }}
                </button>
            </form>

            <p class="mt-6 text-center text-xs text-slate-400">
                {{ $global_config->institute_name ?? $global_config->footer_text ?? 'CMFI School' }} &copy; {{ date('Y') }}
            </p>
        </div>
    </main>
</div>
</body>
</html>
