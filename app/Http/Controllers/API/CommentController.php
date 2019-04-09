<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Comment;
use App\Version;
use App\repositories\CommentManager;

class CommentController extends Controller {

  public function addComment(Request $request, Version $version) {
    $user = auth()->user();
    CommentManager::addComment($request, $version, $user);
    return 'Comment added successfully!';
  }

  public function voteComment(Request $request, Comment $comment) {
    $user = auth()->user();
    $msg = CommentManager::voteComment($request, $comment, $user);
    return $msg;
  }
}
