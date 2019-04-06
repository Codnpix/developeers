<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use App\repositories\CodeSnippetManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Version;
use App\Post;
use App\CodeSnippet;

class VersionManager extends Model {

  public static function commitVersion(Request $request, Post $post) {
    $version = new Version();
    $version->author_id = 1;//Auth::id();
    $version->post_id = $post->id;
    $version->text_content = $request->text_content;
    $version->votes = [];
    $version->save();

    $codeSnippets = $request->code_snippets;

    foreach ($codeSnippets as $snippet) {
      CodeSnippetManager::storeSnippet($snippet, $version->id);
    }
  }

  public static function voteVersion(Request $request, Version $version) {
    $vote = $request->vote;
    $versionVotes = $version->votes;
    $versionVotes[] = $vote;
    $version->votes = $versionVotes;
    $version->save();
  }

  public static function createInitPostVersion($textContent, $codeSnippets, Post $post) {
    $version = new Version();
    $version->number = '1.0';
    $version->author_id = $post->author_id;
    $version->post_id = $post->id;
    $version->text_content = $textContent;
    $version->votes = [];

    $version->save();

    foreach ($codeSnippets as $codeSnippet) {

      CodeSnippetManager::storeSnippet($codeSnippet, $version->id);
    }
  }

  public static function getPostVersions(Post $post) {
    $versions = Version::where('post_id', $post->id)->get();
    return $versions;
  }
}
