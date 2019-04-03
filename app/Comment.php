<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Comment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'developeers';
    public $timestamps = true;

    protected $fillable = [
        'author_id', 'content', 'votes', 'created_at', 'updated_at'
    ];
}
