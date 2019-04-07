<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Post;
use App\Version;
use App\Notification;

class NotificationManager extends Model {

    public static function getNotifications(User $user) {

      //retourner l'ensemble de documents notifications rattaché à cet $user
    }

    public static function addNotification(User $notified,
                                          User $notifying,
                                          Post $post,
                                          Version $version,
                                          $type,
                                          $source) {

      //$type : expected String "comment" or "vote " or "version"
      //$source: expected String "comment" or "post" or "version"

      $message = "";

      if ($type == 'comment') {

       $message = $notifying->name .'a commenté la version'. $version->number .'de'. $post->title;

     } else if ($type == 'vote') {

       if ($source == 'post') {

         $message = $notifying->name .'a voté pour votre post'. $post->title;

       } else if ($source == 'version') {

         $message = $notifying->name .'a voté pour la version'. $version->number .'que vous avez proposé sur'. $post->title;

       } else if ($source == 'comment') {

         $message = $notifying->name .'a voté pour votre commentaire sur la version'. $version->number .'de'. $post->title;

       }
     } else if ($type == 'version') {

       $message = $notifying->name .'a créé une nouvelle version pour'. $post->title;
     }

     if (!$message == "") {
       $notif = new Notification();
       $notif->notified_user_id = $notified->id;
       $notif->message = $message;
       $notif->request_route_link = '/posts/'.$post->id.'/'.$version->id;
       $notif->post_id = $post->id;
       $notif->save();
     }
   }
}
