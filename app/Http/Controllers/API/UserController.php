<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\repositories\NotificationManager;

class UserController extends Controller {

    public function getNotifications() {
        $user = auth()->user();
        $notifs = NotificationManager::getNotifications($user);
        return $notifs;
    }
}
