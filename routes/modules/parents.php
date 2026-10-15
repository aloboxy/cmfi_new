<?php

use App\Http\Controllers\ParentsController;
use Illuminate\Support\Facades\Route;

Route::get('/parents', [ParentsController::class, 'index'])->name('parents.index');
Route::get('/parents/view', [ParentsController::class, 'index']);

Route::match(['get','post'],'/parents/add', [ParentsController::class, 'create'])->name('parents.create');

Route::match(['get','post'],'/parents/profile/{id}', [ParentsController::class, 'profile'])->name('parents.profile')->whereNumber('id');

Route::delete('/parents/delete/{id}', [ParentsController::class, 'destroy'])->name('parents.delete')->whereNumber('id');
Route::post('/parents/delete/{id}', [ParentsController::class, 'destroy'])->whereNumber('id');
Route::get('/parents/delete/{id}', [ParentsController::class, 'destroy'])->whereNumber('id');

Route::match(['get','post'],'/parents/disable_authentication', [ParentsController::class, 'disableAuthentication'])->name('parents.disable_authentication');

Route::post('/parents/change_password', [ParentsController::class, 'changePassword'])->name('parents.change_password');

Route::get('/parents/select_child/{id}', [ParentsController::class, 'selectChild'])->name('parents.select_child')->whereNumber('id');
Route::get('/parents/my_children/{id?}', [ParentsController::class, 'myChildren'])->name('parents.my_children');
Route::get('/parents/my_children', [ParentsController::class, 'myChildren']);
