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

  Route::get('/user/notifications/{user}', 'UserController@getNotifications')->name('getNotifications');
  //Route::resource('profile', 'ProfileController')->except(['edit', 'create']);
  Route::get('/posts', 'PostController@index')->name('posts');
  Route::get('/posts/user/{user}', 'PostController@showUserPosts')->name('userPosts');
  Route::get('/posts/author/{user}', 'PostController@showAuthorPosts')->name('authorPosts');
  Route::post('/posts', 'PostController@store')->name('storePost');
  Route::get('/posts/{post}', 'PostController@show')->name('post');
  Route::get('/posts/{post}/{version}', 'PostController@showVersion')->name('postVersion');
  Route::put('/posts/{post}', 'PostController@update')->name('updatePost');
  Route::delete('/posts/{post}', 'PostController@destroy')->name('deletePost');
  Route::put('/votepost/{post}', 'PostController@votePost')->name('votePost');
  //post research routes :
  Route::get('/searchposts/{words}', 'PostController@searchPosts')->name('searchPosts');
  //post get user feed
  Route::get('/postsfeed/{user}', 'PostController@showUserFeed')->name('userFeed');

  Route::put('/voteversion/{version}', 'PostController@voteVersion')->name('voteVersion');
  Route::post('/commitversion/{post}', 'PostController@commitVersion')->name('commitVersion');

  Route::post('/comments/{version}', 'CommentController@addComment')->name('addComment');
  Route::put('/votecomment/{comment}', 'CommentController@voteComment')->name('voteComment');

  Route::get('/groups', 'GroupController@index')->name('groups');
  Route::get('/groups/user/{user}', 'GroupController@showUserGroups')->name('userGroups');
  Route::post('/groups', 'GroupController@store')->name('storeGroup');
  Route::get('/groups/{group}', 'GroupController@show')->name('group');
  Route::put('/groups/{group}', 'GroupController@update')->name('updateGroup');
  Route::delete('/groups/{group}', 'GroupController@destroy')->name('deleteGroup');
  Route::put('/groups/join/{group}/{user}', 'GroupController@joinGroup')->name('joinGroup');
  Route::put('/groups/leave/{group}/{user}', 'GroupController@leaveGroup')->name('leaveGroup');
  //group research route :
  Route::get('/searchgroups/{words}', 'GroupController@searchGroups')->name('searchGroups');
});
