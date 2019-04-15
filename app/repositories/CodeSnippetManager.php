<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use App\CodeSnippet;
use App\Version;

class CodeSnippetManager extends Model {

  public static function storeSnippet($snippet, $version_id) {
    $codeSnippet = new CodeSnippet();
    $codeSnippet->content = $snippet;
    $codeSnippet->version_id = $version_id;
    $codeSnippet->save();
  }

  public static function getVersionSnippets(Version $version) {
    $snippets = CodeSnippet::where('version_id', $version->id)->get();
    return $snippets;
  }

  public static function updateSnippets(Version $version, $snippets) {
    $versionCurrentSnippets = CodeSnippet::where('version_id', $version->id)->get();
    for($i = 0; $i < count($snippets); $i++) {
      $versionCurrentSnippets[$i]->content = $snippets[$i];
      $versionCurrentSnippets[$i]->version_id = $version->id;
      $versionCurrentSnippets[$i]->save();
    }
  }
}
