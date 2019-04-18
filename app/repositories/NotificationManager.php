<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Post;
use App\Version;
use App\Comment;
use App\Notification;
use App\repositories\VersionManager;
use App\repositories\CommentManager;

class NotificationManager extends Model {

    public static function getNotifications(User $user) {
      $notifications = Notification::where('notified_user_id', $user->id)->get();
      return $notifications;
    }

    public static function broadcastOnPost(Post $post, User $notifying, Version $version, $type, $source, $originElementId) {
      $postUsers = [];
      $postUsers[] = User::find($post->author_id);

      $postVersions = VersionManager::getPostVersions($post);
      foreach ($postVersions as $v) {
        if(!in_array(User::find($v->author_id), $postUsers)) $postUsers[] = User::find($v->author_id);


        $coms = CommentManager::getComments($v);
        foreach ($coms as $c) {
          if(!in_array(User::find($c->author_id), $postUsers)) $postUsers[] = User::find($c->author_id);

        }
      }

      foreach ($postUsers as $u) {
        self::addNotification($u, $notifying, $post, $version, $type, $source, $originElementId);
      }
    }

    public static function broadcastOnVersion(User $notifying, Version $version, $type, $source, $originElementId) {
      $post = Post::find($version->post_id);
      $usersOnVersion = [];
      $usersOnVersion[] = User::find($version->author_id);

      $comments = CommentManager::getComments($version);
      foreach ($comments as $c) {
        if(!in_array(User::find($c->author_id), $usersOnVersion)) $usersOnVersion[] = User::find($c->author_id);

      }

      foreach ($usersOnVersion as $u) {
        self::addNotification($u, $notifying, $post, $version, $type, $source, $originElementId);
      }
    }

    public static function notifyPostAuthor(Post $post, User $notifying, $type, $source, $originElementId) {
      $notified = User::find($post->author_id);
      $postVersions = VersionManager::getPostVersions($post);
      $lastVersion = $postVersions[count($postVersions) - 1];
      self::addNotification($notified, $notifying, $post, $lastVersion, $type, $source, $originElementId);
    }

    public static function notifyVersionAuthor(User $notifying, Version $version, $type, $source, $originElementId) {
      $notified = User::find($version->author_id);
      $post = Post::find($version->post_id);
      self::addNotification($notified,$notifying,$post,$version,$type,$source, $originElementId);
    }

    public static function notifyCommentAuthor(User $notifying, Comment $comment, $type, $source, $originElementId) {
      $notified = User::find($comment->author_id);
      $version = Version::find($comment->version_id);
      $post = Post::find($version->post_id);
      self::addNotification($notified,$notifying,$post,$version,$type,$source, $originElementId);
    }

    private static function addNotification(User $notified,
                                          User $notifying,
                                          Post $post,
                                          Version $version,
                                          $type,
                                          $source,
                                          $originElementId) {

      //$type : expected String "comment" or "vote " or "version"
      //$source: expected String "comment" or "post" or "version"

      $message = "";

      if ($type == 'comment') {

       $message = $notifying->name .' a commenté la version '. $version->number .' de '. $post->title;

     } else if ($type == 'vote') {

       if ($source == 'post') {

         $message = $notifying->name .' a voté pour votre post '. $post->title;

       } else if ($source == 'version') {

         $message = $notifying->name .' a voté pour la version '. $version->number .' que vous avez proposée sur '. $post->title;

       } else if ($source == 'comment') {

         $message = $notifying->name .' a voté pour votre commentaire sur la version '. $version->number .' de '. $post->title;

       }
     } else if ($type == 'version') {

       $message = $notifying->name .' a créé une nouvelle version pour '. $post->title;
     }

     if (!$message == ""
     && $notified->id != $notifying->id) {
       $notif = new Notification();
       $notif->notified_user_id = $notified->id;
       $notif->message = $message;
       $notif->request_route_link = '/posts/'.$post->id.'/'.$version->id;
       $notif->version = array(
         "id"=>$version->id,
         "number"=>$version->number
       );
       $notif->post_id = $post->id;
       $notif->unread = true;
       $notif->origin_element_id = $originElementId;
       $notif->save();
     }
   }

   public static function clearNotifications(User $user, Post $post, Version $version) {
     $notifications = Notification::where('notified_user_id', $user->id)
                                  ->where('post_id', $post->id)
                                  ->where('version.id', $version->id)
                                  ->get();
     foreach($notifications as $n) {
       $n->unread = false;
       $n->save();
     }
   }

   public static function deleteElementRelatedNotifications($elementId) {

       /*
       * param $elementID  : Expected mongoDB Object _id String
       */

       $notifications = Notification::where('origin_element_id', $elementId)->get();

       foreach($notifications as $n) {
           $n->delete();
       }
   }

   public static function deleteObsoleteNotification(Notification $notif) {
       $notif->delete();
       return "Notification deleted";
   }

   public static function markNotificationRead(Notification $notif) {
       $notif->unread = false;
       $notif->save();
       return "Notification has been marked as read";
   }
}
