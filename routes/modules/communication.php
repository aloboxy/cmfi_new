<?php
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

// Messages / Mailbox
Route::get('/communication/mailbox/{action?}', [CommunicationController::class, 'mailbox'])->name('communication.messages')->where('action','inbox|sent|compose|important|trash|read');
Route::post('/communication/message_send', [CommunicationController::class, 'messageSend'])->name('communication.message_send');
Route::post('/communication/message_reply', [CommunicationController::class, 'messageReply'])->name('communication.message_reply');
Route::get('/communication/download', [CommunicationController::class, 'download'])->name('communication.download');
Route::post('/communication/delete_mail', [CommunicationController::class, 'deleteMail'])->name('communication.delete_mail');
Route::post('/communication/set_fvourite_status', [CommunicationController::class, 'setFavourite'])->name('communication.set_favourite');
Route::post('/communication/trash_observe', [CommunicationController::class, 'trashObserve'])->name('communication.trash_observe');
Route::post('/communication/getStafflistRole', [CommunicationController::class, 'getStafflistRole'])->name('communication.getStafflistRole');
Route::post('/communication/getStudentByClass', [CommunicationController::class, 'getStudentByClass'])->name('communication.getStudentByClass');
Route::post('/communication/getParentListBranch', [CommunicationController::class, 'getParentListBranch'])->name('communication.getParentListBranch');

// Logs / Notice placeholders
Route::get('/communication/log', function(){ return view('placeholder', ['title'=>translate('email_sms_log')]); })->name('communication.log');
Route::get('/communication/notice', function(){ return view('placeholder', ['title'=>translate('notice_board')]); })->name('communication.notice');

// Events
Route::match(['get','post'],'/event', [EventController::class, 'index'])->name('events.index');
Route::delete('/event/{id}', [EventController::class, 'destroy'])->name('events.destroy')->whereNumber('id');
Route::post('/event/delete/{id}', [EventController::class, 'destroy'])->whereNumber('id');
Route::get('/event/types', [EventController::class, 'types'])->name('events.type');
Route::post('/event/types', [EventController::class, 'typeStore'])->name('events.type_store');
Route::post('/event/types_edit', [EventController::class, 'typeEdit'])->name('events.type_edit');
Route::delete('/event/type/{id}', [EventController::class, 'typeDelete'])->name('events.type_delete')->whereNumber('id');
Route::post('/event/type_delete/{id}', [EventController::class, 'typeDelete'])->whereNumber('id');
Route::post('/event/status', [EventController::class, 'status'])->name('events.status');
Route::post('/event/getDetails', [EventController::class, 'getDetails'])->name('events.getDetails');
Route::post('/event/getSectionByBranch', [EventController::class, 'getSectionByBranch'])->name('events.getSectionByBranch');
Route::get('/event/get_events_list/{branchID?}', [EventController::class, 'getEventsList'])->name('events.get_events_list');
