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
    $groups = Group::whereIn('users_id', [1])->get();
    return $groups;
  }

  public static function store(Request $request) {

    // if (Auth::check()) {
    //
    //   $initUser_id = Auth::id();//celui qui crée le groupe est le premier user ajouté au groupe.
    //
    //   $group = new Group();
    //
    //   $group->name = $request->name;
    //   $group->description = $request->description;
    //   $group->keywords = $requests->keywords;
    //   $group->votes = [];
    //   $group->users_id = [$initUser_id];
    //   $group->save();
    // }

    $initUser_id = 1;

    $group = new Group();

    $group->name = $request->name;
    $group->description = $request->description;
    $group->keywords = $request->keywords;
    //$group->votes = [];
    $group->users_id = [$initUser_id];
    $group->save();
  }

  public static function updateGroup(Request $request, Group $group) {

    $group->name = $request->name;
    $group->description = $request->description;
    $group->keywords = $request->keywords;
    $group->save();
  }

  public static function joinGroup(Group $group, User $user) {

    if (!array_search($user->id, $group->users_id)) {

      $groupUsers = $group->users_id;
      $groupUsers[] = $user->id;
      $group->users_id = $groupUsers;
      $group->save();

    } else return false;
  }

  public static function leaveGroup(Group $group, User $user) {

    $key = array_search($user->id, $group->users_id);

    if ($key) {

      $groupUsers = $group->users_id;
      array_splice($groupUsers, $key ,1);
      $group->users_id = $groupUsers;
      $group->save();

    } else return false;
  }
}
