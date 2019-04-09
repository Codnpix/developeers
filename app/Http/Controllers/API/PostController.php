<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Support\Facades\Auth;
//use App\Http\Controllers\API\VersionController;
//use App\VersionManager;
use App\Version;
use App\CodeSnippet;
use App\repositories\PostManager;
use App\Comment;
use App\User;
use App\Group;
use App\repositories\CommentManager;
use App\repositories\VersionManager;
use App\repositories\NotificationManager;

class PostController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
      $posts = PostManager::getAllPosts();
      return $posts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        PostManager::store($request);
        return "success";
    }

    public function votePost(Request $request, Post $post) {
      $msg = PostManager::votePost($request, $post);
      return $msg;

    }

    public function commitVersion(Request $request, Post $post) {
      $user = auth()->user();
      VersionManager::commitVersion($request, $post, $user);
      return "Version committed successfully !";
    }

    public function voteVersion(Request $request, Version $version) {
      $user = auth()->user();
      $msg = VersionManager::voteVersion($request, $version, $user);
      return $msg;
    }

    /**
     * Display the specified resource.
     *
     * @param  Post
     * @param Version
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post) {
      $postBuild = PostManager::getPost($post);
      $user = User::find(1);//User::find(Auth::id());
      NotificationManager::clearNotifications($user, $post);
      return $postBuild;
    }

    public function showUserPosts(Post $post, User $user) {
      $posts = PostManager::getUserPosts($user);
      return $posts;
    }

    public function showAuthorPosts(User $user) {
      $posts = PostManager::getAuthorPost($user);
      return $posts;
    }

    public function showGroupPosts(Group $group) {
      $posts = PostManager::getGroupPosts($group);
      return $posts;
    }

    public function showVersion(Post $post, Version $version) {
      $postBuild = PostManager::getPostVersion($post, $version);
      $user = User::find(1);//User::find(Auth::id());
      NotificationManager::clearNotifications($user, $post);
      return $postBuild;
    }

    public function showUserFeed(User $user) {
      $posts = PostManager::getUserFeed($user);
      return $posts;
    }

    public function searchPosts(Request $request, $words) {
      $posts = PostManager::searchPosts($words);
      return $posts;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post) {
        PostManager::updatePost($request, $post);
        return "Post successfully updated!";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post) {
        PostManager::destroyPost($post);
        return "Post deleted successfully";
    }
}
