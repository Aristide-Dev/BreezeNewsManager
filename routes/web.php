<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'newsmanager',
    'middleware' => ['web'],
    'namespace'  => 'AristechDev\NewsManager\Http\Controllers'
], function () {
    Route::get('/', 'NewsManagerController@index')->name('newsmanager.index');
}); 