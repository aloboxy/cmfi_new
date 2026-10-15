@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="space-y-4">
  {{-- Top 4 cards — colorful stat cards matching reference --}}
  <style>
    .stat-cards-container {
      display: grid;
      grid-template-columns: repeat(1, minmax(0, 1fr));
      gap: 1rem;
    }
    @media (min-width: 640px) {
      .stat-cards-container {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }
    @media (min-width: 1024px) {
      .stat-cards-container {
        grid-template-columns: repeat(4, minmax(0, 1fr));
      }
    }
    .custom-stat-card {
      position: relative;
      overflow: hidden;
      border-radius: 1.25rem;
      padding: 1.25rem 1.25rem;
      color: #ffffff !important;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      display: flex;
      align-items: center;
      min-height: 108px;
    }
    .custom-stat-card * {
      color: #ffffff !important;
    }
    .stat-card-blue {
      background: linear-gradient(135deg, #1b457e 0%, #21518f 50%, #285d9f 100%) !important;
    }
    .stat-card-green {
      background: linear-gradient(135deg, #00b471 0%, #0ec77f 50%, #15d688 100%) !important;
    }
    .stat-card-purple {
      background: linear-gradient(135deg, #7b12cb 0%, #9417dc 50%, #b21ce6 100%) !important;
    }
    .stat-card-orange {
      background: linear-gradient(135deg, #d9733e 0%, #ba5e51 55%, #6d4d62 100%) !important;
    }
    .stat-icon-box {
      width: 3.75rem;
      height: 3.75rem;
      border-radius: 1rem;
      background: rgba(255, 255, 255, 0.22);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.35);
      margin-right: 1.15rem;
    }
    .stat-watermark-icon {
      position: absolute;
      right: -0.75rem;
      bottom: -0.75rem;
      width: 6.5rem;
      height: 6.5rem;
      opacity: 0.12;
      pointer-events: none;
      fill: #ffffff;
    }
  </style>

  <div class="stat-cards-container">
    <!-- Card 1: Students -->
    <div class="custom-stat-card stat-card-blue">
      <div class="stat-icon-box">
        <svg viewBox="0 0 24 24" style="width: 1.85rem; height: 1.85rem; fill: #ffffff;">
          <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
          <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
        </svg>
      </div>
      <div style="flex: 1; min-width: 0; z-index: 2;">
        <div style="font-size: 1.75rem; font-weight: 800; line-height: 1; letter-spacing: -0.025em;">{{ $total_students ?? 5 }}</div>
        <div style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 0.35rem; opacity: 0.95;">STUDENTS</div>
        <div style="font-size: 0.68rem; font-weight: 400; opacity: 0.75; margin-top: 0.65rem;">Total Strength</div>
      </div>
      <svg class="stat-watermark-icon" viewBox="0 0 24 24">
        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
        <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
      </svg>
    </div>

    <!-- Card 2: Teachers -->
    <div class="custom-stat-card stat-card-green">
      <div class="stat-icon-box">
        <svg viewBox="0 0 24 24" style="width: 1.85rem; height: 1.85rem; fill: #ffffff;">
          <path d="M10 4h11a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H10V4z" opacity="0.95"/>
          <path d="M14 14v2h3a1 1 0 1 1 0 2h-6a1 1 0 1 1 0-2h1v-2" opacity="0.95"/>
          <circle cx="5.5" cy="8.5" r="2.5"/>
          <path d="M2 17c0-2.2 2-3.5 4-3.5 1.4 0 2.8.7 3.5 1.8.3.4.5.9.5 1.7v1H2v-1z"/>
        </svg>
      </div>
      <div style="flex: 1; min-width: 0; z-index: 2;">
        <div style="font-size: 1.75rem; font-weight: 800; line-height: 1; letter-spacing: -0.025em;">{{ $total_teachers ?? 15 }}</div>
        <div style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 0.35rem; opacity: 0.95;">TEACHERS</div>
        <div style="font-size: 0.68rem; font-weight: 400; opacity: 0.75; margin-top: 0.65rem;">Total Strength</div>
      </div>
      <svg class="stat-watermark-icon" viewBox="0 0 24 24">
        <path d="M10 4h11a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H10V4z"/>
        <path d="M14 14v2h3a1 1 0 1 1 0 2h-6a1 1 0 1 1 0-2h1v-2"/>
        <circle cx="5.5" cy="8.5" r="2.5"/>
        <path d="M2 17c0-2.2 2-3.5 4-3.5 1.4 0 2.8.7 3.5 1.8.3.4.5.9.5 1.7v1H2v-1z"/>
      </svg>
    </div>

    <!-- Card 3: Employee -->
    <div class="custom-stat-card stat-card-purple">
      <div class="stat-icon-box">
        <svg viewBox="0 0 24 24" style="width: 1.85rem; height: 1.85rem; fill: #ffffff;">
          <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
        </svg>
      </div>
      <div style="flex: 1; min-width: 0; z-index: 2;">
        <div style="font-size: 1.75rem; font-weight: 800; line-height: 1; letter-spacing: -0.025em;">{{ $total_employees ?? 5 }}</div>
        <div style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 0.35rem; opacity: 0.95;">EMPLOYEE</div>
        <div style="font-size: 0.68rem; font-weight: 400; opacity: 0.75; margin-top: 0.65rem;">Total Strength</div>
      </div>
      <svg class="stat-watermark-icon" viewBox="0 0 24 24">
        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
      </svg>
    </div>

    <!-- Card 4: Parents -->
    <div class="custom-stat-card stat-card-orange">
      <div class="stat-icon-box">
        <svg viewBox="0 0 24 24" style="width: 1.85rem; height: 1.85rem; fill: #ffffff;">
          <circle cx="9" cy="8" r="3"/>
          <circle cx="16.5" cy="9.5" r="2.5"/>
          <path d="M3 18c0-2.7 3.5-4 6-4s6 1.3 6 4v1H3v-1zm13.5-3c-.4 0-.8.05-1.2.14 1.1.75 1.7 1.8 1.7 2.86v1h4v-1c0-1.8-2.3-3-4.5-3z"/>
        </svg>
      </div>
      <div style="flex: 1; min-width: 0; z-index: 2;">
        <div style="font-size: 1.75rem; font-weight: 800; line-height: 1; letter-spacing: -0.025em;">{{ $total_parents ?? 4 }}</div>
        <div style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 0.35rem; opacity: 0.95;">PARENTS</div>
        <div style="font-size: 0.68rem; font-weight: 400; opacity: 0.75; margin-top: 0.65rem;">Total Strength</div>
      </div>
      <svg class="stat-watermark-icon" viewBox="0 0 24 24">
        <circle cx="9" cy="8" r="3"/>
        <circle cx="16.5" cy="9.5" r="2.5"/>
        <path d="M3 18c0-2.7 3.5-4 6-4s6 1.3 6 4v1H3v-1zm13.5-3c-.4 0-.8.05-1.2.14 1.1.75 1.7 1.8 1.7 2.86v1h4v-1c0-1.8-2.3-3-4.5-3z"/>
      </svg>
    </div>
  </div>

  {{-- Second row: Daily Attendance + two small cards --}}
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
    <!-- Attendance Overview Card -->
    <div class="lg:col-span-8 modern-card p-5">
      <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-bold text-slate-800">Daily Attendance Overview</h3>
          <p class="text-xs text-slate-400 mt-0.5">Real-time student & staff presence metrics</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="flex gap-4 text-xs font-semibold text-slate-600">
            <span class="flex items-center gap-1.5"><span class="size-2.5 rounded-full bg-blue-600"></span> Employee</span>
            <span class="flex items-center gap-1.5"><span class="size-2.5 rounded-full bg-blue-300"></span> Student</span>
          </div>
          <span class="modern-badge modern-badge-success">Active</span>
        </div>
      </div>
      <div class="h-[230px]">
        <canvas data-chart="{{ json_encode([
          'type'=>'bar',
          'data'=>[
            'labels'=>$daily_attendance['labels'],
            'datasets'=>[
              ['label'=>'Employee','data'=>$daily_attendance['employee'],'backgroundColor'=>'#2563eb','borderRadius'=>6],
              ['label'=>'Student','data'=>$daily_attendance['student'],'backgroundColor'=>'#93c5fd','borderRadius'=>6],
            ]
          ],
          'options'=>[
            'responsive'=>true,
            'maintainAspectRatio'=>false,
            'plugins'=>['legend'=>['display'=>false]],
            'scales'=>[
              'y'=>['beginAtZero'=>true,'max'=>20,'grid'=>['color'=>'rgba(241,245,249,1)'],'ticks'=>['stepSize'=>5,'font'=>['size'=>11]]],
              'x'=>['grid'=>['display'=>false],'ticks'=>['font'=>['size'=>11]]]
            ]
          ]
        ]) }}"></canvas>
      </div>
    </div>

    <!-- Mini Stat Cards -->
    <div class="lg:col-span-4 grid grid-cols-2 gap-4">
      <div class="modern-card p-5 text-center flex flex-col justify-between">
        <div>
          <div class="mx-auto grid size-12 place-items-center rounded-2xl bg-pink-50 text-pink-500 mb-3 shadow-xs border border-pink-100">
            <x-heroicon-o-users class="size-6" />
          </div>
          <p class="text-2xl font-extrabold text-slate-800 tracking-tight leading-none">{{ $total_alumni ?? 3 }}</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">Total Alumni<br>Members</p>
        </div>
        <div class="mt-4 h-9">
          <canvas data-chart="{{ json_encode(['type'=>'bar','data'=>['labels'=>['','','','',''],'datasets'=>[['data'=>[2,4,1,3,2],'backgroundColor'=>'#f472b6','borderRadius'=>4]]],'options'=>['responsive'=>true,'maintainAspectRatio'=>false,'plugins'=>['legend'=>['display'=>false]],'scales'=>['x'=>['display'=>false],'y'=>['display'=>false]]]]) }}"></canvas>
        </div>
      </div>

      <div class="modern-card p-5 text-center flex flex-col justify-between">
        <div>
          <div class="mx-auto grid size-12 place-items-center rounded-2xl bg-blue-50 text-blue-600 mb-3 shadow-xs border border-blue-100">
            <x-heroicon-o-user-circle class="size-6" />
          </div>
          <p class="text-2xl font-extrabold text-slate-800 tracking-tight leading-none">{{ $total_admin_users ?? 2 }}</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">Total Admin<br>Users</p>
        </div>
        <div class="mt-4 h-9">
          <canvas data-chart="{{ json_encode(['type'=>'bar','data'=>['labels'=>['','','','',''],'datasets'=>[['data'=>[1,2,3,1,2],'backgroundColor'=>'#60a5fa','borderRadius'=>4]]],'options'=>['responsive'=>true,'maintainAspectRatio'=>false,'plugins'=>['legend'=>['display'=>false]],'scales'=>['x'=>['display'=>false],'y'=>['display'=>false]]]]) }}"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- Third row: Task manager + To Do --}}
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
    <!-- Task Manager Card -->
    <div class="lg:col-span-8 modern-card overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-bold text-slate-800">Task Manager</h3>
          <p class="text-xs text-slate-400 mt-0.5">Pending and scheduled operational tasks</p>
        </div>
        <span class="rounded-full bg-slate-100 text-slate-600 text-xs px-2.5 py-0.5 font-semibold">{{ count($tasks ?? []) }} Tasks</span>
      </div>
      <div class="overflow-x-auto">
        <table class="modern-table">
          <thead>
            <tr>
              <th style="width: 40px;">#</th>
              <th>Task Description</th>
              <th>Priority</th>
              <th>Task Date</th>
              <th>Status</th>
              <th>Assignee</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tasks as $i=>$t)
              <tr>
                <td class="font-bold text-slate-400">{{ $i+1 }}</td>
                <td class="font-semibold text-blue-600">{{ $t['desc'] ?? $t['subject'] ?? '-' }}</td>
                <td>
                  @php $p = $t['priority'] ?? 'Low'; @endphp
                  <span class="modern-badge {{ $p==='Urgent' ? 'modern-badge-danger' : ($p==='High' ? 'modern-badge-warning' : 'modern-badge-info') }}">
                    {{ $p }}
                  </span>
                </td>
                <td class="text-slate-500 font-medium">{{ $t['date'] ?? '-' }}</td>
                <td>
                  @php $s = $t['status'] ?? 'Pending'; @endphp
                  <span class="modern-badge {{ $s==='Urgent' || $s==='Pending' ? 'modern-badge-warning' : 'modern-badge-success' }}">
                    {{ $s }}
                  </span>
                </td>
                <td>
                  <span class="inline-flex items-center gap-1.5 font-medium text-slate-700">
                    <span class="size-5 rounded-full bg-slate-200 text-[10px] font-bold grid place-items-center text-slate-600">{{ strtoupper(substr($t['user'] ?? 'U', 0, 1)) }}</span>
                    {{ $t['user'] ?? '-' }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-6 text-slate-400">No active tasks found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- To-Do Action Center Card -->
    <div class="lg:col-span-4 modern-card p-5 flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
          <h3 class="text-sm font-bold text-slate-800">Quick To-Do</h3>
          <span class="text-xs text-slate-400">Personal Notes</span>
        </div>
        <div class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Subject</label>
            <input type="text" placeholder="Task title..." class="modern-input">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
            <textarea placeholder="Add task notes or reminders..." rows="2" class="modern-input"></textarea>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Due Date</label>
            <input type="text" placeholder="YYYY-MM-DD" class="modern-input">
          </div>
          <div class="flex justify-end gap-2 pt-1">
            <button type="button" class="modern-btn modern-btn-primary w-full">
              <x-heroicon-o-plus class="size-4" /> Add Task
            </button>
          </div>
        </div>
      </div>

      <div class="pt-4 mt-4 border-t border-slate-100 space-y-2.5 max-h-48 overflow-y-auto">
        @forelse($todos as $todo)
          <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50/80 hover:bg-slate-100/80 border border-slate-100 transition">
            <span class="size-2 rounded-full bg-blue-500 mt-1.5 shrink-0"></span>
            <div class="min-w-0 flex-1 leading-tight">
              <p class="text-xs font-semibold text-slate-800">{{ $todo['subject'] ?? '-' }}</p>
              <p class="text-[10px] text-slate-400 mt-0.5">{{ $todo['date'] ?? '' }}</p>
            </div>
          </div>
        @empty
          <p class="text-xs text-center text-slate-400 py-2">No pending to-do items.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
