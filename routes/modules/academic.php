<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

// ------------------------------------------------------------
// Classes
// ------------------------------------------------------------
Route::get('/classes', [ClassesController::class, 'index'])->name('classes.index');
Route::post('/classes', [ClassesController::class, 'store'])->name('classes.store');
Route::get('/classes/{id}/edit', [ClassesController::class, 'edit'])->name('classes.edit')->whereNumber('id');
Route::put('/classes/{id}', [ClassesController::class, 'update'])->name('classes.update')->whereNumber('id');
Route::post('/classes/{id}', [ClassesController::class, 'update'])->whereNumber('id');
Route::delete('/classes/{id}', [ClassesController::class, 'destroy'])->name('classes.destroy')->whereNumber('id');

// Teacher allocation (legacy: classes/teacher_allocation)
Route::get('/classes/teacher_allocation', [ClassesController::class, 'teacherAllocation'])->name('classes.teacher_allocation');
Route::post('/classes/teacher_allocation', [ClassesController::class, 'teacherAllocationSave'])->name('classes.teacher_allocation_save');
Route::post('/classes/getAllocationTeacher', [ClassesController::class, 'getAllocationTeacher'])->name('classes.getAllocationTeacher');
Route::delete('/classes/teacher_allocation/{id}', [ClassesController::class, 'teacherAllocationDelete'])->name('classes.teacher_allocation_delete')->whereNumber('id');
Route::post('/classes/teacher_allocation_save', [ClassesController::class, 'teacherAllocationSave']);
Route::post('/classes/teacher_allocation_delete/{id}', [ClassesController::class, 'teacherAllocationDelete'])->whereNumber('id');

// ------------------------------------------------------------
// Sections
// ------------------------------------------------------------
Route::get('/sections', [SectionsController::class, 'index'])->name('sections.index');
Route::get('/sections/{id}/edit', [SectionsController::class, 'edit'])->name('sections.edit')->whereNumber('id');
Route::post('/sections/save', [SectionsController::class, 'save'])->name('sections.save');
Route::post('/sections', [SectionsController::class, 'save'])->name('sections.store');
Route::delete('/sections/{id}', [SectionsController::class, 'destroy'])->name('sections.destroy')->whereNumber('id');
Route::post('/sections/delete/{id}', [SectionsController::class, 'destroy'])->whereNumber('id');

// ------------------------------------------------------------
// Subject
// ------------------------------------------------------------
Route::get('/subject', [SubjectController::class, 'index'])->name('subject.index');
Route::post('/subject/save', [SubjectController::class, 'save'])->name('subject.save');
Route::post('/subject', [SubjectController::class, 'save'])->name('subject.store');
Route::get('/subject/edit/{id}', [SubjectController::class, 'edit'])->name('subject.edit')->whereNumber('id');
Route::get('/subject/{id}/edit', [SubjectController::class, 'edit'])->whereNumber('id');
Route::delete('/subject/{id}', [SubjectController::class, 'destroy'])->name('subject.destroy')->whereNumber('id');
Route::post('/subject/delete/{id}', [SubjectController::class, 'destroy'])->whereNumber('id');

Route::get('/subject/class_assign', [SubjectController::class, 'classAssign'])->name('subject.class_assign');
Route::post('/subject/class_assign_save', [SubjectController::class, 'classAssignSave'])->name('subject.class_assign_save');
Route::post('/subject/class_assign_edit', [SubjectController::class, 'classAssignEdit'])->name('subject.class_assign_edit');
Route::delete('/subject/class_assign/{classId}/{sectionId}', [SubjectController::class, 'classAssignDelete'])->name('subject.class_assign_delete')->whereNumber(['classId', 'sectionId']);
Route::post('/subject/class_assign_delete/{classId}/{sectionId}', [SubjectController::class, 'classAssignDelete'])->whereNumber(['classId', 'sectionId']);

