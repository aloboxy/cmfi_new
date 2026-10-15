<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportCardTemplateController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolSettingsController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TranslationsController;
use Illuminate\Support\Facades\Route;

// Global settings (Settings::universal)
Route::match(['get','post'], '/settings/universal', [SettingsController::class, 'universal'])->name('settings.universal');

// School settings — navigation expects settings.school
Route::get('/settings/school', [SchoolSettingsController::class, 'school'])->name('settings.school');
Route::post('/settings/school', [SchoolSettingsController::class, 'schoolUpdate'])->name('settings.school.update');
Route::post('/school_settings/school_update', [SchoolSettingsController::class, 'schoolUpdate']);

// Payment gateway
Route::get('/settings/payment_gateway', [SchoolSettingsController::class, 'payment'])->name('settings.payment_gateway');
Route::get('/payment_gateway', [SchoolSettingsController::class, 'payment']);
Route::post('/school_settings/paypal_save', [SchoolSettingsController::class, 'paypalSave'])->name('settings.paypal_save');
Route::post('/school_settings/stripe_save', [SchoolSettingsController::class, 'stripeSave'])->name('settings.stripe_save');
Route::post('/school_settings/payumoney_save', [SchoolSettingsController::class, 'payumoneySave'])->name('settings.payumoney_save');
Route::post('/school_settings/paystack_save', [SchoolSettingsController::class, 'paystackSave'])->name('settings.paystack_save');
Route::post('/school_settings/razorpay_save', [SchoolSettingsController::class, 'razorpaySave'])->name('settings.razorpay_save');
Route::post('/school_settings/payment_active', [SchoolSettingsController::class, 'paymentActive'])->name('settings.payment_active');
Route::post('/settings/payment_active', [SchoolSettingsController::class, 'paymentActive']);

// SMS config
Route::get('/settings/smsconfig', [SchoolSettingsController::class, 'smsconfig'])->name('settings.smsconfig');
Route::get('/smsconfig', [SchoolSettingsController::class, 'smsconfig']);
Route::post('/school_settings/sms_active', [SchoolSettingsController::class, 'smsActive'])->name('settings.sms_active');
Route::post('/school_settings/twilio', [SchoolSettingsController::class, 'twilio'])->name('settings.twilio');
Route::post('/school_settings/clickatell', [SchoolSettingsController::class, 'clickatell'])->name('settings.clickatell');
Route::post('/school_settings/msg91', [SchoolSettingsController::class, 'msg91'])->name('settings.msg91');
Route::post('/school_settings/bulksms', [SchoolSettingsController::class, 'bulksms'])->name('settings.bulksms');
Route::post('/school_settings/textlocal', [SchoolSettingsController::class, 'textlocal'])->name('settings.textlocal');
Route::get('/school_settings/smstemplate', [SchoolSettingsController::class, 'smstemplate'])->name('settings.smstemplate');
Route::post('/school_settings/smsTemplateSave', [SchoolSettingsController::class, 'smsTemplateSave'])->name('settings.sms_template_save');

// Email config
Route::get('/settings/emailconfig', [SchoolSettingsController::class, 'emailconfig'])->name('settings.emailconfig');
Route::get('/mailconfig/email', [SchoolSettingsController::class, 'emailconfig']);
Route::post('/school_settings/saveEmailConfig', [SchoolSettingsController::class, 'saveEmailConfig'])->name('settings.email_config_save');
Route::get('/school_settings/emailtemplate', [SchoolSettingsController::class, 'emailtemplate'])->name('settings.emailtemplate');
Route::post('/school_settings/emailTemplateSave', [SchoolSettingsController::class, 'emailTemplateSave'])->name('settings.email_template_save');

// Accounting links
Route::get('/school_settings/accounting_links', [SchoolSettingsController::class, 'accountingLinks'])->name('settings.accounting_links');
Route::post('/school_settings/accountingLinksSave', [SchoolSettingsController::class, 'accountingLinksSave'])->name('settings.accounting_links_save');

// Live class config
Route::get('/school_settings/live_class_config', [SchoolSettingsController::class, 'liveClassConfig'])->name('settings.live_class_config');
Route::post('/school_settings/liveClassSave', [SchoolSettingsController::class, 'liveClassSave'])->name('settings.live_class_save');

