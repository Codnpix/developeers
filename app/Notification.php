<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Notification extends Eloquent {

  protected $connection = 'mongodb';
  protected $collection = 'notifications';
  public $timestamps = true;

  protected $fillable = [
      'notified_user_id',
      'message',
      'request_route_link',
      'post_id',
      'unread',
      'origin_element_id',
      'created_at',
      'updated_at'
  ];
}
