<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class CodeSnippet extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'code_snippets';
    public $timestamps = true;

    protected $fillable = [
        'content', 'language', 'version_id'
    ];
}
