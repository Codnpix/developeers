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

  public static function addComment(Request $request, Version $version) {
    $author = User::find(1);//User::find(Auth::id());
    $comment = new Comment();
    $comment->author_id = $author->id;
    $coment->author_name = $author->name;
    $comment->content = $request->content;
    $comment->version_id = $version->id;
    $comment->votes = [];
    $comment->save();

    $notifType = "comment";
    $notifSource = "version";
    NotificationManager::broadcastOnVersion($author, $version, $notifType, $notifSource);
  }

  public static function voteComment(Request $request, Comment $comment) {
    $votingUser = User::find(1); //User::find(Auth::id());
    $commentVotes = $comment->votes;
    $commentVotes[] = $request->vote;
    $comment->votes = $commentVotes;
    $comment->save();

    $notifType = "vote";
    $notifSource = "comment";
    NotificationManager::notifyCommentAuthor($votingUser, $comment, $notifType, $notifSource);
  }

  public static function getComments(Version $version) {
    $comments = Comment::where('version_id', $version->id)->get();
    return $comments;
  }
}
