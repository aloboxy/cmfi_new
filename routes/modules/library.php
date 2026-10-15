<?php

use App\Http\Controllers\LibraryController;
use Illuminate\Support\Facades\Route;

// Books
Route::match(['get','post'], '/library/book', [LibraryController::class, 'book'])->name('library.book');
Route::match(['get','post'], '/library/book_edit/{id}', [LibraryController::class, 'bookEdit'])->name('library.book_edit')->whereNumber('id');
Route::delete('/library/book_delete/{id}', [LibraryController::class, 'bookDelete'])->name('library.book_delete')->whereNumber('id');
Route::post('/library/book_delete/{id}', [LibraryController::class, 'bookDelete'])->whereNumber('id');

// Category
Route::match(['get','post'], '/library/category', [LibraryController::class, 'category'])->name('library.category');
Route::post('/library/category_edit', [LibraryController::class, 'categoryEdit'])->name('library.category_edit');
Route::delete('/library/category_delete/{id}', [LibraryController::class, 'categoryDelete'])->name('library.category_delete')->whereNumber('id');
Route::post('/library/category_delete/{id}', [LibraryController::class, 'categoryDelete'])->whereNumber('id');

// Issue / Return
Route::match(['get','post'], '/library/issue', [LibraryController::class, 'issue'])->name('library.issue');
Route::match(['get','post'], '/library/book_manage', [LibraryController::class, 'issue'])->name('library.book_manage');
Route::post('/library/bookIssued', [LibraryController::class, 'bookIssued'])->name('library.bookIssued');
Route::post('/library/bookReturn', [LibraryController::class, 'bookReturn'])->name('library.bookReturn');
Route::delete('/library/issued_book_delete/{id}', [LibraryController::class, 'issuedBookDelete'])->name('library.issued_book_delete')->whereNumber('id');
Route::post('/library/issued_book_delete/{id}', [LibraryController::class, 'issuedBookDelete'])->whereNumber('id');
Route::post('/library/getBooksByCategory', [LibraryController::class, 'getBooksByCategory'])->name('library.getBooksByCategory');
