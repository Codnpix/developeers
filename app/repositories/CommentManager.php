<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use App\repositories\VersionManager;

use App\Version;
use App\Comment;

class CommentManager extends Model {

  public static function addComment(Request $request, Version $version) {

    $comment = new Comment();
    $comment->author_id = 1;//Auth::id();
    $comment->content = $request->content;
    $comment->version_id = $version->id;
    $comment->votes = [];
    $comment->save();
  }

  public static function voteComment(Request $request, Comment $comment) {
    $commentVotes = $comment->votes;
    $commentVotes[] = $request->vote;
    $comment->votes = $commentVotes;
    $comment->save();
  }

  public static function getComments(Version $version) {
    $comments = Comment::where('version_id', $version->id)->get();
    return $comments;
  }
}