// Report card template builder
Route::get('/settings/report-card-template', [ReportCardTemplateController::class, 'index'])->name('settings.report_card_template');
Route::post('/settings/report-card-template', [ReportCardTemplateController::class, 'store'])->name('settings.report_card_template.store');
Route::post('/settings/report-card-template/grapejs', [ReportCardTemplateController::class, 'saveGrapejs'])->name('settings.report_card_template.grapejs');
Route::post('/settings/report-card-template/canvas', [ReportCardTemplateController::class, 'saveCanvas'])->name('settings.report_card_template.canvas');
Route::get('/settings/report-card-template/preview/{branchId?}', [ReportCardTemplateController::class, 'preview'])->name('settings.report_card_template.preview');
Route::get('/school_settings/report_card_template', [ReportCardTemplateController::class, 'index']);

// Sessions (schoolyear) — navigation expects sessions.index
Route::get('/sessions', [SessionsController::class, 'index'])->name('sessions.index');
Route::post('/sessions', [SessionsController::class, 'store'])->name('sessions.store');
Route::post('/sessions/edit', [SessionsController::class, 'update'])->name('sessions.update');
Route::delete('/sessions/{id}', [SessionsController::class, 'destroy'])->name('sessions.destroy')->whereNumber('id');
Route::post('/sessions/delete/{id}', [SessionsController::class, 'destroy'])->whereNumber('id');

// Role & permissions — navigation expects role.index
Route::get('/role', [RoleController::class, 'index'])->name('role.index');
Route::post('/role', [RoleController::class, 'index'])->name('role.store');
Route::match(['get','post'], '/role/edit/{id}', [RoleController::class, 'edit'])->name('role.edit')->whereNumber('id');
Route::delete('/role/{id}', [RoleController::class, 'destroy'])->name('role.destroy')->whereNumber('id');
Route::post('/role/delete/{id}', [RoleController::class, 'destroy'])->whereNumber('id');
Route::match(['get','post'], '/role/permission/{id}', [RoleController::class, 'permission'])->name('role.permission')->whereNumber('id');

// Custom field — navigation expects customfield.index
Route::get('/custom_field', [CustomFieldController::class, 'index'])->name('customfield.index');
Route::get('/custom_field/edit/{id}', [CustomFieldController::class, 'edit'])->name('customfield.edit')->whereNumber('id');
Route::post('/custom_field/save', [CustomFieldController::class, 'save'])->name('customfield.save');
Route::delete('/custom_field/{id}', [CustomFieldController::class, 'delete'])->name('customfield.destroy')->whereNumber('id');
Route::post('/custom_field/delete/{id}', [CustomFieldController::class, 'delete'])->whereNumber('id');
Route::post('/custom_field/status', [CustomFieldController::class, 'status'])->name('customfield.status');
Route::post('/custom_field/getFieldsByBranch', [CustomFieldController::class, 'status']);

// Backup — navigation expects backup.index
Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
Route::get('/backup/create', [BackupController::class, 'create'])->name('backup.create');
Route::get('/backup/download', [BackupController::class, 'download'])->name('backup.download');
Route::delete('/backup/{file}', [BackupController::class, 'deleteFile'])->name('backup.delete');
Route::get('/backup/delete_file/{file}', [BackupController::class, 'deleteFile']);

// Translations — navigation expects translations.index
Route::get('/translations', [TranslationsController::class, 'index'])->name('translations.index');
Route::match(['get','post'], '/translations/update', [TranslationsController::class, 'update'])->name('translations.update');
Route::post('/translations/create', [TranslationsController::class, 'store'])->name('translations.store');
Route::post('/translations/rename/{id}', [TranslationsController::class, 'rename'])->name('translations.rename')->whereNumber('id');
Route::delete('/translations/{id}', [TranslationsController::class, 'destroy'])->name('translations.destroy')->whereNumber('id');
Route::post('/translations/delete/{id}', [TranslationsController::class, 'destroy'])->whereNumber('id');
Route::post('/translations/status', [TranslationsController::class, 'status'])->name('translations.status');
Route::post('/translations/get_details', [TranslationsController::class, 'getDetails'])->name('translations.get_details');
Route::get('/translations/set_language/{lang}', function($lang){ session(['set_lang'=>$lang]); return back(); })->name('translations.set_language');

// Profile — own profile + password
Route::match(['get','post'], '/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::match(['get','post'], '/profile/password', [ProfileController::class, 'password'])->name('profile.password');

// Module control — super admin only
Route::get('/settings/modules', [\App\Http\Controllers\ModuleControlController::class, 'index'])->name('settings.modules');
Route::post('/settings/modules/toggle', [\App\Http\Controllers\ModuleControlController::class, 'toggle'])->name('settings.modules.toggle');
Route::post('/settings/modules/bulk', [\App\Http\Controllers\ModuleControlController::class, 'bulkToggle'])->name('settings.modules.bulk');
