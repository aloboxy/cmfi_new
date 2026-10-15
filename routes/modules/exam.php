<?php
use App\Http\Controllers\ExamController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

// Exam setup
Route::get('/exam', [ExamController::class, 'index'])->name('exam.index');
Route::post('/exam', [ExamController::class, 'store'])->name('exam.store');
Route::get('/exam/edit/{id}', [ExamController::class, 'edit'])->name('exam.edit')->whereNumber('id');
Route::put('/exam/{id}', [ExamController::class, 'update'])->name('exam.update')->whereNumber('id');
Route::post('/exam/{id}', [ExamController::class, 'update'])->whereNumber('id');
Route::delete('/exam/{id}', [ExamController::class, 'destroy'])->name('exam.destroy')->whereNumber('id');
Route::post('/exam/delete/{id}', [ExamController::class, 'destroy'])->whereNumber('id');

// Term
Route::get('/exam/term', [ExamController::class, 'term'])->name('exam.term');
Route::post('/exam/term', [ExamController::class, 'termStore'])->name('exam.term_store');
Route::post('/exam/term_edit', [ExamController::class, 'termEdit'])->name('exam.term_edit');
Route::delete('/exam/term/{id}', [ExamController::class, 'termDelete'])->name('exam.term_delete')->whereNumber('id');
Route::post('/exam/term_delete/{id}', [ExamController::class, 'termDelete'])->whereNumber('id');

// Mark distribution
Route::get('/exam/mark_distribution', [ExamController::class, 'markDistribution'])->name('exam.mark_distribution');
Route::post('/exam/mark_distribution', [ExamController::class, 'markDistributionStore'])->name('exam.mark_distribution_store');
Route::post('/exam/mark_distribution_edit', [ExamController::class, 'markDistributionEdit'])->name('exam.mark_distribution_edit');
Route::delete('/exam/mark_distribution/{id}', [ExamController::class, 'markDistributionDelete'])->name('exam.mark_distribution_delete')->whereNumber('id');
Route::post('/exam/mark_distribution_delete/{id}', [ExamController::class, 'markDistributionDelete'])->whereNumber('id');

// Hall
Route::get('/exam/hall', [ExamController::class, 'hall'])->name('exam.hall');
Route::post('/exam/hall', [ExamController::class, 'hallStore'])->name('exam.hall_store');
Route::post('/exam/hall_edit', [ExamController::class, 'hallEdit'])->name('exam.hall_edit');
Route::delete('/exam/hall/{id}', [ExamController::class, 'hallDelete'])->name('exam.hall_delete')->whereNumber('id');
Route::post('/exam/hall_delete/{id}', [ExamController::class, 'hallDelete'])->whereNumber('id');

// Grade
Route::get('/exam/grade', [ExamController::class, 'grade'])->name('exam.grade');
Route::post('/exam/grade', [ExamController::class, 'gradeStore'])->name('exam.grade_store');
Route::get('/exam/grade_edit/{id}', [ExamController::class, 'gradeEdit'])->name('exam.grade_edit')->whereNumber('id');
Route::put('/exam/grade/{id}', [ExamController::class, 'gradeUpdate'])->name('exam.grade_update')->whereNumber('id');
Route::post('/exam/grade/{id}', [ExamController::class, 'gradeUpdate'])->whereNumber('id');
Route::delete('/exam/grade/{id}', [ExamController::class, 'gradeDelete'])->name('exam.grade_delete')->whereNumber('id');
Route::post('/exam/grade_delete/{id}', [ExamController::class, 'gradeDelete'])->whereNumber('id');

// Marks
Route::match(['get','post'],'/exam/marks', [ExamController::class, 'marks'])->name('exam.marks');
Route::post('/exam/mark_save', [ExamController::class, 'markSave'])->name('exam.mark_save');
Route::post('/exam/getDistributionByBranch', [ExamController::class, 'getDistributionByBranch'])->name('exam.getDistributionByBranch');

// Schedule alias to timetable viewexam (exam schedule)
Route::match(['get','post'],'/exam/schedule', [TimetableController::class, 'viewExam'])->name('exam.schedule');

// Master GradeSheet / Tabulation Register
Route::match(['get','post'], '/exam/tabulation', [ExamController::class, 'tabulationSheet'])->name('exam.tabulation');
Route::get('/exam/tabulation/export', [ExamController::class, 'tabulationExport'])->name('exam.tabulation_export');

// Report Card — bulk and individual generation based on template design
Route::get('/exam/report-card', [ExamController::class, 'reportCard'])->name('exam.report_card');
Route::get('/exam/marksheet', [ExamController::class, 'reportCard'])->name('exam.marksheet');
Route::get('/exam/report-card/students', [ExamController::class, 'reportCardStudents'])->name('exam.report_card.students');
Route::post('/exam/report-card/print', [ExamController::class, 'reportCardPrint'])->name('exam.report_card.print');
Route::post('/exam/reportCardPrint', [ExamController::class, 'reportCardPrint'])->name('exam.reportCardPrint');

