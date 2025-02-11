<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'documents',
    'middleware' => 'web',
    'namespace' => 'AristechDev\NewsManager\Http\Controllers'
], function () {
    Route::get('/', 'DocumentController@index')->name('documents.index');
}); 