<?php

use Illuminate\Support\Facades\Route;
use AristechDev\NewsManager\Http\Controllers\React\NewsController;

Route::group([
    'prefix' => 'news',
    'middleware' => 'web',
], function () {
    // Route::get('/', 'ActualitesController@index')->name('news.index');
    Route::get('/', [NewsController::class, 'index'])->name('news.index');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('news.show');
}); 



Route::prefix('admin-panel')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('news', NewsController::class);
});