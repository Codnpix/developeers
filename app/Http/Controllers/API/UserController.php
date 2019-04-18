<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Notification;
use App\repositories\NotificationManager;

class UserController extends Controller {

    public function getNotifications() {
        $user = auth()->user();
        $notifs = NotificationManager::getNotifications($user);
        return $notifs;
    }

    public function deleteObsoleteNotification($notifId) {

        /*
        * if the request_route_link of a Notification
        * returns a 404 error, we will call this method.
        */
        $notif = Notification::find($notifId);
        $response = NotificationManager::deleteObsoleteNotification($notif);
        return $response;
    }
}
