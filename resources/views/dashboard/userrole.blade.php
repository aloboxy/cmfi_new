@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="p-2 bg-[#f8f9fa] dark:bg-slate-900 -m-4 lg:-m-5">
<div class="max-w-[1600px] mx-auto bg-white dark:bg-slate-900 rounded-[24px] shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
  <div class="grid grid-cols-12 gap-0">
    {{-- Main content --}}
    <div class="col-span-12 bg-[#fcf9f7] dark:bg-slate-900 p-4 lg:p-6 space-y-4">
      <div class="grid grid-cols-12 gap-4">
        {{-- Bio --}}
        <div class="col-span-12 lg:col-span-4 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700">
          <div class="flex justify-between items-start">
            <h3 class="font-bold text-slate-800 dark:text-white">Bio</h3>
            <button class="size-6 grid place-items-center text-slate-400">⋯</button>
          </div>
          <div class="flex flex-col items-center mt-2">
            <div class="size-28 rounded-full p-1 border-2 border-[#3b5998] border-dashed">
              <img src="{{ get_image_url('student', $student->photo ?? '') }}" class="size-full rounded-full object-cover" onerror="this.src='https://i.pravatar.cc/200?u=carla'">
            </div>
            <h3 class="mt-3 font-bold text-slate-800 dark:text-white">{{ $student->first_name ?? 'Carla' }} {{ $student->last_name ?? 'Peter' }} @if($age)<span class="text-[#f4a6a0] font-normal">({{ $age }})</span>@endif</h3>
            <p class="text-xs text-slate-500">{{ $student->email ?? 'carlapeter@gmail.com' }}</p>
            <p class="text-xs text-slate-500">{{ $student->mobileno ?? '+ 88 9856418' }}</p>
            <div class="flex gap-2 mt-4">
              <span class="grid size-8 place-items-center rounded-full border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300"><x-heroicon-o-printer class="size-4" /></span>
              <span class="grid size-8 place-items-center rounded-full border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300"><x-heroicon-o-map-pin class="size-4" /></span>
              <span class="grid size-8 place-items-center rounded-full bg-[#3b5998] text-white"><x-heroicon-o-globe-alt class="size-4" /></span>
              <span class="grid size-8 place-items-center rounded-full border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300"><i class="fab fa-twitter text-xs"></i></span>
              <span class="grid size-8 place-items-center rounded-full border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300"><i class="fab fa-instagram text-xs"></i></span>
            </div>
          </div>
        </div>

        {{-- Middle --}}
        <div class="col-span-12 lg:col-span-4 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-100 dark:border-slate-700">
              <p class="text-xs text-slate-500">Events</p>
              <div class="flex items-center justify-between mt-2">
                <p class="text-2xl font-bold text-[#3b5998]">{{ $eventsCount }}</p>
                <span class="grid size-7 place-items-center rounded-full bg-[#3b5998] text-white"><x-heroicon-o-arrow-up-right class="size-4" /></span>
              </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-100 dark:border-slate-700">
              <p class="text-xs text-slate-500">Target achieved</p>
              <div class="flex items-center justify-between mt-2">
                <p class="text-2xl font-bold text-[#3b5998]">{{ $targetAchieved }}%</p>
                <span class="grid size-7 place-items-center rounded-full border border-slate-200 dark:border-slate-600"><x-heroicon-o-arrow-up-right class="size-3" /></span>
              </div>
            </div>
          </div>
          <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-100 dark:border-slate-700">
            <h4 class="font-bold text-slate-800 dark:text-white mb-3">Groups</h4>
            <div class="grid grid-cols-3 gap-3">
              @foreach($groups as $g)
                <div class="rounded-2xl border border-slate-100 dark:border-slate-700 p-3 text-center bg-white dark:bg-slate-800">
                  <p class="text-xs font-bold text-slate-800 dark:text-white">{{ $g['name'] }}</p>
                  <p class="text-[10px] text-slate-400">{{ $g['sub'] }}</p>
                  <button class="mt-3 w-full rounded-full bg-[#f4b393] text-xs font-semibold py-1.5 text-slate-800">Join</button>
                </div>
              @endforeach
            </div>
            <div class="flex items-center gap-2 mt-4">
              <button class="flex-1 rounded-full bg-[#3b5998] text-white text-xs font-semibold py-2">+ Create New Group</button>
              <div class="flex -space-x-2">
                <img src="https://i.pravatar.cc/100?img=12" class="size-7 rounded-full border-2 border-white dark:border-slate-800">
                <img src="https://i.pravatar.cc/100?img=5" class="size-7 rounded-full border-2 border-white dark:border-slate-800">
                <img src="https://i.pravatar.cc/100?img=8" class="size-7 rounded-full border-2 border-white dark:border-slate-800">
              </div>
            </div>
          </div>
        </div>

        {{-- Event Calendar --}}
        <div class="col-span-12 lg:col-span-4 bg-[#5a6a8a] rounded-2xl p-4 text-white">
          <div class="flex items-center justify-between">
            <h3 class="font-bold">Event Calendar</h3>
            <button class="text-white/60">⋯</button>
          </div>
          <div class="mt-3 bg-white rounded-xl p-1 flex text-xs font-semibold">
            <span class="flex-1 text-center py-1.5 rounded-lg bg-[#f4b393] text-slate-800">Day to day</span>
            <span class="flex-1 text-center py-1.5 text-slate-500">Events</span>
          </div>
          <div class="mt-3">
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold">Feb 2023</p>
              <div class="flex gap-1"><button class="size-6 grid place-items-center rounded-full hover:bg-white/10">‹</button><button class="size-6 grid place-items-center rounded-full hover:bg-white/10">›</button></div>
            </div>
            <div class="grid grid-cols-7 text-center text-[10px] leading-6 mt-2 text-white/60">
              <span>MO</span><span>TU</span><span>WE</span><span>TH</span><span>FR</span><span>SA</span><span>SU</span>
              <span class="col-start-6">01</span><span>02</span><span>03</span>
              <span>04</span><span>05</span><span>06</span><span>07</span><span>08</span><span>09</span><span>10</span>
              <span>11</span><span>12</span><span>13</span><span>14</span><span>15</span><span class="bg-[#f4b393] text-slate-800 rounded-full size-6 grid place-items-center mx-auto">16</span><span>17</span>
              <span>18</span><span>19</span><span>20</span><span>21</span><span>22</span><span>23</span><span>24</span>
              <span>25</span><span>26</span><span>27</span><span>28</span>
            </div>
          </div>
          {{-- mini events list from real data --}}
          <div class="mt-3 space-y-1">
            @foreach(array_slice($events,0,2) as $ev)
              <div class="rounded-lg bg-white/10 px-3 py-2 text-xs flex justify-between"><span>{{ $ev['title'] }}</span><span class="opacity-70">{{ $ev['start'] }}</span></div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4">
        {{-- Exam helds --}}
        <div class="col-span-12 lg:col-span-8 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
          <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white">Exam helds</h3>
            <div class="flex gap-1 text-slate-400"><x-heroicon-o-printer class="size-4" /><x-heroicon-o-arrow-down-tray class="size-4" /></div>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-xs">
              <thead><tr class="text-slate-400 text-left"><th class="px-3 py-2"><input type="checkbox" class="rounded"></th><th class="px-3 py-2">Exam Id</th><th class="px-3 py-2">Student Name</th><th class="px-3 py-2">Subject</th><th class="px-3 py-2">Class</th><th class="px-3 py-2">Status</th><th class="px-3 py-2">Sub. Date</th><th class="px-3 py-2"></th></tr></thead>
              <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                @foreach($examHelds as $row)
                  <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <td class="px-3 py-2"><input type="checkbox" class="rounded" @if($loop->first) checked @endif></td>
                    <td class="px-3 py-2 font-medium">{{ $row['exam_id'] }}</td>
                    <td class="px-3 py-2">{{ $row['student_name'] }}</td>
                    <td class="px-3 py-2">{{ $row['subject'] }}</td>
                    <td class="px-3 py-2">{{ $row['class'] }}</td>
                    <td class="px-3 py-2"><span class="text-[10px] font-bold {{ $row['status']==='Active' ? 'text-emerald-500' : ($row['status']==='Opened' ? 'text-orange-400' : 'text-slate-500') }}">{{ $row['status'] }}</span></td>
                    <td class="px-3 py-2">{{ $row['date'] }}</td>
                    <td class="px-3 py-2 text-slate-400">⋯</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="flex items-center justify-center gap-2 py-3 text-xs">
            <span class="size-6 grid place-items-center rounded-full bg-[#f4b393] text-white">1</span><span>2</span><span>…</span><span>10</span>
          </div>
        </div>

        {{-- Today's Timeline --}}
        <div class="col-span-12 lg:col-span-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-4">
          <h3 class="font-bold text-slate-800 dark:text-white">Today's Timeline</h3>
          <div class="mt-3 space-y-4">
            @foreach($timeline as $t)
              <div class="flex gap-3">
                <span class="mt-1 size-2 rounded-full border-2 {{ $loop->first ? 'border-[#f4b393] bg-[#f4b393]/30' : 'border-slate-200' }} shrink-0"></span>
                <div class="flex-1">
                  <p class="text-xs font-medium text-slate-800 dark:text-white leading-tight">{{ $t['title'] }}</p>
                  <p class="text-[10px] text-[#f4b393]">Class: {{ $t['class'] }}</p>
                </div>
                <span class="text-[10px] text-slate-400">{{ $t['time'] }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
