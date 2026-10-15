<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

// View - list (with optional role)
Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
Route::get('/employee/view/{role?}', [EmployeeController::class, 'index'])->whereNumber('role');
Route::get('/employee/view', [EmployeeController::class, 'index']);

// Add
Route::match(['get','post'],'/employee/add', [EmployeeController::class, 'create'])->name('employee.create');

// Profile
Route::match(['get','post'],'/employee/profile/{id}', [EmployeeController::class, 'profile'])->name('employee.profile')->whereNumber('id');

Route::delete('/employee/delete/{id}', [EmployeeController::class, 'destroy'])->name('employee.delete')->whereNumber('id');
Route::post('/employee/delete/{id}', [EmployeeController::class, 'destroy'])->whereNumber('id');
Route::get('/employee/delete/{id}', [EmployeeController::class, 'destroy'])->whereNumber('id');

// Password
Route::post('/employee/change_password', [EmployeeController::class, 'changePassword'])->name('employee.change_password');

// Bank
Route::post('/employee/bank_account_create', [EmployeeController::class, 'bankCreate'])->name('employee.bank_create');
Route::post('/employee/bank_account_update', [EmployeeController::class, 'bankUpdate'])->name('employee.bank_update');
Route::get('/employee/bankaccount_delete/{id}', [EmployeeController::class, 'bankDelete'])->name('employee.bank_delete')->whereNumber('id');
Route::delete('/employee/bankaccount_delete/{id}', [EmployeeController::class, 'bankDelete'])->whereNumber('id');
Route::post('/employee/bank_details', [EmployeeController::class, 'bankDetails'])->name('employee.bank_details');

// Documents
Route::post('/employee/document_create', [EmployeeController::class, 'documentCreate'])->name('employee.document_create');
Route::post('/employee/document_update', [EmployeeController::class, 'documentUpdate'])->name('employee.document_update');
Route::get('/employee/document_delete/{id}', [EmployeeController::class, 'documentDelete'])->name('employee.document_delete')->whereNumber('id');
Route::delete('/employee/document_delete/{id}', [EmployeeController::class, 'documentDelete'])->whereNumber('id');
Route::post('/employee/document_details', [EmployeeController::class, 'documentDetails'])->name('employee.document_details');
Route::get('/employee/documents_download', [EmployeeController::class, 'documentsDownload'])->name('employee.documents_download');

// Department
Route::match(['get','post'],'/employee/department', [EmployeeController::class, 'department'])->name('employee.department');
Route::post('/employee/department_edit', [EmployeeController::class, 'departmentEdit'])->name('employee.department_edit');
Route::delete('/employee/department_delete/{id}', [EmployeeController::class, 'departmentDelete'])->name('employee.department_delete')->whereNumber('id');
Route::post('/employee/department_delete/{id}', [EmployeeController::class, 'departmentDelete'])->whereNumber('id');
Route::post('/employee/departmentDetails', [EmployeeController::class, 'departmentDetails'])->name('employee.departmentDetails');

// Designation
Route::match(['get','post'],'/employee/designation', [EmployeeController::class, 'designation'])->name('employee.designation');
Route::post('/employee/designation_edit', [EmployeeController::class, 'designationEdit'])->name('employee.designation_edit');
Route::delete('/employee/designation_delete/{id}', [EmployeeController::class, 'designationDelete'])->name('employee.designation_delete')->whereNumber('id');
Route::post('/employee/designation_delete/{id}', [EmployeeController::class, 'designationDelete'])->whereNumber('id');
Route::post('/employee/designationDetails', [EmployeeController::class, 'designationDetails'])->name('employee.designationDetails');

// Disable authentication
Route::match(['get','post'],'/employee/disable_authentication', [EmployeeController::class, 'disableAuthentication'])->name('employee.disable_authentication');

// CSV import
Route::post('/employee/csv_import', [EmployeeController::class, 'csvImport'])->name('employee.csv_import');
Route::get('/employee/csv_Sampledownloader', [EmployeeController::class, 'csvSampleDownload'])->name('employee.csv_sample');
Route::get('/employee/csv_sample', [EmployeeController::class, 'csvSampleDownload']);
