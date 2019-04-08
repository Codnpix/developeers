<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Version extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'versions';
    public $timestamps = true;

    protected $fillable = [
        'number', 'author_id', 'author_name', 'post_id', 'text_content', 'votes', 'created_at', 'updated_at'
    ];
}
