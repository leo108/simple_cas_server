<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 15:06
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];

    public function hosts()
    {
        return $this->hasMany('App\Models\ServiceHost');
    }
}