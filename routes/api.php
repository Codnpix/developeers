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

//FREE ACCESS
Route::namespace('API')->middleware('cors')->group(function(){
    Route::options('{any}');//useless?
    Route::post('login', 'PassportController@login')->name('login');
    Route::post('register', 'PassportController@register')->name('register');

    //GUEST POSTS & Groups
    //all posts :
    Route::get('/guest/allposts', 'GuestController@showAllPosts')->name('guest.allposts');
    //single posts by id :
    Route::get('/guest/posts/{post}', 'GuestController@showPost')->name('guest.post');
    //single group by id :
    Route::get('/guest/groups/{group}', 'GuestController@showGroup')->name('guest.group');
    //posts list in a group :
    Route::get('/guest/groupposts/{group}', 'GuestController@showGroupPosts')->name('guest.groupPosts');
    //Specific version of a post :
    Route::get('/guest/postversion/{post}/{version}', 'GuestController@showVersion')->name('guest.postVersion');
    //posts feed :
    Route::get('/guest/postsfeed', 'GuestController@showGuestFeed')->name('guest.postsfeed');
    //search posts :
    Route::get('/guest/searchposts/{words}', 'GuestController@searchPosts')->name('guest.searchPosts');
    //search groups :
    Route::get('/guest/searchgroups/{words}', 'GuestController@searchGroups')->name('guest.searchGroups');
    //comments pages :
    Route::get('/guest/commentsafter/{post}/{version}/{comment}', 'CommentController@getCommentsAfter')->name('comments.after');
    Route::get('/guest/commentsbefore/{post}/{version}/{comment}', 'CommentController@getCommentsBefore')->name('comments.after');
});

//AUTHENTICATED ONLY
Route::middleware('auth:api', 'cors')->namespace('API')->group(function () {
    Route::options('{any}');
    //USER
    Route::get('user', 'PassportController@details')->name('user.details');
    Route::put('user/update', 'PassportController@updateUser')->name('user.update');
    Route::post('user/uploadpic', 'UserController@uploadProfilePic')->name('user.uploadPic');
    Route::get('user/profilepic', 'UserController@getProfilePic')->name('user.getProfilePic');
    Route::get('user/profilepic/{user}', 'UserController@getAnUserProfilePic')->name('user.getUserProfilePic');
    //USER DATA
    Route::post('/inituserdata', 'PassportController@storeInitUserData')->name('user.initdata');
    Route::get('/userdata','UserController@getUserData')->name('user.userdata');
    Route::get('/userdata/{user}', 'UserController@getAnUserData')->name('user.anuserdata');
    Route::put('/userdata', 'UserController@updateUserData')->name('user.updateData');
    Route::put('/followuser/{user}', 'UserController@followUser')->name('user.followuser');
    Route::put('/unfollowuser/{user}', 'UserController@unfollowUser')->name('user.unfollowuser');
    //NOTIFICATIONS
    //get all user notifications :
    Route::get('/notifications', 'UserController@getNotifications')->name('user.notifications');
    Route::delete('/notifications/{notification}', 'UserController@deleteObsoleteNotification')->name('user.deleteObsoleteNotification');
    Route::put('/notifications/{notifiation}', 'UserController@markNotificationRead')->name('user.readNotification');
    //POST
    //get all posts wich user is involved on. :
    Route::get('/userposts', 'PostController@showUserPosts')->name('posts.user');
    //get all posts whose author is the user. :
    Route::get('/authorposts', 'PostController@showAuthorPosts')->name('posts.author');
    //store new post :
    Route::post('/posts', 'PostController@store')->name('posts.store');
    //modify post data (title, keywords) :
    Route::put('/posts/{post}', 'PostController@update')->name('posts.update');
    //delete a post
    Route::delete('/posts/{post}', 'PostController@destroy')->name('posts.delete');
    //vote for a post (vote = true or false)
    Route::put('/votepost/{post}', 'PostController@votePost')->name('posts.vote');
    //post get user feed
    Route::get('/postsfeed', 'PostController@showUserFeed')->name('posts.feed');
    //get all posts (heavy!)
    Route::get('/posts', 'PostController@index')->name('posts');
    //get all posts in a specific group:
    Route::get('/posts/group/{group}', 'PostController@showGroupPosts')->name('posts.group');
    //get a specific post by its id (default version : last version):
    Route::get('/posts/{post}', 'PostController@show')->name('posts.post');
    //get a specific version of a specific post :
    Route::get('/posts/{post}/{version}', 'PostController@showVersion')->name('posts.version');
    //post research routes :
    Route::get('/searchposts/{words}', 'PostController@searchPosts')->name('posts.search');
    //delete post specific version
    Route::delete('/deleteversion/{version}', 'PostController@destroyPostVersion')->name('posts.deleteVersion');
    //delete a comment
    Route::delete('/deletecomment/{comment}', 'PostController@destroyComment')->name('posts.deleteComment');
    //update a version
    Route::put('/updateversion/{version}', 'PostController@updateVersion')->name('posts.updateVersion');
    //update a comment
    Route::put('/updatecomment/{comment}', 'PostController@updateComment')->name('posts.updateComment');
    //follow a post
    Route::put('/follow/{post}', 'PostController@followPost')->name('post.follow');
    //unfollow a post
    Route::put('/unfollow/{post}', 'PostController@unfollowPost')->name('post.unfollow');

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
    Route::get('/commentsafter/{post}/{version}/{comment}', 'CommentController@getCommentsAfter')->name('comments.after');
    Route::get('/commentsbefore/{post}/{version}/{comment}', 'CommentController@getCommentsBefore')->name('comments.after');

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
