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

use App\Version;
use App\CodeSnippet;
use App\User;
use App\Group;
use App\Post;
use App\Comment;

use Carbon\Carbon;


class PostManager extends Model {

    private  const RECENT_POSTS_FEED_LIMIT = 5;
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
    return $posts;
  }

  public static function getGroupPosts(Group $group) {
    $posts = Post::where('group_id', $group->id)->get();
    $postsBuild= [];
    foreach ($posts as &$post) {
      $pBuild = self::buildPostForList($post);
      $postsBuild[] = $pBuild;
    }
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

  private static function buildPostForList(Post $post) {
      $postVersions = VersionManager::getPostVersions($post);

      $nbVersions = count($postVersions);
      $post->number_of_versions = $nbVersions;

      $excerpt = self::getExcerpt($postVersions[0]->text_content);
      $post->excerpt = $excerpt;
      return $post;
  }

  public static function getUserPosts(User $user) {

    $posts = Post::whereIn('followers', [$user->id])->get();
    $postsBuild  = [];
    foreach ($posts as $post) {
        $pBuild = self::buildPostForList($post);
        $postsBuild[] = $pBuild;
    }
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

    return $postsBuild;
  }

  public static function searchPosts($words) {

    $searchWords = explode("-", strtolower($words));

    $allPosts = Post::all();
    $inKeywordsPosts = [];

    foreach ($allPosts as $p) {

      $postKeywords = $p->keywords;//check keywords
      $titleWords = explode(" ", strtolower($p->title));//check title words

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
      }
    }

    $total = array_unique($inKeywordsPosts);
    $searchResult = [];
    foreach($total as $tp) {
        $searchResult[] = $tp;
    }

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

    /*-------------------------------------
    * ALGO - PSEUDO CODE
    * postsFeed (auth user) :
    * -analyser les mots clés des groupes suivis par l'utilisateur
    * -(si groupes)selectionner tous les posts recoupés avec ces mots clés
    * -(sinon)selectionner tous les posts
    * -les trier par ordre descendant de date
    * -extraire dans un tableau à part les posts publiés il y a moins de 3 jours
    * -tronquer le premier résultat à 30 posts
    * -tronquer le deuxième résultat à 5 posts
    * -concaténer les deux tableaux avec celui des 5 plus récents en premier
    *-------------------------------------*/

    $userGroups = Group::whereIn('users_id', [$user->id])->get();
    $userKeywords = [];
    foreach ($userGroups as $group) {
        $gKeywords = $group->keywords;
        foreach ($gKeywords as $word) {
            $userKeywords[] = $word;
        }
    }

    $postsMainList;

    if ($userGroups->count() > 0) {
        $postsCrossWords = [];
        foreach($userKeywords as $uWord) {
            $postsHaveWord = Post::whereIn('keywords', [$uWord])->get();
            foreach($postsHaveWord as $post) {
                $postsCrossWords[] = $post;
            }
        }
        $postsMainList = $postsCrossWords;
    } else {
        $allPosts = Post::orderBy('created_at', 'asc')->get();
        foreach ($allPosts as $p) {
            $postsMainList[] = $p;
        }
    }

    $postsMainList = array_reverse(array_unique($postsMainList));//sorted by desc timestamp

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

    $postsMainList = (count($postsMainList) > self::MAIN_POSTS_FEED_LIMIT) ? array_slice($postsMainList, 0, self::MAIN_POSTS_FEED_LIMIT) : $postsMainList;
    $thisMonthPosts = (count($thisMonthPosts) > self::RECENT_POSTS_FEED_LIMIT) ? array_slice($thisMonthPosts, 0, self::RECENT_POSTS_FEED_LIMIT) : $thisMonthPosts;

    $result = array_merge($thisMonthPosts, $postsMainList);

    return $result;
  }

  public static function getGuestFeed() {

      /*-------------------------------------
      * ALGO - PSEUDO CODE
      * GuestFeed (guest user):
      * -selectionner tous les posts
      * -les trier par date descendante
      * -extraire les posts publiés il y a moins de 3 jours dans un tableau à part
      * -tronquer le premier tableau à {30?} posts et les classer par nb de votes positifs
      * -tronquer le deuxième tableau à {5?} posts
      * -concaténer les deux tableaux  avec celui des 5 plus récents en premier
      *-------------------------------------*/

      $allPosts = Post::orderBy('created_at', 'desc')->get();
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

      $postsMainList = (count($postsMainList) > self::MAIN_POSTS_FEED_LIMIT) ? array_slice($postsMainList, 0, self::MAIN_POSTS_FEED_LIMIT) : $postsMainList;
      $thisMonthPosts = (count($thisMonthPosts) > self::RECENT_POSTS_FEED_LIMIT) ? array_slice($thisMonthPosts, 0, self::RECENT_POSTS_FEED_LIMIT) : $thisMonthPosts;

      $result = array_merge($thisMonthPosts, $postsMainList);

      return $result;
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
}
