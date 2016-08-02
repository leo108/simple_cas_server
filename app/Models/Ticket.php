<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 14:53
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public $timestamps = false;
    protected $fillable = ['ticket', 'service_url', 'expire_at', 'user_id', 'service_id', 'created_at'];

    public function isExpired()
    {
        return (new Carbon($this->expire_at))->getTimestamp() < time();
    }

    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
