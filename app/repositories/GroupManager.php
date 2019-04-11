<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Version;
use App\Post;
use App\Group;
use App\User;

class GroupManager extends Model {

  public static function getUserGroups(User $user) {
    $groups = Group::whereIn('users_id', [$user->id])->get();
    return $groups;
  }

  public static function searchGroups($words) {

    $searchWords = explode(" ", strtolower($words));

    $allGroups = Group::all();
    $inKeywordsGroups = [];

    foreach ($allGroups as $g) {

      $groupKeywords = $g->keywords;//check keywords
      $nameWords = explode(" ", strtolower($g->name));//check title words

      foreach ($searchWords as $sw) {
        //search in keywords
        if (in_array($sw, $groupKeywords)) $inKeywordsGroups[] = $g;
        //search in title
        if (in_array($sw, $nameWords)) $inKeywordsGroups[] = $g;
      }
    }

    return array_unique($inKeywordsGroups);
  }

  public static function store(Request $request, User $user) {

    $group = new Group();

    $group->name = $request->name;
    $group->description = $request->description;
    $group->keywords = $request->keywords;
    $group->users_id = [$user->id];
    $group->users = [[$user->id => $user->name]];
    $group->save();

    return $group;
  }

  public static function updateGroup(Request $request, Group $group) {

    $group->name = $request->name;
    $group->description = $request->description;
    $group->keywords = $request->keywords;
    $group->save();
  }

  public static function joinGroup(Group $group, User $user) {

    if (!in_array($user->id, $group->users_id)) {

      $groupUsersId = $group->users_id;
      $groupUsersId[] = $user->id;
      $group->users_id = $groupUsersId;

      $groupUsers = $group->users;
      $groupUsers[] = [$user->id => User::find($user->id)->name];
      $group->users = $groupUsers;

      $group->save();

    } else return false;
  }

  public static function leaveGroup(Group $group, User $user) {

    $key = array_search($user->id, $group->users_id);
    $key2 = array_search([$user->id => $user->name], $group->users);

    if (($key == true || $key == 0) && ($key2 == true || $key2 == 0)) {

      $groupUsersId = $group->users_id;
      array_splice($groupUsersId, $key ,1);
      $group->users_id = $groupUsersId;

      $groupUsers = $group->users;
      array_splice($groupUsers, $key2, 1);
      $group->users = $groupUsers;

      $group->save();

    } else return false;
  }
}
