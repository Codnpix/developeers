<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Keyword extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'developeers';
    public $timestamps = true;

    protected $fillable = [
        'word',
    ];
}
