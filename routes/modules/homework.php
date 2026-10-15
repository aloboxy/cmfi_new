<?php
use App\Http\Controllers\HomeworkController;
use Illuminate\Support\Facades\Route;

Route::match(['get','post'], '/homework', [HomeworkController::class, 'index'])->name('homework.index');
Route::get('/homework/create', [HomeworkController::class, 'create'])->name('homework.create');
Route::get('/homework/add', [HomeworkController::class, 'create'])->name('homework.add');
Route::post('/homework/save', [HomeworkController::class, 'store'])->name('homework.save');
Route::post('/homework', [HomeworkController::class, 'store'])->name('homework.store');
Route::get('/homework/edit/{id}', [HomeworkController::class, 'edit'])->name('homework.edit')->whereNumber('id');
Route::put('/homework/{id}', [HomeworkController::class, 'update'])->name('homework.update')->whereNumber('id');
Route::post('/homework/{id}', [HomeworkController::class, 'update'])->whereNumber('id');
Route::delete('/homework/{id}', [HomeworkController::class, 'destroy'])->name('homework.destroy')->whereNumber('id');
Route::post('/homework/delete/{id}', [HomeworkController::class, 'destroy'])->whereNumber('id');

Route::get('/homework/evaluate/{id}', [HomeworkController::class, 'evaluate'])->name('homework.evaluate')->whereNumber('id');
Route::post('/homework/evaluate_save', [HomeworkController::class, 'evaluateSave'])->name('homework.evaluate_save');
Route::match(['get','post'], '/homework/report', [HomeworkController::class, 'report'])->name('homework.report');
Route::post('/homework/evaluateModal', [HomeworkController::class, 'evaluate'])->name('homework.evaluateModal');
Route::post('/homework/evaluateDetails', [HomeworkController::class, 'evaluate'])->name('homework.evaluateDetails');
