<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile_pic extends Model {

    protected $fillable = [
        'user_id', 'image_path'
    ];
}
