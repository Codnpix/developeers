<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use App\repositories\CodeSnippetManager;
use App\repositories\CommentManager;
use App\repositories\NotificationManager;
use App\repositories\ProfilePicManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Version;
use App\Post;
use App\CodeSnippet;
use App\User;


class VersionManager extends Model {

  public static function commitVersion(Request $request, Post $post, User $user) {
    $versionAuthor = $user;
    $version = new Version();
    $version->author_id = $versionAuthor->id;
    $version->author_name = $versionAuthor->name;

    $picUrl = ProfilePicManager::getUserProfilePic($user->id);
    if ($picUrl == "false") $picUrl = 'http://localhost/developeers/public/blank_profile_pic.png';
    $version->author_profile_pic_url = $picUrl;

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
    NotificationManager::broadcastOnPost($post, $versionAuthor, $version, $notifType, $notifSource, $version->id);
  }

  public static function updateAuthorProfilePicUrl(Version $version, $url) {
      $version->author_profile_pic_url = $url;
      $version->save();
      return "Author profile picture updated successfully.";
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

      NotificationManager::notifyVersionAuthor($votingUser, $version, $notifType, $notifSource, $version->id);

      return "Vote added on version successfully!";

    } else if ($userAlreadyVoted) {

      $prevVote = $versionVotes[$key]["vote"];

      if ($prevVote != $vote) {
        $versionVotes[$key]["vote"] = $vote;
        $version->votes = $versionVotes;
        $version->save();
        NotificationManager::notifyVersionAuthor($votingUser, $version, $notifType, $notifSource, $version->id);

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

    $picUrl = ProfilePicManager::getUserProfilePic($post->author_id);
    if ($picUrl == "false") $picUrl = 'http://localhost/developeers/public/blank_profile_pic.png';
    $version->author_profile_pic_url = $picUrl;

    $version->post_id = $post->id;
    $version->text_content = $textContent;
    $version->votes = [];

    $version->save();

    foreach ($codeSnippets as $codeSnippet) {

      CodeSnippetManager::storeSnippet($codeSnippet, $version->id);
    }
  }

  public static function updateVersion(Request $request, Version $version) {

    $newSnippets = $request->code_snippets;
    CodeSnippetManager::updateSnippets($version, $newSnippets);

    $version->text_content = $request->text_content;
    $version->save();

    return "Version updated successfully";
  }

  public static function getPostVersions(Post $post) {
    $versions = Version::where('post_id', $post->id)->get();
    return $versions;
  }

  public static function destroyVersion(Version $version) {

    $snippets = CodeSnippetManager::getVersionSnippets($version);
    $comments = CommentManager::getComments($version);
    foreach($snippets as $snippet) {
        $snippet->delete();
    }
    foreach($comments as $comment) {
        NotificationManager::deleteElementRelatedNotifications($comment->id);
        $comment->delete();
    }

    NotificationManager::deleteElementRelatedNotifications($version->id);

    $version->delete();
    return "Version deleted successfully";
  }
}
