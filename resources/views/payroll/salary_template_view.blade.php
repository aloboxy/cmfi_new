<div class="space-y-4">
<h3 class="text-lg font-bold text-slate-800">{{ $template->name }}</h3>
<p class="text-sm text-slate-600">{{ translate('basic_salary') }}: {{ $template->basic_salary }} | {{ translate('overtime') }}: {{ $template->overtime_salary }}</p>
<div class="grid grid-cols-2 gap-4">
<div><h4 class="font-semibold text-sm mb-2">{{ translate('allowances') }}</h4><ul class="text-sm space-y-1">@forelse($allowances as $a)<li class="flex justify-between border-b py-1"><span>{{ $a->name }}</span><span>{{ $a->amount }}</span></li>@empty<li class="text-slate-500">{{ translate('no_information_available') }}</li>@endforelse</ul></div>
<div><h4 class="font-semibold text-sm mb-2">{{ translate('deductions') }}</h4><ul class="text-sm space-y-1">@forelse($deductions as $d)<li class="flex justify-between border-b py-1"><span>{{ $d->name }}</span><span>{{ $d->amount }}</span></li>@empty<li class="text-slate-500">{{ translate('no_information_available') }}</li>@endforelse</ul></div>
</div>
</div>
