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
use App\repositories\CommentManager;

class PostController extends Controller {


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

      $posts = PostManager::getAllPosts();
      $postsBuild = [];

      foreach ($posts as $post) {
        $postBuild = PostManager::getPost($post);
        $postsBuild[] = $postBuild;
      }
      return $postsBuild;
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
      PostManager::votePost($request, $post);
      return "Vote added on post successfully!";
    }

    public function addComment(Request $request, Version $version) {
      //CommentManager::addComment($request, $version);
      //return 'Comment added successfully!';
    }

    public function voteComment(Request $request, Comment $comment) {
      //
    }

    public function commitVersion(Request $request, Post $post) {
      PostManager::commitVersion($request, $post);
      return "Version committed successfully !";
    }

    public function voteVersion(Request $request, Version $version) {
      PostManager::voteVersion($request, $version);
      return 'Vote added on version successfully!';
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
        return $postBuild;
    }

    public function showVersion(Post $post, Version $version) {
      $postBuild = PostManager::getPostVersion($post, $version);
      return $postBuild;
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
