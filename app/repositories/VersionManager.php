<?php

namespace App\repositories;

use Illuminate\Database\Eloquent\Model;
use App\Version;

class VersionManager extends Model {

    public static function getVersion(Version $version) {
      return $version;
    }
}
