<?php
use App\Http\Controllers\AttachmentsController;
use Illuminate\Support\Facades\Route;

Route::get('/attachments', [AttachmentsController::class, 'index'])->name('attachments.index');
Route::get('/attachments/edit/{id}', [AttachmentsController::class, 'edit'])->name('attachments.edit')->whereNumber('id');
Route::post('/attachments/save', [AttachmentsController::class, 'save'])->name('attachments.save');
Route::post('/attachments', [AttachmentsController::class, 'save'])->name('attachments.store');
Route::delete('/attachments/{id}', [AttachmentsController::class, 'delete'])->name('attachments.destroy')->whereNumber('id');
Route::post('/attachments/delete/{id}', [AttachmentsController::class, 'delete'])->whereNumber('id');
Route::get('/attachments/download', [AttachmentsController::class, 'download'])->name('attachments.download');

Route::get('/attachments/type', [AttachmentsController::class, 'type'])->name('attachments.type');
Route::post('/attachments/type', [AttachmentsController::class, 'typeStore'])->name('attachments.type_store');
Route::post('/attachments/type_edit', [AttachmentsController::class, 'typeEdit'])->name('attachments.type_edit');
Route::delete('/attachments/type/{id}', [AttachmentsController::class, 'typeDelete'])->name('attachments.type_delete')->whereNumber('id');
Route::post('/attachments/type_delete/{id}', [AttachmentsController::class, 'typeDelete'])->whereNumber('id');
