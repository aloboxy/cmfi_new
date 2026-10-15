<?php

use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

// Branch — superadmin only, maps to legacy Branch controller
Route::get('/branch', [BranchController::class, 'index'])->name('branch.index');
Route::post('/branch', [BranchController::class, 'store'])->name('branch.store');
Route::get('/branch/{id}/edit', [BranchController::class, 'edit'])->name('branch.edit')->whereNumber('id');
Route::put('/branch/{id}', [BranchController::class, 'update'])->name('branch.update')->whereNumber('id');
Route::post('/branch/{id}', [BranchController::class, 'update'])->whereNumber('id'); // legacy form posts to same URI
Route::delete('/branch/{id}', [BranchController::class, 'destroy'])->name('branch.destroy')->whereNumber('id');
