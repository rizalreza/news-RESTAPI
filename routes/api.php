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

	Route::get('publish/news','NewsController@getPublish')->name('news.publish');
	Route::get('draft/news','NewsController@getDraft')->name('news.draft');

	Route::resource('news/registration', 'RegisterController', [
	    'only' => ['store']
	  ]);

	Route::resource('topics', 'TopicController', [
	    'except' => ['create','edit']
	  ]);

});
