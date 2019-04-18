<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use App\repositories\VersionManager;
use App\repositories\NotificationManager;

use App\Version;
use App\Comment;
use App\User;

class CommentManager extends Model {

  public static function addComment(Request $request, Version $version, User $user) {
    $author = $user;
    $comment = new Comment();
    $comment->author_id = $author->id;
    $comment->author_name = $author->name;
    $comment->content = $request->content;
    $comment->version_id = $version->id;
    $comment->votes = [];
    $comment->save();

    $notifType = "comment";
    $notifSource = "version";
    NotificationManager::broadcastOnVersion($author, $version, $notifType, $notifSource, $comment->id);
  }

  public static function voteComment(Request $request, Comment $comment, User $user) {

    $votingUser = $user;
    $vote = $request->vote;
    $commentVotes = $comment->votes;

    $notifType = "vote";
    $notifSource = "comment";

    $userAlreadyVoted = false;
    $key;

    foreach ($commentVotes as $k=>$cv) {
      $userAlreadyVoted = ($cv['user']['id'] == $votingUser->id) ? true : false;
      $key = $userAlreadyVoted ? $k : null;
    }

    $previousValueVoted = $userAlreadyVoted ? $commentVotes[$key]["vote"] : null;

    if (!$userAlreadyVoted) {

      $commentVotes[] = [
        "vote"=>$vote,
        "user"=>[
          "name"=>$votingUser->name,
          "id"=>$votingUser->id
        ]
      ];

      $comment->votes = $commentVotes;
      $comment->save();

      NotificationManager::notifyCommentAuthor($votingUser, $comment, $notifType, $notifSource, $comment->id);

      return "Vote added on comment successfully!";

    } else if ($userAlreadyVoted) {

      $prevVote = $commentVotes[$key]["vote"];

      if ($prevVote != $vote) {
        $commentVotes[$key]["vote"] = $vote;
        $comment->votes = $commentVotes;
        $comment->save();

        NotificationManager::notifyCommentAuthor($votingUser, $comment, $notifType, $notifSource, $comment->id);

        return "Vote has been updated successfully !";
      } else return "User already voted";
    }
  }

  public static function getComments(Version $version) {
    $comments = Comment::where('version_id', $version->id)->get();
    return $comments;
  }

  public static function updateComment(Request $request, Comment $comment) {
    $comment->content = $request->content;
    $comment->save();
    return "Comment updated successfully.";
  }

  public static function destroyComment(Comment $comment) {
    NotificationManager::deleteElementRelatedNotifications($comment->id);
    $comment->delete();
    return "Comment deleted successfully";
  }
}
