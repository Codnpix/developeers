<?php

namespace App\repositories;

use App\Profile_pic;

use Illuminate\Database\Eloquent\Model;

class ProfilePicManager extends Model {

    public static function storePath($path, $userId) {
        self::deletePreviousProfilePics($userId);
        $pic = new Profile_pic();
        $pic->image_path = $path;
        $pic->user_id = $userId;
        $pic->save();
        return "Image stored successfully !";
    }

    public static function getUserProfilePic($userId) {
        $pic = Profile_pic::where('user_id', $userId)->first();
        $path = $pic->image_path;

        return $path;
    }

    private static function deletePreviousProfilePics($userId) {
        $pics = Profile_pic::where('user_id', $userId)->get();
        foreach ($pics as $pic) {
            $pic->delete();
        }
    }
}
