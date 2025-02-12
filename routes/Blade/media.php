<?php

use Illuminate\Support\Facades\Route;
use AristechDev\NewsManager\Http\Controllers\Blade\MediaController;

Route::group([
    'prefix' => 'medias',
    'middleware' => 'web',
], function () {
    // Route::get('/', 'ActualitesController@index')->name('news.index');
    Route::get('/', [MediaController::class, 'publicMedias'])->name('medias.index');
}); 



Route::prefix('admin-panel')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('medias', MediaController::class);
});