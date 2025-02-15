<?php

use Illuminate\Support\Facades\Route;
use AristechDev\NewsManager\Http\Controllers\React\DocumentController;

Route::group([
    'prefix' => 'documents',
    'middleware' => 'web',
], function () {
    Route::get('/', [DocumentController::class, 'publicDocuments'])->name('documents.index');
}); 



Route::prefix('admin-panel')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('documents', DocumentController::class);
});