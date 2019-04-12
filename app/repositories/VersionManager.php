<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use App\repositories\CodeSnippetManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Version;
use App\Post;
use App\CodeSnippet;
use App\User;
use App\repositories\NotificationManager;

class VersionManager extends Model {

  public static function commitVersion(Request $request, Post $post, User $user) {
    $versionAuthor = $user;
    $version = new Version();
    $version->author_id = $versionAuthor->id;
    $version->author_name = $versionAuthor->name;
    $version->post_id = $post->id;
    $version->text_content = $request->text_content;
    $version->number = $request->number;
    $version->votes = [];
    $version->save();

    $codeSnippets = $request->code_snippets;

    foreach ($codeSnippets as $snippet) {
      CodeSnippetManager::storeSnippet($snippet, $version->id);
    }

    $notifType = "version";
    $notifSource = "post";
    NotificationManager::broadcastOnPost($post, $versionAuthor, $version, $notifType, $notifSource);
  }

  public static function voteVersion(Request $request, Version $version, User $user) {

    $votingUser = $user;
    $vote = $request->vote;
    $versionVotes = $version->votes;

    $notifType = "vote";
    $notifSource = "version";

    $userAlreadyVoted = false;
    $key;

    foreach ($versionVotes as $k=>$vv) {
      $userAlreadyVoted = ($vv['user']['id'] == $votingUser->id) ? true : false;
      $key = $userAlreadyVoted ? $k : null;
    }

    $previousValueVoted = $userAlreadyVoted ? $versionVotes[$key]["vote"] : null;

    if (!$userAlreadyVoted) {

      $versionVotes[] = [
        "vote"=>$vote,
        "user"=>[
          "name"=>$votingUser->name,
          "id"=>$votingUser->id
        ]
      ];

      $version->votes = $versionVotes;
      $version->save();

      NotificationManager::notifyVersionAuthor($votingUser, $version, $notifType, $notifSource);

      return "Vote added on version successfully!";

    } else if ($userAlreadyVoted) {

      $prevVote = $versionVotes[$key]["vote"];

      if ($prevVote != $vote) {
        $versionVotes[$key]["vote"] = $vote;
        $version->votes = $versionVotes;
        $version->save();
        NotificationManager::notifyVersionAuthor($votingUser, $version, $notifType, $notifSource);

        return "Vote has been updated successfully !";
      } else return "User already voted";

    }
  }

  public static function createInitPostVersion($textContent, $codeSnippets, Post $post) {
    $authorName = User::find($post->author_id)->name;
    $version = new Version();
    $version->number = '1.0';
    $version->author_id = $post->author_id;
    $version->author_name = $authorName;
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
