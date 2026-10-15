<?php

use App\Http\Controllers\HostelController;
use Illuminate\Support\Facades\Route;

// Hostel
Route::match(['get','post'], '/hostels', [HostelController::class, 'index'])->name('hostels.index');
Route::match(['get','post'], '/hostels/edit/{id}', [HostelController::class, 'edit'])->name('hostels.edit')->whereNumber('id');
Route::delete('/hostels/delete/{id}', [HostelController::class, 'delete'])->name('hostels.delete')->whereNumber('id');
Route::post('/hostels/delete/{id}', [HostelController::class, 'delete'])->whereNumber('id');

// Category
Route::match(['get','post'], '/hostels/category', [HostelController::class, 'category'])->name('hostels.category');
Route::post('/hostels/category_edit', [HostelController::class, 'categoryEdit'])->name('hostels.category_edit');
Route::delete('/hostels/category_delete/{id}', [HostelController::class, 'categoryDelete'])->name('hostels.category_delete')->whereNumber('id');
Route::post('/hostels/category_delete/{id}', [HostelController::class, 'categoryDelete'])->whereNumber('id');

// Rooms
Route::match(['get','post'], '/hostels/room', [HostelController::class, 'rooms'])->name('hostels.rooms');
Route::match(['get','post'], '/hostels/room_edit/{id}', [HostelController::class, 'roomEdit'])->name('hostels.room_edit')->whereNumber('id');
Route::delete('/hostels/room_delete/{id}', [HostelController::class, 'roomDelete'])->name('hostels.room_delete')->whereNumber('id');
Route::post('/hostels/room_delete/{id}', [HostelController::class, 'roomDelete'])->whereNumber('id');

// Assign / allocation report
Route::match(['get','post'], '/hostels/assign', [HostelController::class, 'assign'])->name('hostels.assign');
Route::match(['get','post'], '/hostels/allocation', [HostelController::class, 'assign'])->name('hostels.allocation');
Route::post('/hostels/getRoomByHostel', [HostelController::class, 'getRoomByHostel'])->name('hostels.getRoomByHostel');
