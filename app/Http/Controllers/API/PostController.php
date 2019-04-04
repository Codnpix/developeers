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

class PostController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

      $posts = Post::all();

      foreach ($posts as &$post) {
        $versions = $this->getPostVersions($post);
        foreach ($versions as &$version) {
          $snippets = $this->getVersionSnippets($version);
          $version->codeSnippets = $snippets;
        }
        $post->versions = $versions;
      }
      return $posts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $post = new Post();

        $post->title = $request->title;
        $post->group_id = $request->group_id;
        $post->votes = [];
        $post->keywords = $request->keywords;
        //$post->author_id = Auth::id();
        $post->author_id = 1;

        $post->save();

        $this->createInitialPostVersion($request->text_content, $request->code_snippets, $post);

        return "success";

    }

    private function createInitialPostVersion($textContent, $codeSnippets, Post $post) {

      $version = new Version();
      $version->number = '1.0';
      $version->author_id = $post->author_id;
      $version->post_id = $post->id;
      $version->text_content = $textContent;
      $version->votes = [];

      $version->save();

      foreach ($codeSnippets as $codeSnippet) {

        $this->storeSnippet($codeSnippet, $version->id);
      }

    }

    private function storeSnippet($snippet, $version_id) {

      $codeSnippet = new CodeSnippet();

      $codeSnippet->content = $snippet;
      $codeSnippet->version_id = $version_id;

      $codeSnippet->save();

    }

    private function getPostVersions(Post $post) {

      $versions = Version::where('post_id', $post->id)->get();

      return $versions;
    }

    private function getVersionSnippets(Version $version) {

      $snippets = CodeSnippet::where('version_id', $version->id)->get();

      return $snippets;
    }

    /**
     * Display the specified resource.
     *
     * @param  Post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post) {

        /*
        * Retourne un post complet à partir de $post et du contenu de ses versions
        */

        $versions = $this->getPostVersions($post);

        foreach ($versions as $version) {

          $version->codeSnippets = $this->getVersionSnippets($version);
        }

        $response = $post;
        $response->versions = $versions;

        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post) {

        /*
        * Récupérer dans les champs de la requête : codeSnippets, textContent, numéro de version
        * Créer une nouvelle version (VC::store(req))
        */

        $post->title = $request->title;
        $post->keywords = $request->keywords;
        $post->textContent = $request->text_content;
        $post->group_id = $post->group_id; //inchangable;
        //$post->author_id = Auth::id();
        $post->save();

        return "Post successfully updated!";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        /*
        * supprimer le document de ce post en bdd, et supprimer tous les documents
        * des versions qu'il contient (en entrainant la suppression des codesnippets de la version et des commentaires)
        */

        $postVersions = $this->getPostVersions($post);

        foreach ($postVersions as $version) {

          $snippets = $this->getVersionSnippets($version);

          foreach ($snippets as $snippet) {

            $snippet->delete();
          }

          $version->delete();
        }

        $post->delete();
    }
}
