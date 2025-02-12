<?php

use Illuminate\Support\Facades\Route;
use AristechDev\NewsManager\Http\Controllers\React\ReportController;

Route::group([
    'prefix' => 'documents',
    'middleware' => 'web',
], function () {
    Route::get('/', [ReportController::class, 'publicDocuments'])->name('documents.index');
}); 



Route::prefix('admin-panel')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('documents', ReportController::class);
});