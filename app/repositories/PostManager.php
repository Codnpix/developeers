<?php
namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;

use App\Version;
use App\CodeSnippet;

class PostManager extends Model {
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public static function getAllPosts() {
    $posts = Post::all();
    foreach ($posts as &$post) {
      $versions = self::getPostVersions($post);
      foreach ($versions as &$version) {
        $snippets = self::getVersionSnippets($version);
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
  public static function store(Request $request) {
      $post = new Post();
      $post->title = $request->title;
      $post->group_id = $request->group_id;
      $post->votes = [];
      $post->keywords = $request->keywords;
      //$post->author_id = Auth::id();
      $post->author_id = 1;
      $post->save();

      self::createInitialPostVersion($request->text_content, $request->code_snippets, $post);

      return "success";
  }

  private static function createInitialPostVersion($textContent, $codeSnippets, Post $post) {
    $version = new Version();
    $version->number = '1.0';
    $version->author_id = $post->author_id;
    $version->post_id = $post->id;
    $version->text_content = $textContent;
    $version->votes = [];

    $version->save();

    foreach ($codeSnippets as $codeSnippet) {

      self::storeSnippet($codeSnippet, $version->id);
    }

  }

  private static function storeSnippet($snippet, $version_id) {
    $codeSnippet = new CodeSnippet();
    $codeSnippet->content = $snippet;
    $codeSnippet->version_id = $version_id;
    $codeSnippet->save();
  }

  private static function getPostVersions(Post $post) {
    $versions = Version::where('post_id', $post->id)->get();
    return $versions;
  }

  private static function getVersionSnippets(Version $version) {
    $snippets = CodeSnippet::where('version_id', $version->id)->get();
    return $snippets;
  }

  public static function commitVersion(Request $request, Post $post) {
    $version = new Version();
    $version->author_id = 1;//Auth::id();
    $version->post_id = $post->id;
    $version->text_content = $request->text_content;
    $version->votes = [];
    $version->save();

    $codeSnippets = $request->code_snippets;

    foreach ($codeSnippets as $snippet) {
      self::storeSnippet($snippet, $version->id);
    }
  }

  public static function votePost(Request $request, Post $post) {
    $vote = $request->vote;
    $postVotes = $post->votes;
    $postVotes[] = $vote;
    $post->votes = $postVotes;
    $post->save();
  }

  public static function voteVersion(Request $request, Version $version) {
    $vote = $request->vote;
    $versionVotes = $version->votes;
    $versionVotes[] = $vote;
    $version->votes = $versionVotes;
    $version->save();
  }

  /**
   * Display the specified resource.
   *
   * @param  Post
   * @return \Illuminate\Http\Response
   */
  public static function show(Post $post) {

      $versions = self::getPostVersions($post);

      foreach ($versions as $version) {
        $version->codeSnippets = self::getVersionSnippets($version);
      }

      $postBuild = $post;
      $postBuild->versions = $versions;

      return $postBuild;
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  Post
   */
  public static function updatePost(Request $request, Post $post) {

      $post->title = $request->title;
      $post->keywords = $request->keywords;
      $post->textContent = $request->text_content;
      $post->group_id = $post->group_id; //inchangable;
      //$post->author_id = Auth::id();
      $post->save();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  Post  $post
   * @return \Illuminate\Http\Response
   */
  public static function destroyPost(Post $post) {

      $postVersions = self::getPostVersions($post);

      foreach ($postVersions as $version) {
        $snippets = self::getVersionSnippets($version);
        foreach ($snippets as $snippet) {
          $snippet->delete();
        }
        $version->delete();
      }
      $post->delete();
  }
}
