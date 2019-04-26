<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use App\repositories\VersionManager;
use App\repositories\NotificationManager;
use App\repositories\ProfilePicManager;

use App\Version;
use App\Comment;
use App\User;

class CommentManager extends Model {

    private const COMMENT_LIST_LIMIT = 10;//?

  public static function addComment(Request $request, Version $version, User $user) {
    $author = $user;
    $comment = new Comment();
    $comment->author_id = $author->id;
    $comment->author_name = $author->name;
    $picUrl = ProfilePicManager::getUserProfilePic($user->id);
    if ($picUrl == "false") $picUrl = env('APP_PUBLIC_LOCAL_URL').'blank_profile_pic.png';
    $comment->author_profile_pic_url = env('APP_STORAGE_LOCAL_URL').$picUrl;
    $comment->content = $request->content;
    $comment->version_id = $version->id;
    $comment->votes = [];
    $comment->save();

    $notifType = "comment";
    $notifSource = "version";
    NotificationManager::broadcastOnVersion($author, $version, $notifType, $notifSource, $comment->id);
  }

  public static function updateAuthorProfilePicUrl(Comment $comment, $url) {
      $comment->author_profile_pic_url = $url;
      $comment->save();
      return "Author profile picture updated successfully.";
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
    $sortedComments = [];
    foreach($comments as $c) {
        $sortedComments[] = $c;
    }
    usort($sortedComments, array('App\Repositories\CommentManager', 'sortByDate'));
    $result = array_slice($sortedComments, 0, self::COMMENT_LIST_LIMIT);
    return $result;
  }

  public static function getAllComments(Version $version) {
      $comments = Comment::where('version_id', $version->id)->get();
      $sortedComments = [];
      foreach($comments as $c) {
          $sortedComments[] = $c;
      }
      usort($sortedComments, array('App\Repositories\CommentManager', 'sortByDate'));
      return $sortedComments;
  }

  public static function getCommentsAfter(Version $version, Comment $lastCommentOfList) {
      $fullCommentsList = Comment::where('version_id', $version->id)->get();
      $fullCommentsSortedList = [];
      foreach($fullCommentsList as $c) {
          $fullCommentsSortedList[] = $c;
      }
      usort($fullCommentsSortedList, array('App\Repositories\CommentManager', 'sortByDate'));
      //get the key of the pivot comment
      $key = array_search($lastCommentOfList, $fullCommentsSortedList);
      $result = array_slice($fullCommentsSortedList, $key, self::COMMENT_LIST_LIMIT);
      return $result;
  }

  public static function getCommentsBefore(Version $version, Comment $firstCommentOfList) {
      $fullCommentsList = Comment::where('version_id', $version->id)->get();
      $fullCommentsSortedList = [];
      foreach($fullCommentsList as $c) {
          $fullCommentsSortedList[] = $c;
      }
      usort($fullCommentsSortedList, array('App\Repositories\CommentManager', 'sortByDate'));

      $key = array_search($firstCommentOfList, $fullCommentsSortedList);
      $key = ($key - self::COMMENT_LIST_LIMIT >= 0) ? $key - self::COMMENT_LIST_LIMIT : 0;
      $result = array_slice($fullCommentsSortedList, $key, self::COMMENT_LIST_LIMIT);
      return $result;
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

  private static function sortByDate($a, $b) {
      if ($a['created_at'] == $b['created_at']) {
          return 0;
      }
      return ($a['created_at'] < $b['created_at']) ? 1 : -1;
  }
}
