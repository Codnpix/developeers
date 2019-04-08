<?php
namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\repositories\VersionManager;
use App\repositories\CodeSnippetManager;
use App\repositories\CommentManager;
use App\repositories\NotificationManager;

use App\Version;
use App\CodeSnippet;
use App\User;
use App\Group;
use App\Post;
use App\Comment;

class PostManager extends Model {
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public static function getAllPosts() {
    $posts = Post::all();
    foreach ($posts as &$post) {
      $versions = VersionManager::getPostVersions($post);
      foreach ($versions as &$version) {
        $snippets = CodeSnippetManager::getVersionSnippets($version);
        $version->codeSnippets = $snippets;
        $comments = CommentManager::getComments($version);
        $version->comments = $comments;
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
      $post->author_name = User::find(1)->name;
      $post->save();

      VersionManager::createInitPostVersion($request->text_content, $request->code_snippets, $post);

      return "success";
  }

  public static function votePost(Request $request, Post $post) {
    $notifying = User::find(1);//User::find(Auth::id());
    $vote = $request->vote;
    $postVotes = $post->votes;
    $postVotes[] = $vote;
    $post->votes = $postVotes;
    $post->save();

    $notifType = "vote";
    $notifSource = "post";
    NotificationManager::notifyPostAuthor($post, $notifying, $notifType, $notifSource);
  }

  /**
   * Display the specified resource.
   *
   * @param  Post
   * @param Version
   * @return \Illuminate\Http\Response
   */
  public static function getPostVersion(Post $post, Version $version) {

    $snippets = CodeSnippetManager::getVersionSnippets($version);

    $versions = VersionManager::getPostVersions($post);
    $versionsList = [];

    $comments = CommentManager::getComments($version);

    foreach ($versions as $v) {
      array_push($versionsList, array($v->number => $v->id));
    }

    $postBuild = $post;
    $postBuild->versions = $versionsList;
    $postBuild->active_version = $version;
    $postBuild->active_version->code_snippets = $snippets;
    $postBuild->active_version->comments = $comments;

    return $postBuild;
  }

  public static function getPost(Post $post) {

    $versions = VersionManager::getPostVersions($post);
    $lastVersion = $versions[count($versions) - 1];

    $snippets = CodeSnippetManager::getVersionSnippets($lastVersion);

    $comments = CommentManager::getComments($lastVersion);

    $versionsList = [];

    foreach ($versions as $version) {
      array_push($versionsList, array($version->number=>$version->id));
    }

    $postBuild = $post;
    $postBuild->versions = $versionsList;
    $postBuild->active_version = $lastVersion;
    $postBuild->active_version->code_snippets = $snippets;
    $postBuild->active_version->comments = $comments;

    return $postBuild;
  }

  public static function getUserPosts(User $user) {

    $userVersions = Version::where('author_id', '=', $user->id)->get();
    $userVersionsPosts = [];
    foreach ($userVersions as $ver) {
      $p = Post::find($ver->post_id);
      $userVersionsPosts[] = $p;
    }
    $userComments = Comment::where('author_id', '=', $user->id)->get();
    $userCommentsPosts = [];
    foreach ($userComments as $com) {
      $ver = Version::find($com->version_id);
      $p = Post::find($ver->post_id);
      $userCommentsPosts[] = $p;
    }
    $totalUserPosts = array_unique(array_merge($userVersionsPosts, $userCommentsPosts));

    return $totalUserPosts;
  }

  public static function getAuthorPost(User $user) {
    $posts = Post::where('author_id', '=', $user->id)->get();
    return $posts;
  }

  public static function searchPosts($words) {

    $searchWords = explode(" ", strtolower($words));

    $allPosts = Post::all();
    $inKeywordsPosts = [];

    foreach ($allPosts as $p) {

      $postKeywords = $p->keywords;//check keywords
      $titleWords = explode(" ", strtolower($p->title));//check title words

      foreach ($searchWords as $sw) {
        //search in keywords
        if (in_array($sw, $postKeywords)) $inKeywordsPosts[] = $p;
        //search in title
        if (in_array($sw, $titleWords)) $inKeywordsPosts[] = $p;
      }
    }

    return array_unique($inKeywordsPosts);
  }

  public static function getUserFeed(User $user) {

    $posts = [];
    $userGroups = Group::whereIn('users_id', [$user->id])->get();
    //return $userGroups;
    foreach ($userGroups as $g) {
      $gPosts = Post::where('group_id', $g->id)
      ->orderBy('updated_at', 'desc')
      ->get();
      foreach ($gPosts as $p) {
        $posts[] = $p;
      }
    }

    array_splice($posts, 10);

    return $posts;
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
