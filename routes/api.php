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
Route::namespace('API')->group(function(){
  Route::post('login', 'PassportController@login')->name('login');
  Route::post('register', 'PassportController@register')->name('register');
});


Route::middleware('auth:api')->namespace('API')->group(function () {
    //USER
    Route::get('user', 'PassportController@details');
    //NOTIFICATIONS
    //get all user notifications :
    Route::get('/user/notifications/{user}', 'UserController@getNotifications')->name('getNotifications');
    //POST
    //get all posts (heavy!)
    Route::get('/posts', 'PostController@index')->name('posts');
    //get all posts wich user is involved on. :
    Route::get('/posts/user', 'PostController@showUserPosts')->name('userPosts');
    //get all posts whose author is the user. :
    Route::get('/posts/author', 'PostController@showAuthorPosts')->name('authorPosts');
    //store new post :
    Route::post('/posts', 'PostController@store')->name('storePost');
    //get a specific post by its id (default version : last version):
    Route::get('/posts/{post}', 'PostController@show')->name('post');
    //get a specific version of a specific post :
    Route::get('/posts/{post}/{version}', 'PostController@showVersion')->name('postVersion');
    //modify post data (title, keywords) :
    Route::put('/posts/{post}', 'PostController@update')->name('updatePost');
    //delete a post
    Route::delete('/posts/{post}', 'PostController@destroy')->name('deletePost');
    //vote for a post (vote = true or false)
    Route::put('/votepost/{post}', 'PostController@votePost')->name('votePost');
    //post research routes :
    Route::get('/searchposts/{words}', 'PostController@searchPosts')->name('searchPosts');
    //post get user feed
    Route::get('/postsfeed/{user}', 'PostController@showUserFeed')->name('userFeed');

    //VERSIONS
    //vote for a version (vote = true or false)
    Route::put('/voteversion/{version}', 'PostController@voteVersion')->name('voteVersion');
    //store a new version of a post
    Route::post('/commitversion/{post}', 'PostController@commitVersion')->name('commitVersion');

    //COMMENTS
    //store a new comment on a version
    Route::post('/comments/{version}', 'CommentController@addComment')->name('addComment');
    //vote for a comment
    Route::put('/votecomment/{comment}', 'CommentController@voteComment')->name('voteComment');

    //GROUPS
    //get all groups
    Route::get('/groups', 'GroupController@index')->name('groups');
    //get all groups followed by the user
    Route::get('/groups/user/{user}', 'GroupController@showUserGroups')->name('userGroups');
    //store a new group
    Route::post('/groups', 'GroupController@store')->name('storeGroup');
    //get a specific group
    Route::get('/groups/{group}', 'GroupController@show')->name('group');
    //modify a group data (name, keywords)
    Route::put('/groups/{group}', 'GroupController@update')->name('updateGroup');
    //delete a group
    Route::delete('/groups/{group}', 'GroupController@destroy')->name('deleteGroup');
    //join a group (follow)
    Route::put('/groups/join/{group}', 'GroupController@joinGroup')->name('joinGroup');
    //leave a group
    Route::put('/groups/leave/{group}', 'GroupController@leaveGroup')->name('leaveGroup');
    //group research route :
    Route::get('/searchgroups/{words}', 'GroupController@searchGroups')->name('searchGroups');

    //Route::resource('profile', 'ProfileController')->except(['edit', 'create']);
});
