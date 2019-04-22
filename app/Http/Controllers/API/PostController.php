<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Support\Facades\Auth;
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
        $post = PostManager::store($request);
        return $post;
    }

    public function votePost(Request $request, Post $post) {
      $user = auth()->user();
      $msg = PostManager::votePost($request, $post, $user);
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
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post) {
      $postBuild = PostManager::getPost($post);
      $version = Version::find($postBuild->active_version->id);
      $user = auth()->user();
      NotificationManager::clearNotifications($user, $post, $version);
      return $postBuild;
    }

    public function showUserPosts(Post $post) {
      $user = auth()->user();
      $posts = PostManager::getUserPosts($user);
      return $posts;
    }

    public function showAuthorPosts() {
      $user = auth()->user();
      $posts = PostManager::getAuthorPost($user);
      return $posts;
    }

    public function showGroupPosts(Group $group) {
      $posts = PostManager::getGroupPosts($group);
      return $posts;
    }

    public function showVersion(Post $post, Version $version) {
      $postBuild = PostManager::getPostVersion($post, $version);
      $user = auth()->user();
      NotificationManager::clearNotifications($user, $post, $version);
      return $postBuild;
    }

    public function showUserFeed() {
      $user = auth()->user();
      $posts = PostManager::getUserFeed($user);
      return $posts;
    }

    public function searchPosts(Request $request, $words) {
      $posts = PostManager::searchPosts($words);
      return $posts;
    }

    public function followPost(Post $post) {
        $user = auth()->user();
        $response = PostManager::followPost($post, $user);
        return $response;
    }

    public function unfollowPost(Post $post) {
        $user = auth()->user();
        $response = PostManager::unfollowPost($post, $user);
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post) {
        $response = PostManager::updatePost($request, $post);
        return $response;
    }

    public function updateVersion(Request $request, Version $version) {
      $response = VersionManager::updateVersion($request, $version);
      return $response;
    }

    public function updateComment(Request $request, Comment $comment) {
      $response = CommentManager::updateComment($request, $comment);
      return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post) {
        $response = PostManager::destroyPost($post);
        return $response;
    }

    public function destroyPostVersion(Version $version) {
      $response = VersionManager::destroyVersion($version);
      return $response;
    }

    public function destroyComment(Comment $comment) {
      $response = CommentManager::destroyComment($comment);
      return $response;
    }
}
