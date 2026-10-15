<?php
use App\Http\Controllers\LiveClassController;
use Illuminate\Support\Facades\Route;

Route::get('/liveclass', [LiveClassController::class, 'index'])->name('liveclass.index');
Route::post('/liveclass', [LiveClassController::class, 'store'])->name('liveclass.store');
Route::get('/liveclass/edit/{id}', [LiveClassController::class, 'edit'])->name('liveclass.edit')->whereNumber('id');
Route::put('/liveclass/{id}', [LiveClassController::class, 'update'])->name('liveclass.update')->whereNumber('id');
Route::post('/liveclass/{id}', [LiveClassController::class, 'update'])->whereNumber('id');
Route::delete('/liveclass/{id}', [LiveClassController::class, 'destroy'])->name('liveclass.destroy')->whereNumber('id');
Route::post('/liveclass/delete/{id}', [LiveClassController::class, 'destroy'])->whereNumber('id');
Route::get('/liveclass/host', [LiveClassController::class, 'host'])->name('liveclass.host');
Route::post('/liveclass/hostModal', [LiveClassController::class, 'host'])->name('liveclass.hostModal');

// legacy alias
Route::get('/live_class', [LiveClassController::class, 'index']);
Route::post('/live_class', [LiveClassController::class, 'store']);
