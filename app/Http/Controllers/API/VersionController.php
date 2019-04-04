<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Version;
use App\Post;
use App\Http\Controllers\API\CodeSnippetController;


class VersionController extends Controller {

    public static getPostVersions($post_id) {

      $versions = Version::where('post_id', $post_id)->get();

      return $versions;
    }
}
