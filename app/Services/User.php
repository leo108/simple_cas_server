<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 14:57
 */

namespace App\Services;

use App\User as Model;

class User
{
    /**
     * @param $id
     * @return \App\User
     */
    public static function getUserById($id)
    {
        return Model::find($id);
    }

    /**
     * @param $name
     * @return \App\User
     */
    public static function getUserByName($name)
    {
        return Model::where('name', $name)->first();
    }

    public static function create($name, $realName, $password, $email, $isAdmin = false, $enabled = true)
    {
        if (static::getUserByName($name)) {
            throw new \RuntimeException('Username duplicated'); //todo change exception class
        }

        return Model::create(
            [
                'name'      => $name,
                'real_name' => $realName,
                'password'  => bcrypt($password),
                'email'     => $email,
                'enabled'   => boolval($enabled),
                'admin'     => boolval($isAdmin),
            ]
        );
    }
}
