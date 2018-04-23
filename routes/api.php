<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function() {

	Route::resource('news', 'NewsController', [
	    'except' => ['create','edit']
	  ]);

	Route::resource('news/registration', 'RegisterController', [
	    'only' => ['store','destroy']
	  ]);

	Route::resource('topics', 'TopicController', [
	    'except' => ['create','edit']
	  ]);

});
