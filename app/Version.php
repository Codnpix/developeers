<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Version extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'developeers';
    public $timestamps = true;

    protected $fillable = [
        'author_id', 'snippets_id', 'text_content', 'comments_id', 'votes', 'created_at', 'updated_at'
    ];
}
