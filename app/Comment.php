<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Comment extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'comments';
    public $timestamps = true;

    protected $fillable = [
        'author_id', 'author_name', 'content', 'version_id', 'votes', 'created_at', 'updated_at'
    ];
}
