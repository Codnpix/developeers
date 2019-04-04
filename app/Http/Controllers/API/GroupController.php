<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Group;
use App\Keyword;

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
      $group->votes = [];
      $group->users_id = [$initUser_id];
      $group->save();

      return "Group created successfully";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group) {
        return $group;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group) {

        $group->name = $request->name;
        $group->description = $request->description;
        $group->keywords = $request->keywords;
        $group->save();

        return "Group updated successfully";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group) {

        $group->delete();

        return "Group deleted successfully";
    }
}
