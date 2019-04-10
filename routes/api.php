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
    Route::get('user', 'PassportController@details')->name('user.details');
    Route::put('user/update', 'PassportController@updateUser')->name('user.update');
    //NOTIFICATIONS
    //get all user notifications :
    Route::get('/user/notifications', 'UserController@getNotifications')->name('user.notifications');
    //POST
    //get all posts (heavy!)
    Route::get('/posts', 'PostController@index')->name('posts');
    //get all posts wich user is involved on. :
    Route::get('/posts/user', 'PostController@showUserPosts')->name('posts.user');
    //get all posts whose author is the user. :
    Route::get('/posts/author', 'PostController@showAuthorPosts')->name('posts.author');
    //get all posts in a specific group:
    Route::get('/posts/group/{group}', 'PostController@showGroupPosts')->name('posts.group');
    //store new post :
    Route::post('/posts', 'PostController@store')->name('posts.store');
    //get a specific post by its id (default version : last version):
    Route::get('/posts/{post}', 'PostController@show')->name('posts.post');
    //get a specific version of a specific post :
    Route::get('/posts/{post}/{version}', 'PostController@showVersion')->name('posts.version');
    //modify post data (title, keywords) :
    Route::put('/posts/{post}', 'PostController@update')->name('posts.update');
    //delete a post
    Route::delete('/posts/{post}', 'PostController@destroy')->name('posts.delete');
    //vote for a post (vote = true or false)
    Route::put('/votepost/{post}', 'PostController@votePost')->name('posts.vote');
    //post research routes :
    Route::get('/searchposts/{words}', 'PostController@searchPosts')->name('posts.search');
    //post get user feed
    Route::get('/postsfeed', 'PostController@showUserFeed')->name('posts.feed');

    //VERSIONS
    //vote for a version (vote = true or false)
    Route::put('/voteversion/{version}', 'PostController@voteVersion')->name('versions.vote');
    //store a new version of a post
    Route::post('/commitversion/{post}', 'PostController@commitVersion')->name('versions.commit');

    //COMMENTS
    //store a new comment on a version
    Route::post('/comments/{version}', 'CommentController@addComment')->name('comments.add');
    //vote for a comment
    Route::put('/votecomment/{comment}', 'CommentController@voteComment')->name('comments.vote');

    //GROUPS
    //get all groups
    Route::get('/groups', 'GroupController@index')->name('groups');
    //get all groups followed by the user
    Route::get('/groups/user', 'GroupController@showUserGroups')->name('groups.user');
    //store a new group
    Route::post('/groups', 'GroupController@store')->name('groups.store');
    //get a specific group
    Route::get('/groups/{group}', 'GroupController@show')->name('groups.group');
    //modify a group data (name, keywords)
    Route::put('/groups/{group}', 'GroupController@update')->name('groups.update');
    //delete a group
    Route::delete('/groups/{group}', 'GroupController@destroy')->name('groups.delete');
    //join a group (follow)
    Route::put('/groups/join/{group}', 'GroupController@joinGroup')->name('groups.join');
    //leave a group
    Route::put('/groups/leave/{group}', 'GroupController@leaveGroup')->name('groups.leave');
    //group research route :
    Route::get('/searchgroups/{words}', 'GroupController@searchGroups')->name('groups.search');

    //Route::resource('profile', 'ProfileController')->except(['edit', 'create']);
});
