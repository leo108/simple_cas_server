<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 15:17
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ServiceHost extends Model
{
    protected $fillable = ['host', 'service_id'];

    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }
}