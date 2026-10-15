<?php
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::match(['get','post'], '/attendance/student', [AttendanceController::class, 'student'])->name('attendance.student');
Route::post('/attendance/student_save', [AttendanceController::class, 'studentSave'])->name('attendance.student_save');
Route::match(['get','post'], '/attendance/staff', [AttendanceController::class, 'staff'])->name('attendance.staff');
Route::post('/attendance/staff_save', [AttendanceController::class, 'staff'])->name('attendance.staff_save');

// legacy aliases
Route::match(['get','post'], '/attendance/student_entry', [AttendanceController::class, 'student'])->name('attendance.student_entry');
Route::match(['get','post'], '/attendance/employees_entry', [AttendanceController::class, 'staff'])->name('attendance.employees_entry');
