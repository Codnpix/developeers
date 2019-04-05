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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('API')->group(function() {

  //Route::resource('groups', 'GroupController')->except(['edit', 'create']);
  //Route::resource('profile', 'ProfileController')->except(['edit', 'create']);
  Route::get('/posts', 'PostController@index')->name('posts');
  Route::post('/posts', 'PostController@store')->name('storePost');
  Route::get('/posts/{post}', 'PostController@show')->name('post');
  Route::put('/posts/{post}', 'PostController@update')->name('updatePost');
  Route::delete('/posts/{post}', 'PostController@destroy')->name('deletePost');
  Route::put('/votepost/{post}', 'PostController@votePost')->name('votePost');
  Route::put('/voteversion/{version}', 'PostController@voteVersion')->name('voteVersion');
  Route::post('/commitversion/{post}', 'PostController@commitVersion')->name('commitVersion');
  Route::get('/test', 'PostController@test')->name('test');
});
