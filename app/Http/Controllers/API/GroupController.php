<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Group;
use App\Keyword;
use App\User;
use App\repositories\GroupManager;

class GroupController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return Group::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
      GroupManager::store($request);
      return "Group created successfully";
    }

    /**
     * Display the specified resource.
     *
     * @param  Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group) {
        return $group;
    }

    public function showUserGroups(User $user) {
      $groups = GroupManager::getUserGroups($user);
      return $groups;
    }

    public function searchGroups(Request $request, $words) {
      $groups = GroupManager::searchGroups($words);
      return $groups;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group) {
        GroupManager::updateGroup($request, $group);
        return "Group updated successfully";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Group $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group) {
        $group->delete();
        return "Group deleted successfully";
    }

    public function joinGroup(Group $group, User $user) {
      GroupManager::joinGroup($group, $user);
      return "User successfully joined the group!";
    }

    public function leaveGroup(Group $group, User $user) {
      $test = GroupManager::leaveGroup($group, $user);
      return "User Successfully leaved the group.";
    }
}
