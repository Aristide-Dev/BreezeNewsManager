<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'news',
    'middleware' => 'web',
    'namespace' => 'AristechDev\NewsManager\Http\Controllers'
], function () {
    Route::get('/', 'NewsController@index')->name('news.index');
}); 