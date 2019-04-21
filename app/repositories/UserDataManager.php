<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\User;
use App\UserData;
use App\repositories\ProfilePicManager;

class UserDataManager extends Model {


    public static function getUserData(User $user) {
        //Route::get()
        $udata = UserData::where('user_id', $user->id)->first();
        return $udata;
    }

    public static function storeInitUserData(User $user) {
        //called by UserController::registerUser
        //Route::post()
        $udata = new UserData();
        $udata->user_id = $user->id;
        $udata->user_name = $user->name;
        $pic = ProfilePicManager::getUserProfilePic($user->id);
        $udata->profile_pic_url = $pic->url;
        $udata->user_links = [];
        $udata->followers = [];
        $udata->user_presentation = '';
        $udata->user_interests = [];
        $udata->save();
        return "UserData object document initialized";
    }

    public static function updateUserData(Request $req, User $user) {
        //Route::put()
        $udata = UserData::where('user_id', $user->id)->first();

        if (!$udata) {//temporaire, pour pas devoir recréer tous les user d'avant qui ont pas leur userData
            $udata = new UserData();
            $udata->user_id = $user->id;
            $udata->user_name = $user->name;
        }

        $udata->user_links = $req->user_links;
        $udata->user_presentation = $req->user_presentation;
        $udata->user_interests = $req->user_interests;
        $udata->save();
        return "UserData updated successfully";
    }

    public static function followUser(User $follower, User $user) {
        //Route::put()
        $udata = UserData::where('user_id', $user->id)->first();
        $flws = $udata->followers;
        $key = array_search(array(
                            'id' => $follower->id,
                            'name' => $follower->name
                            ), $flws);
        if ($key) {
            return "You are already following that user";
        } else {
            $flws[] = array(
                        'id' => $follower->id,
                        'name' => $follower->name
                    );
            $udata->followers = $flws;
            $udata->save();
        }

    }

    public static function unfollowUser(User $unfollower, User $user) {
        //Route::put()
        $udata = UserData::where('user_id', $user->id)->first();
        $flws = $udata->followers;
        $key = array_search(array(
                            'id' => $unfollower->id,
                            'name' => $unfollower->name
                            ), $flws);
        if ($key) {
            array_splice($flws, $key, 1);
            $udata->followers = $flws;
            $udata->save();
            return "You will be no longer following that user";
        } else {
            return "You are not following that user";
        }
    }
}
