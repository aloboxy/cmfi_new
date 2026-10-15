<?php

use App\Http\Controllers\FeesController;
use Illuminate\Support\Facades\Route;

// Fees Type
Route::match(['get','post'], '/fees/type', [FeesController::class, 'type'])->name('fees.type');
Route::match(['get','post'], '/fees/type_edit/{id}', [FeesController::class, 'typeEdit'])->name('fees.type_edit')->whereNumber('id');
Route::delete('/fees/type_delete/{id}', [FeesController::class, 'typeDelete'])->name('fees.type_delete')->whereNumber('id');
Route::post('/fees/type_delete/{id}', [FeesController::class, 'typeDelete'])->whereNumber('id');

// Fees Group
Route::match(['get','post'], '/fees/group', [FeesController::class, 'group'])->name('fees.group');
Route::match(['get','post'], '/fees/group_edit/{id}', [FeesController::class, 'groupEdit'])->name('fees.group_edit')->whereNumber('id');
Route::delete('/fees/group_delete/{id}', [FeesController::class, 'groupDelete'])->name('fees.group_delete')->whereNumber('id');
Route::post('/fees/group_delete/{id}', [FeesController::class, 'groupDelete'])->whereNumber('id');

// Fees Master - alias to group for navigation compatibility
Route::match(['get','post'], '/fees/master', [FeesController::class, 'master'])->name('fees.master');

// Fine
Route::match(['get','post'], '/fees/fine', [FeesController::class, 'fine'])->name('fees.fine');
Route::match(['get','post'], '/fees/fine_setup', [FeesController::class, 'fine'])->name('fees.fine_setup');
Route::match(['get','post'], '/fees/fine_setup_edit/{id}', [FeesController::class, 'fineEdit'])->name('fees.fine_edit')->whereNumber('id');
Route::delete('/fees/fine_delete/{id}', [FeesController::class, 'fineDelete'])->name('fees.fine_delete')->whereNumber('id');
Route::post('/fees/fine_delete/{id}', [FeesController::class, 'fineDelete'])->whereNumber('id');

// Allocation
Route::match(['get','post'], '/fees/allocation', [FeesController::class, 'allocation'])->name('fees.allocation');

// Collect / Invoice
Route::match(['get','post'], '/fees/collect', [FeesController::class, 'collect'])->name('fees.collect');
Route::get('/fees/invoice/{id}', [FeesController::class, 'invoice'])->name('fees.invoice')->whereNumber('id');
Route::post('/fees/fee_add', [FeesController::class, 'feeAdd'])->name('fees.fee_add');

// Reminder
Route::match(['get','post'], '/fees/reminder', [FeesController::class, 'reminder'])->name('fees.reminder');
Route::match(['get','post'], '/fees/reminder_edit/{id}', [FeesController::class, 'editReminder'])->name('fees.reminder_edit')->whereNumber('id');
Route::delete('/fees/reminder_delete/{id}', [FeesController::class, 'reminderDelete'])->name('fees.reminder_delete')->whereNumber('id');
Route::post('/fees/reminder_delete/{id}', [FeesController::class, 'reminderDelete'])->whereNumber('id');

// Reports
Route::match(['get','post'], '/fees/due_report', [FeesController::class, 'dueReport'])->name('fees.due_report');

// Ajax helpers
Route::post('/fees/getBalanceByType', [FeesController::class, 'getBalanceByType'])->name('fees.getBalanceByType');
Route::post('/fees/getTypeByBranch', [FeesController::class, 'getTypeByBranch'])->name('fees.getTypeByBranch');
Route::post('/fees/getGroupByBranch', [FeesController::class, 'getGroupByBranch'])->name('fees.getGroupByBranch');
Route::post('/fees/getTypeByGroup', [FeesController::class, 'getTypeByGroup'])->name('fees.getTypeByGroup');
