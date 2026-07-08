<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('entry')->group(function () {
    // Savings Entry Routes
    Route::prefix('savings/bulk_entry')->group(function () {
        Route::get('/', 'BulkSavingsEntryController@index')->name('bulk_entry.index');
        Route::get('/export', 'BulkSavingsEntryController@export')->name('bulk_entry.export');
        Route::get('create', 'BulkSavingsEntryController@create')->name('bulk_entry.create');
        Route::post('store', 'BulkSavingsEntryController@store')->name('bulk_entry.store');
        Route::get('{id}/show', 'BulkSavingsEntryController@show')->name('bulk_entry.show');
        Route::get('{id}/verify', 'BulkSavingsEntryController@verify')->name('bulk_entry.verify');
        Route::post('{id}/verify_entries', 'BulkSavingsEntryController@verify_entries')->name('bulk_entry.verify_entries');
        Route::post('{id}/reject_entries', 'BulkSavingsEntryController@reject_entries')->name('bulk_entry.reject_entries');
    });
});
