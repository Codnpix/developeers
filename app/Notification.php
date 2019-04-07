<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model {

  protected $connection = 'mongodb';
  protected $collection = 'notifications';
  public $timestamps = true;

  protected $fillable = [
      'notified_user_id',
      'message',
      'request_route_link',
      'post_id',
      'created_at',
      'updated_at'
  ];
}
