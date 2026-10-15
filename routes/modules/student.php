<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Admission - Create
Route::get('/student/add', [StudentController::class, 'create'])->name('student.create');
Route::post('/student/add', [StudentController::class, 'store'])->name('student.store');
// Legacy aliases
Route::get('/student/create', [StudentController::class, 'create']);
Route::post('/student/create', [StudentController::class, 'store']);

// CSV import
Route::match(['get','post'],'/student/csv_import', [StudentController::class, 'csvImport'])->name('student.csv_import');
Route::get('/student/csv_sample', [StudentController::class, 'csvSampleDownload'])->name('student.csv_sample');
Route::get('/student/csv_Sampledownloader', [StudentController::class, 'csvSampleDownload']);

// Category
Route::match(['get','post'],'/student/category', [StudentController::class, 'category'])->name('student.category');
Route::post('/student/category_edit', [StudentController::class, 'categoryEdit'])->name('student.category_edit');
Route::delete('/student/category_delete/{id}', [StudentController::class, 'categoryDelete'])->name('student.category_delete')->whereNumber('id');
Route::post('/student/category_delete/{id}', [StudentController::class, 'categoryDelete'])->whereNumber('id');
Route::post('/student/categoryDetails', [StudentController::class, 'categoryDetails'])->name('student.categoryDetails');

// View / list
Route::get('/student', [StudentController::class, 'index'])->name('student.index');
Route::get('/student/view', [StudentController::class, 'index']);

// Profile
Route::match(['get','post'],'/student/profile/{id}', [StudentController::class, 'profile'])->name('student.profile')->whereNumber('id');

// Delete / bulk
Route::delete('/student/delete/{id}', [StudentController::class, 'delete'])->name('student.delete')->whereNumber('id');
Route::post('/student/delete_data/{eid}/{sid}', [StudentController::class, 'delete'])->whereNumber(['eid','sid']);
Route::get('/student/delete_data/{eid}/{sid}', [StudentController::class, 'delete'])->whereNumber(['eid','sid']);
Route::post('/student/bulk_delete', [StudentController::class, 'bulkDelete'])->name('student.bulk_delete');

// Disable authentication
Route::match(['get','post'],'/student/disable_authentication', [StudentController::class, 'disableAuthentication'])->name('student.disable_authentication');

// ID card
Route::match(['get','post'],'/student/generate_idcard', [StudentController::class, 'generateIdcard'])->name('student.idcard');
Route::match(['get','post'],'/student/idcard', [StudentController::class, 'generateIdcard']);

// Search
Route::post('/student/search', [StudentController::class, 'search'])->name('student.search');
Route::get('/student/search', [StudentController::class, 'search']);

// Password
Route::post('/student/change_password', [StudentController::class, 'changePassword'])->name('student.change_password');

// Documents
Route::post('/student/document_create', [StudentController::class, 'documentCreate'])->name('student.document_create');
Route::post('/student/document_update', [StudentController::class, 'documentUpdate'])->name('student.document_update');
Route::get('/student/document_delete/{id}', [StudentController::class, 'documentDelete'])->name('student.document_delete')->whereNumber('id');
Route::delete('/student/document_delete/{id}', [StudentController::class, 'documentDelete'])->whereNumber('id');
Route::post('/student/document_details', [StudentController::class, 'documentDetails'])->name('student.document_details');
Route::get('/student/documents_download', [StudentController::class, 'documentsDownload'])->name('student.documents_download');

// Transfer / Promotion
Route::match(['get','post'],'/student/transfer', [StudentController::class, 'transfer'])->name('student.transfer');
Route::post('/student/transfersave', [StudentController::class, 'transferSave'])->name('student.transfersave');

// Quick view
Route::post('/student/quickDetails', [StudentController::class, 'quickDetails'])->name('student.quickDetails');
