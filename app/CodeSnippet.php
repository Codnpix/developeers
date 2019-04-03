<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class CodeSnippet extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'developeers';
    public $timestamps = true;

    protected $fillable = [
        'content', 'language',
    ];
}
