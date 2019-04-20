<?php

namespace App\repositories;

use App\Profile_pic;
use App\repositories\CommentManager;
use App\repositories\PostManager;
use App\repositories\VersionManager;
use Illuminate\Support\Facades\Storage;

use App\Comment;
use App\Post;
use App\Version;

use Illuminate\Database\Eloquent\Model;

class ProfilePicManager extends Model {

    public static function storePath($path, $userId) {
        self::deletePreviousProfilePics($userId);
        $url = 'http://localhost/developeers/storage/app/public/' .$path;
        $pic = new Profile_pic();
        $pic->image_path = $url;
        $pic->local_path = $path;
        $pic->user_id = $userId;
        $pic->save();
        //update picture pathes on user's comments, versions and posts
        $userComments = Comment::where('author_id', $userId)->get();
        foreach ($userComments as $c) {
            CommentManager::updateAuthorProfilePicUrl($c, $url);
        }
        $userVersions = Version::where('author_id', $userId)->get();
        foreach ($userVersions as $v) {
            VersionManager::updateAuthorProfilePicUrl($v, $url);
        }
        $userPosts = Post::where('author_id', $userId)->get();
        foreach ($userPosts as $c) {
            PostManager::updateAuthorProfilePicUrl($c, $url);
        }
        return "Image stored successfully !";
    }

    public static function getUserProfilePic($userId) {
        $pic = Profile_pic::where('user_id', $userId)->first();
        if(!$pic) {
            return "false";
        }
        $path = $pic->image_path;
        return $path;
    }

    private static function deletePreviousProfilePics($userId) {
        $pics = Profile_pic::where('user_id', $userId)->get();
        foreach ($pics as $pic) {
            $file = basename($pic->image_path);
            unlink(storage_path('app/public/'.$pic->local_path));
            $pic->delete();
        }
    }
}
