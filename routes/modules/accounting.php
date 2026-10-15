<?php

use App\Http\Controllers\AccountingController;
use Illuminate\Support\Facades\Route;

// Accounts (transactions)
Route::match(['get','post'], '/accounting', [AccountingController::class, 'index'])->name('accounting.index');
Route::match(['get','post'], '/accounting/edit/{id}', [AccountingController::class, 'edit'])->name('accounting.edit')->whereNumber('id');
Route::delete('/accounting/delete/{id}', [AccountingController::class, 'delete'])->name('accounting.delete')->whereNumber('id');
Route::post('/accounting/delete/{id}', [AccountingController::class, 'delete'])->whereNumber('id');

// Voucher head
Route::match(['get','post'], '/accounting/voucher_head', [AccountingController::class, 'voucherHead'])->name('accounting.voucher_head');
Route::post('/accounting/voucher_head_edit', [AccountingController::class, 'voucherHeadEdit'])->name('accounting.voucher_head_edit');
Route::post('/accounting/voucherHeadDetails', [AccountingController::class, 'voucherHeadDetails'])->name('accounting.voucherHeadDetails');
Route::delete('/accounting/voucher_head_delete/{id}', [AccountingController::class, 'voucherHeadDelete'])->name('accounting.voucher_head_delete')->whereNumber('id');
Route::post('/accounting/voucher_head_delete/{id}', [AccountingController::class, 'voucherHeadDelete'])->whereNumber('id');

// Vouchers
Route::get('/accounting/voucher_deposit', [AccountingController::class, 'voucherDeposit'])->name('accounting.deposit_voucher');
Route::get('/accounting/voucher_expense', [AccountingController::class, 'voucherExpense'])->name('accounting.expense_voucher');
Route::post('/accounting/voucher_save', [AccountingController::class, 'voucherSave'])->name('accounting.voucher_save');
Route::match(['get','post'], '/accounting/voucher_deposit_edit/{id}', [AccountingController::class, 'voucherDepositEdit'])->name('accounting.voucher_deposit_edit')->whereNumber('id');
Route::match(['get','post'], '/accounting/voucher_expense_edit/{id}', [AccountingController::class, 'voucherExpenseEdit'])->name('accounting.voucher_expense_edit')->whereNumber('id');
Route::delete('/accounting/voucher_delete/{id}', [AccountingController::class, 'voucherDelete'])->name('accounting.voucher_delete')->whereNumber('id');
Route::post('/accounting/voucher_delete/{id}', [AccountingController::class, 'voucherDelete'])->whereNumber('id');

// Reports
Route::match(['get','post'], '/accounting/statement', [AccountingController::class, 'accountStatement'])->name('accounting.statement');
Route::match(['get','post'], '/accounting/account_statement', [AccountingController::class, 'accountStatement'])->name('accounting.account_statement');
Route::match(['get','post'], '/accounting/income_report', [AccountingController::class, 'incomeReport'])->name('accounting.income_report');
Route::match(['get','post'], '/accounting/income_repots', [AccountingController::class, 'incomeReport']);
Route::match(['get','post'], '/accounting/expense_report', [AccountingController::class, 'expenseReport'])->name('accounting.expense_report');
Route::match(['get','post'], '/accounting/expense_repots', [AccountingController::class, 'expenseReport']);
Route::get('/accounting/balance_sheet', [AccountingController::class, 'balanceSheet'])->name('accounting.balance_sheet');
Route::post('/accounting/getVoucherHead', [AccountingController::class, 'getVoucherHead'])->name('accounting.getVoucherHead');
