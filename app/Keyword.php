<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Keyword extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'keywords';
    public $timestamps = true;

    protected $fillable = [
        'word',
    ];
}
