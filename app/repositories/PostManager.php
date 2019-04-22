<?php
namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\repositories\VersionManager;
use App\repositories\CodeSnippetManager;
use App\repositories\CommentManager;
use App\repositories\NotificationManager;
use App\repositories\ProfilePicManager;
use App\repositories\UserDataManager;

use App\Version;
use App\CodeSnippet;
use App\User;
use App\Group;
use App\Post;
use App\Comment;

use Carbon\Carbon;


class PostManager extends Model {

    private  const RECENT_POSTS_FEED_LIMIT = 30;
    private  const MAIN_POSTS_FEED_LIMIT = 30;
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
    usort($posts, array('App\Repositories\PostManager', 'sortByDate'));
    return $posts;
  }

  public static function getGroupPosts(Group $group) {
    $posts = Post::where('group_id', $group->id)->get();
    $postsBuild= [];
    foreach ($posts as &$post) {
      $pBuild = self::buildPostForList($post);
      $postsBuild[] = $pBuild;
    }
    usort($postsBuild, array('App\Repositories\PostManager', 'sortByDate'));
    return $postsBuild;
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public static function store(Request $request) {
    $user = auth()->user();
    $post = new Post();
    $post->title = $request->title;
    $post->group_id = $request->group_id;
    $post->group_name = Group::find($request->group_id)->name;
    $post->group = array(
      "_id" => Group::find($request->group_id)->id,
      "name" => Group::find($request->group_id)->name,
      "description" => Group::find($request->group_id)->description,
      "keywords" => Group::find($request->group_id)->keywords,
      "users_id" => Group::find($request->group_id)->users_id,
      "users" => Group::find($request->group_id)->users
    );
    $post->votes = [];
    $post->keywords = $request->keywords;
    $post->author_id = $user->id;
    $post->author_name = $user->name;

    $picUrl = ProfilePicManager::getUserProfilePic($user->id);
    if ($picUrl == "false") $picUrl = 'http://localhost/developeers/public/blank_profile_pic.png';
    $post->author_profile_pic_url = $picUrl;

    $post->followers = [$user->id];//author automatically follows his own post
    $post->save();

    VersionManager::createInitPostVersion($request->text_content, $request->code_snippets, $post);

    return $post;
  }

  public static function updateAuthorProfilePicUrl(Post $post, $url) {
      $post->author_profile_pic_url = $url;
      $post->save();
      return "Author profile picture updated successfully.";
  }

  public static function votePost(Request $request, Post $post, User $user) {

    $notifying = $user;
    $vote = $request->vote;
    $postVotes = $post->votes;

    $notifType = "vote";
    $notifSource = "post";

    $userAlreadyVoted = false;
    $key;

    foreach ($postVotes as $k=>$pv) {
      $userAlreadyVoted = ($pv['user']['id'] == $notifying->id) ? true : false;
      $key = $userAlreadyVoted ? $k : null;
    }

    $previousValueVoted = $userAlreadyVoted ? $postVotes[$key]["vote"] : null;

    if (!$userAlreadyVoted) {

      $postVotes[] = [
        "vote"=>$vote,
        "user"=>[
          "name"=>$notifying->name,
          "id"=>$notifying->id
        ]
      ];
      $post->votes = $postVotes;
      $post->save();

      NotificationManager::notifyPostAuthor($post, $notifying, $notifType, $notifSource, $post->id);

      return "Vote added on post successfully!";

    } else if ($userAlreadyVoted) {

      if ($previousValueVoted != $vote) {
        $postVotes[$key]["vote"] = $vote;
        $post->votes = $postVotes;
        $post->save();
        NotificationManager::notifyPostAuthor($post, $notifying, $notifType, $notifSource, $post->id);

        return "Vote has been updated successfully !";
      } else return "User already voted";

    }

  }

  public static function getPostVersion(Post $post, Version $version) {

    $snippets = CodeSnippetManager::getVersionSnippets($version);

    $versions = VersionManager::getPostVersions($post);
    $versionsList = [];

    $comments = CommentManager::getComments($version);

    foreach ($versions as $v) {
      array_push($versionsList, array(
        'number' => $v->number,
        '_id' => $v->id
      ));
    }

    $postBuild = $post;
    $postBuild->versions = $versionsList;
    $postBuild->active_version = $version;
    $postBuild->active_version->code_snippets = $snippets;
    $postBuild->active_version->comments = $comments;

    return $postBuild;
  }

  public static function getPostVersionCommentPage(Post $post, Version $version, Comment $comment) {
      $snippets = CodeSnippetManager::getVersionSnippets($version);

      $versions = VersionManager::getPostVersions($post);
      $versionsList = [];

      $comments = CommentManager::getCommentsAfter($version, $comment);

      foreach ($versions as $v) {
        array_push($versionsList, array(
          'number' => $v->number,
          '_id' => $v->id
        ));
      }

      $postBuild = $post;
      $postBuild->versions = $versionsList;
      $postBuild->active_version = $version;
      $postBuild->active_version->code_snippets = $snippets;
      $postBuild->active_version->comments = $comments;

      return $postBuild;
  }

  // public static function getPostVersionCommentPrevPage(Post $post, Version $version, Comment $comment) {
  //     $snippets = CodeSnippetManager::getVersionSnippets($version);
  //
  //     $versions = VersionManager::getPostVersions($post);
  //     $versionsList = [];
  //
  //     $comments = CommentManager::getCommentsBefore($version, $comment);
  //
  //     foreach ($versions as $v) {
  //       array_push($versionsList, array(
  //         'number' => $v->number,
  //         '_id' => $v->id
  //       ));
  //     }
  //
  //     $postBuild = $post;
  //     $postBuild->versions = $versionsList;
  //     $postBuild->active_version = $version;
  //     $postBuild->active_version->code_snippets = $snippets;
  //     $postBuild->active_version->comments = $comments;
  //
  //     return $postBuild;
  // }

  public static function getPost(Post $post) {

    $versions = VersionManager::getPostVersions($post);
    $lastVersion = $versions[count($versions) - 1];

    $snippets = CodeSnippetManager::getVersionSnippets($lastVersion);

    $comments = CommentManager::getComments($lastVersion);

    $versionsList = [];
    foreach ($versions as $v) {
      array_push($versionsList, array(
        'number' => $v->number,
        '_id' => $v->id
      ));
    }

    $postBuild = $post;
    $postBuild->versions = $versionsList;
    $postBuild->active_version = $lastVersion;
    $postBuild->active_version->code_snippets = $snippets;
    $postBuild->active_version->comments = $comments;

    return $postBuild;
  }

  private static function getExcerpt($text) {
      if (strlen($text)) {
          if (strlen($text) < 100) {
              return $text;
          } else {
              return substr($text, 0, 99).' [...]';
          }
      }
  }

  public static function getUserPosts(User $user) {

    $posts = Post::whereIn('followers', [$user->id])->get();
    $postsBuild  = [];
    foreach ($posts as $post) {
        $pBuild = self::buildPostForList($post);
        $postsBuild[] = $pBuild;
    }
    usort($postsBuild, array('App\Repositories\PostManager', 'sortByDate'));
    return $postsBuild;
  }

  public static function getAuthorPost() {
    $user = auth()->user();
    $posts = Post::where('author_id', $user->id)->get();
    $postsBuild = [];

    foreach ($posts as &$post) {
      $pBuild = self::buildPostForList($post);
      $postsBuild[] = $pBuild;
    }
    usort($postsBuild, array('App\Repositories\PostManager', 'sortByDate'));
    return $postsBuild;
  }

  public static function searchPosts($words) {

    $searchWords = explode("-", strtolower($words));

    $allPosts = Post::all();
    $inKeywordsPosts = [];

    foreach ($allPosts as $p) {

      $postKeywords = $p->keywords;//check keywords
      $titleWords = explode(" ", strtolower($p->title));//check title words
      $postAuthorName = explode(" ", strtolower($p->author_name));//check in author's name

      foreach ($searchWords as $sw) {
        //search in keywords
        if (in_array($sw, $postKeywords)) {
            $pBuild = self::buildPostForList($p);
            $inKeywordsPosts[] = $pBuild;
        }
        //search in title
        if (in_array($sw, $titleWords)) {
            $pBuild = self::buildPostForList($p);
            $inKeywordsPosts[] = $pBuild;
        }
        //search in author's name
        if (in_array($sw, $postAuthorName)) {
            $pBuild = self::buildPostForList($p);
            $inKeywordsPosts[] = $pBuild;
        }
      }
    }

    $total = array_unique($inKeywordsPosts);
    $searchResult = [];
    foreach($total as $tp) {
        $searchResult[] = $tp;
    }

    usort($searchResult, array('App\Repositories\PostManager', 'sortByDate'));

    return $searchResult;
  }

  public static function followPost(Post $post, User $user) {
      $followers = $post->followers;
      if(!in_array($user->id, $followers)) {
          $followers[] = $user->id;
          $post->followers = $followers;
          $post->save();
          $msg = "User is now following the post : ".$post->title;
          return $msg;
      } else return "User is already following this post";
  }

  public static function unfollowPost(Post $post, User $user) {
      $followers = $post->followers;
      $key = array_search($user->id, $followers);
      if (gettype($key) == 'integer') {
          array_splice($followers, $key, 1);
          $post->followers = $followers;
          $post->save();
          $msg = "User will be no longer following the post : ".$post->title ;
          return $msg;
      } else return "User is not following this post";
  }

  public static function getUserFeed(User $user) {

    $postsMainList;

    $userGroups = Group::whereIn('users_id', [$user->id])->get();
    $userKeywords = [];
    foreach ($userGroups as $group) {
        $gKeywords = $group->keywords;
        foreach ($gKeywords as $word) {
            $userKeywords[] = $word;
        }
    }

    //add posts crossed with keywords in followed groups
    if ($userGroups->count() > 0) {
        $postsCrossWords = [];
        foreach($userKeywords as $uWord) {
            $postsHaveWord = Post::whereIn('keywords', [$uWord])->get();
            foreach($postsHaveWord as $post) {
                $postsCrossWords[] = $post;
            }
        }
        $postsMainList = $postsCrossWords;
    }

    //add posts whose author is followed by user
    $followedUsers = UserDataManager::getUsersFollowedBy($user);
    if (count($followedUsers) > 1) {
        foreach ($followedUsers as $fu) {
            if ($fu['id'] != 0) {
                $u = User::find($fu['id']);
                $uPosts = Post::where('author_id', $u->id)->get();
                foreach ($uPosts as $p) {
                    $postsMainList[] = $p;
                }
            }
        }
    }

    //if no groups and no users are followed by user, add all posts
    if ($userGroups->count() == 0 && count($followedUsers) <= 1) {
        $allPosts = Post::all();
        foreach ($allPosts as $p) {
            $postsMainList[] = $p;
        }
    }

    $postsMainList = array_unique($postsMainList);

    $today = Carbon::now();
    $lastMonth = $today->subMonths(1);

    $thisMonthPosts = [];
    $cpPostsMainList = $postsMainList;
    foreach ($cpPostsMainList as $post) {
        $key = array_search($post, $postsMainList);
        $postCreatedAt = Carbon::parse($post->created_at);
        if ( $postCreatedAt->greaterThan($lastMonth) ) {
            $thisMonthPosts[] = $post;
            array_splice($postsMainList, $key, 1);
        }
    }
    usort($postsMainList, array('App\Repositories\PostManager', 'sortByDate'));
    usort($thisMonthPosts, array('App\Repositories\PostManager', 'sortByDate'));

    $postsMainList = (count($postsMainList) > self::MAIN_POSTS_FEED_LIMIT) ? array_slice($postsMainList, 0, self::MAIN_POSTS_FEED_LIMIT) : $postsMainList;
    $thisMonthPosts = (count($thisMonthPosts) > self::RECENT_POSTS_FEED_LIMIT) ? array_slice($thisMonthPosts, 0, self::RECENT_POSTS_FEED_LIMIT) : $thisMonthPosts;

    $total = array_merge($thisMonthPosts, $postsMainList);
    $postsBuildResult=[];
    foreach($total as $p) {
        $postsBuildResult[] = self::buildPostForList($p);
    }
    return $postsBuildResult;
  }

  public static function getGuestFeed() {

      $allPosts = Post::all();
      //pass it to a real array
      $postsMainList  =[];
      foreach ($allPosts as $post) {
          $postsMainList[] = $post;
      }


      $today = Carbon::now();
      $lastMonth = $today->subMonths(1);

      $thisMonthPosts = [];
      $cpPostsMainList = $postsMainList;
      foreach ($cpPostsMainList as $post) {
          $key = array_search($post, $postsMainList);
          $postCreatedAt = Carbon::parse($post->created_at);
          if ( $postCreatedAt->greaterThan($lastMonth) ) {
              $thisMonthPosts[] = $post;
              array_splice($postsMainList, $key, 1);
          }
      }

      usort($postsMainList, array('App\Repositories\PostManager', 'sortByDate'));
      usort($thisMonthPosts, array('App\Repositories\PostManager', 'sortByDate'));

      $postsMainList = (count($postsMainList) > self::MAIN_POSTS_FEED_LIMIT) ? array_slice($postsMainList, 0, self::MAIN_POSTS_FEED_LIMIT) : $postsMainList;
      $thisMonthPosts = (count($thisMonthPosts) > self::RECENT_POSTS_FEED_LIMIT) ? array_slice($thisMonthPosts, 0, self::RECENT_POSTS_FEED_LIMIT) : $thisMonthPosts;

      $total = array_merge($thisMonthPosts, $postsMainList);

      $postsBuildResult=[];
      foreach($total as $p) {
          $postsBuildResult[] = self::buildPostForList($p);
      }
      return $postsBuildResult;
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
      $post->save();
      return "Post successfully updated!";
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  Post  $post
   * @return \Illuminate\Http\Response
   */
  public static function destroyPost(Post $post) {

      $postVersions = VersionManager::getPostVersions($post);

      foreach ($postVersions as $version) {
        $snippets = CodeSnippetManager::getVersionSnippets($version);
        $comments = CommentManager::getComments($version);
        foreach ($snippets as $snippet) {
          $snippet->delete();
        }
        foreach ($comments as $comment) {
            NotificationManager::deleteElementRelatedNotifications($comment->id);
            $comment->delete();
        }
        NotificationManager::deleteElementRelatedNotifications($version->id);
        $version->delete();
      }
      NotificationManager::deleteElementRelatedNotifications($post->id);
      $post->delete();
      return "Post deleted successfully";
  }

  private static function buildPostForList(Post $post) {
      $postVersions = VersionManager::getPostVersions($post);

      $nbVersions = count($postVersions);
      $post->number_of_versions = $nbVersions;

      $excerpt = self::getExcerpt($postVersions[0]->text_content);
      $post->excerpt = $excerpt;
      return $post;
  }

  private static function sortByDate($a, $b) {
      if ($a['created_at'] == $b['created_at']) {
          return 0;
      }
      return ($a['created_at'] < $b['created_at']) ? 1 : -1;
  }
}
