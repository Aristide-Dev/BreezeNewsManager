<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'media',
    'middleware' => 'web',
    'namespace' => 'AristechDev\NewsManager\Http\Controllers'
], function () {
    Route::get('/', 'MediaController@index')->name('media.index');
}); 