Route::match(['get', 'post'], '/subject/teacher_assign', [SubjectController::class, 'teacherAssign'])->name('subject.teacher_assign');
Route::delete('/subject/teacher_assign/{id}', [SubjectController::class, 'teacherAssignDelete'])->name('subject.teacher_assign_delete')->whereNumber('id');
Route::post('/subject/teacher_assign_delete/{id}', [SubjectController::class, 'teacherAssignDelete'])->whereNumber('id');
Route::post('/subject/getByClassSection', [SubjectController::class, 'getByClassSection'])->name('subject.getByClassSection');
Route::post('/subject/getClassAssignModal', [SubjectController::class, 'getClassAssignModal'])->name('subject.getClassAssignModal');

// ------------------------------------------------------------
// Timetable — class schedule
// ------------------------------------------------------------
Route::match(['get', 'post'], '/timetable/viewclass', [TimetableController::class, 'viewClass'])->name('timetable.viewclass');
Route::match(['get', 'post'], '/timetable/set_classwise', [TimetableController::class, 'setClasswise'])->name('timetable.set_classwise');
Route::match(['get', 'post'], '/timetable/update_classwise', [TimetableController::class, 'updateClasswise'])->name('timetable.update_classwise');
Route::post('/timetable/class_save/{mode}', [TimetableController::class, 'classSave'])->name('timetable.class_save')->whereIn('mode', ['new', 'update']);

// Exam schedule
Route::match(['get', 'post'], '/timetable/viewexam', [TimetableController::class, 'viewExam'])->name('timetable.viewexam');
Route::match(['get', 'post'], '/timetable/set_examwise', [TimetableController::class, 'setExamwise'])->name('timetable.set_examwise');
Route::post('/timetable/exam_create', [TimetableController::class, 'examCreate'])->name('timetable.exam_create');
Route::delete('/timetable/exam_delete/{examId}/{classId}/{sectionId}', [TimetableController::class, 'examDelete'])->name('timetable.exam_delete')->whereNumber(['examId', 'classId', 'sectionId']);
Route::post('/timetable/exam_delete/{examId}/{classId}/{sectionId}', [TimetableController::class, 'examDelete'])->whereNumber(['examId', 'classId', 'sectionId']);
Route::post('/timetable/getExamTimetableM', [TimetableController::class, 'getExamTimetableM'])->name('timetable.getExamTimetableM');

// Legacy aliases so old JS URLs still work (redirects via same controller)
Route::post('/timetable/class_save/new', [TimetableController::class, 'classSave'])->defaults('mode', 'new');
Route::post('/timetable/class_save/update', [TimetableController::class, 'classSave'])->defaults('mode', 'update');

// ------------------------------------------------------------
// Ajax helpers for dependent dropdowns
// ------------------------------------------------------------
Route::post('/ajax/getClassByBranch', [AjaxController::class, 'getClassByBranch'])->name('ajax.getClassByBranch');
Route::post('/ajax/getSectionByClass', [AjaxController::class, 'getSectionByClass'])->name('ajax.getSectionByClass');
Route::post('/ajax/getSectionByBranch', [AjaxController::class, 'getSectionByBranch'])->name('ajax.getSectionByBranch');
Route::post('/ajax/getDataByBranch', [AjaxController::class, 'getDataByBranch'])->name('ajax.getDataByBranch');
Route::post('/ajax/getExamByBranch', [AjaxController::class, 'getExamByBranch'])->name('ajax.getExamByBranch');
Route::post('/ajax/getStaffListRole', [AjaxController::class, 'getStaffListRole'])->name('ajax.getStaffListRole');
Route::post('/ajax/getSubjectByClass', [AjaxController::class, 'getSubjectByClass'])->name('ajax.getSubjectByClass');
Route::post('/ajax/getClassAssignM', [AjaxController::class, 'getClassAssignModal'])->name('ajax.getClassAssignM');
Route::post('/ajax/getStafflistRole', [AjaxController::class, 'getStaffListRole']);
