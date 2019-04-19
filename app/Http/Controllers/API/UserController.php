<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Notification;
use App\repositories\NotificationManager;
use App\Http\Requests\Profile_pic_request as PicRequest;
use App\repositories\ProfilePicManager;
use Illuminate\Support\Facades\Storage;

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

    public function markNotificationRead($notifId) {
        $notif = Notification::find($notifId);
        $response = NotificationManager::markNotificationRead($notif);
        return $response;
    }

    public function uploadProfilePic(PicRequest $request) {
        $path = $request->file('file')->store(config('images.path'), 'public');
        //$url = Storage::url($path);
        $userId = auth()->user()->id;
        $response = ProfilePicManager::storePath($path, $userId);
        return $response;
    }

    public function getProfilePic() {
        $user = auth()->user();
        $response = ProfilePicManager::getUserProfilePic($user->id);
        return $response;
    }

    public function getAnUserProfilePic(User $user) {
        $response = ProfilePicManager::getUserProfilePic($user->id);
        return $response;
    }
}
