<?php

use App\Http\Controllers\AdvanceSalaryController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

// Payroll
Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
Route::match(['get','post'], '/payroll/salary_template', [PayrollController::class, 'salaryTemplate'])->name('payroll.salary_template');
Route::match(['get','post'], '/payroll/salary_template_edit/{id}', [PayrollController::class, 'salaryTemplateEdit'])->name('payroll.salary_template_edit')->whereNumber('id');
Route::delete('/payroll/salary_template_delete/{id}', [PayrollController::class, 'salaryTemplateDelete'])->name('payroll.salary_template_delete')->whereNumber('id');
Route::post('/payroll/salary_template_delete/{id}', [PayrollController::class, 'salaryTemplateDelete'])->whereNumber('id');
Route::get('/payroll/salary_template_view/{id}', [PayrollController::class, 'salaryTemplateView'])->name('payroll.salary_template_view')->whereNumber('id');
Route::match(['get','post'], '/payroll/salary_assign', [PayrollController::class, 'salaryAssign'])->name('payroll.salary_assign');
Route::match(['get','post'], '/payroll/create/{id}/{month}/{year}', [PayrollController::class, 'create'])->name('payroll.create')->whereNumber(['id','month','year']);
Route::get('/payroll/invoice/{id}/{hash}', [PayrollController::class, 'invoice'])->name('payroll.invoice')->whereNumber('id');

// Advance Salary
Route::get('/advance_salary', [AdvanceSalaryController::class, 'index'])->name('advancesalary.index');
Route::post('/advance_salary', [AdvanceSalaryController::class, 'index']); // search / update uses same uri
Route::post('/advance_salary/save', [AdvanceSalaryController::class, 'save'])->name('advancesalary.save');
Route::delete('/advance_salary/delete/{id}', [AdvanceSalaryController::class, 'delete'])->name('advancesalary.delete')->whereNumber('id');
Route::post('/advance_salary/delete/{id}', [AdvanceSalaryController::class, 'delete'])->whereNumber('id');
Route::get('/advance_salary/request', [AdvanceSalaryController::class, 'request'])->name('advancesalary.request');
Route::post('/advance_salary/request', [AdvanceSalaryController::class, 'request']);
Route::post('/advance_salary/request_save', [AdvanceSalaryController::class, 'requestSave'])->name('advancesalary.request_save');
Route::delete('/advance_salary/request_delete/{id}', [AdvanceSalaryController::class, 'requestDelete'])->name('advancesalary.request_delete')->whereNumber('id');
Route::post('/advance_salary/request_delete/{id}', [AdvanceSalaryController::class, 'requestDelete'])->whereNumber('id');
Route::post('/advance_salary/getRequestDetails', [AdvanceSalaryController::class, 'getRequestDetails'])->name('advancesalary.getRequestDetails');

// Leave
Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index');
Route::post('/leave', [LeaveController::class, 'index']);
Route::post('/leave/save', [LeaveController::class, 'save'])->name('leave.save');
Route::delete('/leave/delete/{id}', [LeaveController::class, 'delete'])->name('leave.delete')->whereNumber('id');
Route::post('/leave/delete/{id}', [LeaveController::class, 'delete'])->whereNumber('id');
Route::delete('/leave/request_delete/{id}', [LeaveController::class, 'requestDelete'])->name('leave.request_delete')->whereNumber('id');
Route::post('/leave/request_delete/{id}', [LeaveController::class, 'requestDelete'])->whereNumber('id');
Route::match(['get','post'], '/leave/request', [LeaveController::class, 'request'])->name('leave.request');
Route::match(['get','post'], '/leave/category', [LeaveController::class, 'category'])->name('leave.category');
Route::post('/leave/category_edit', [LeaveController::class, 'categoryEdit'])->name('leave.category_edit');
Route::delete('/leave/category_delete/{id}', [LeaveController::class, 'categoryDelete'])->name('leave.category_delete')->whereNumber('id');
Route::post('/leave/category_delete/{id}', [LeaveController::class, 'categoryDelete'])->whereNumber('id');
Route::post('/leave/getCategory', [LeaveController::class, 'getCategory'])->name('leave.getCategory');
Route::post('/leave/getRequestDetails', [LeaveController::class, 'getRequestDetails'])->name('leave.getRequestDetails');
Route::post('/leave/getApprovelLeaveDetails', [LeaveController::class, 'getApprovelLeaveDetails'])->name('leave.getApprovelLeaveDetails');
Route::get('/leave/download/{id}', [LeaveController::class, 'download'])->name('leave.download')->whereNumber('id');

// Award
Route::match(['get','post'], '/award', [AwardController::class, 'index'])->name('award.index');
Route::match(['get','post'], '/award/edit/{id}', [AwardController::class, 'edit'])->name('award.edit')->whereNumber('id');
Route::delete('/award/delete/{id}', [AwardController::class, 'delete'])->name('award.delete')->whereNumber('id');
Route::post('/award/delete/{id}', [AwardController::class, 'delete'])->whereNumber('id');

// Legacy ajax alias for award student fetch (branch_id + class_id -> student options)
Route::post('/ajax/getStudentByClass', function (\Illuminate\Http\Request $request) {
    $branchId = $request->input('branch_id');
    $classId = $request->input('class_id');
    if (empty($classId)) return response('<option value="">'.translate('select_class_first').'</option>');
    $q = \Illuminate\Support\Facades\DB::table('enroll as e')
        ->join('student as s','s.id','=','e.student_id')
        ->where('e.class_id',$classId)
        ->where('e.session_id', get_session_id());
    if (!empty($branchId)) $q->where('e.branch_id',$branchId);
    elseif (!is_superadmin_loggedin()) $q->where('e.branch_id', get_loggedin_branch_id());
    $rows = $q->select('s.id','s.first_name','s.last_name','s.register_no')->get();
    if ($rows->isEmpty()) return response('<option value="">'.translate('no_information_available').'</option>');
    $html='<option value="">'.translate('select').'</option>';
    foreach($rows as $r) $html.='<option value="'.$r->id.'">'.e($r->first_name.' '.$r->last_name).' ('.e($r->register_no).')</option>';
    return response($html);
})->name('ajax.getStudentByClass');
