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

class PostController extends Controller {

//PLUS TARD; DÉPLACER TOUTE L'INTELLINGENCE MÉTIER, OPÉRATIONS SUR TABLES ETC DANS UNE CLASS REPOSITORY
//DU COUP, NE PLUS UTILISER LES ROUTES RESSOURCES SUR LES MÉTHODES

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

    public function commitVersion(Request $request, Post $post) {
      PostManager::commitVersion($request, $post);
      return "Version committed successfully !";
    }

    public function votePost(Request $request, Post $post) {
      PostManager::votePost($request, $post);
      return "Vote added on post successfully!";
    }

    public function voteVersion(Request $request, Version $version) {
      PostManager::voteVersion($request, $version);
      return 'Vote added on version successfully!';
    }

    /**
     * Display the specified resource.
     *
     * @param  Post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post) {
        $postBuild = PostManager::show($post);
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
