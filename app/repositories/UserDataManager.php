<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\User;
use App\UserData;

class UserDataManager extends Model {


    public static function getUserData(User $user) {
        //Route::get()
        $udata = UserData::where('user_id', $user->id)->first();
        return $udata;
    }

    public static function getUsersFollowedBy(User $user) {
        $followedUsers = UserData::where('user_id', $user->id)->first()->following;
        return $followedUsers;
    }

    public static function storeInitUserData(User $user) {
        //called by UserController::registerUser
        //Route::post()
        $udata = new UserData();
        $udata->user_id = $user->id;
        $udata->user_name = $user->name;
        $udata->user_links = [];
        $udata->followers = [array(
            'id'=> 0,
            'name'=>'bug fixing example'
        )];
        $udata->following = [array(
            'id'=> 0,
            'name'=>'bug fixing example'
        )];
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
            $udata->followers = [array(
                'id'=> 0,
                'name'=>'bug fixing example'
            )];
            $udata->following = [array(
                'id'=> 0,
                'name'=>'bug fixing example'
            )];
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
        if (gettype($key)=='integer') {
            return "You are already following at ".$user->name;
        } else {
            $flws[] = array(
                        'id' => $follower->id,
                        'name' => $follower->name
                    );
            $udata->followers = $flws;
            $udata->save();

            //update la propriété following du UserData de $user
            $authUserData = UserData::where('user_id', $follower->id)->first();
            $authFollowing = $authUserData->following;
            $authFollowing[] = array(
                'id' => $user->id,
                'name' => $user->name
            );
            $authUserData->following = $authFollowing;
            $authUserData->save();
            return "You are now following at ".$user->name;
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
        if (gettype($key)=='integer') {
            array_splice($flws, $key, 1);
            $udata->followers = $flws;
            $udata->save();

            //update la propriété following du UserData de $user
            $authUserData = UserData::where('user_id', $unfollower->id)->first();
            $authFollowing = $authUserData->following;
            $fkey = array_search(array(
                                'id' => $user->id,
                                'name' => $user->name
                                ), $authFollowing);
            if(gettype($fkey)=='integer') {
                array_splice($authFollowing, $fkey, 1);
            }
            $authUserData->following = $authFollowing;
            $authUserData->save();

            return "You will be no longer following at ".$user->name;
        } else {
            return "You are not following at ".$user->name;
        }
    }
}
