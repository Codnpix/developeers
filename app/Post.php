<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Post extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'posts';
    public $timestamps = true;

    protected $fillable = [
      'title',
      'author_id',
      'author_name',
      'group_id',
      'group_name',
      'group',
      'keywords',
      'votes',
      'created_at',
      'updated_at'
    ];
}
