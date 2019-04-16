<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Support\Facades\Auth;
use App\Version;
use App\CodeSnippet;
use App\repositories\PostManager;
use App\Comment;
use App\User;
use App\Group;
use App\repositories\CommentManager;
use App\repositories\VersionManager;

class GuestController extends Controller {

  public function showAllPosts() {
    $posts = PostManager::getAllPosts();
    return $posts;
  }

  public function showPost(Post $post) {
    $postBuild = PostManager::getPost($post);
    return $postBuild;
  }

  public function showGroup(Group $group) {
      return $group;
  }

  public function showGroupPosts(Group $group) {
    $posts = PostManager::getGroupPosts($group);
    return $posts;
  }

  public function showVersion(Post $post, Version $version) {
    $postBuild = PostManager::getPostVersion($post, $version);
    return $postBuild;
  }

  public function showGuestFeed() {
    $posts = PostManager::getGuestFeed();
    return $posts;
  }

  public function searchPosts(Request $request, $words) {
    $posts = PostManager::searchPosts($words);
    return $posts;
  }

  public function searchGroups(Request $request, $words) {
    $groups = GroupManager::searchGroups($words);
    return $groups;
  }
}
