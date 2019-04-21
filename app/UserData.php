<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Model;

class UserData extends Model {

    protected $connection = 'mongodb';
    protected $collection = 'user_data';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_links',
        'followers',
        'following',
        'user_presentation',
        'user_interests',
        'created_at',
        'updated_at'
    ];
}
