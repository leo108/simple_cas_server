<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/3
 * Time: 23:23
 */

namespace App\UserProvider;


use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class CasUserProvider extends EloquentUserProvider
{
    public function validateCredentials(UserContract $user, array $credentials)
    {
        if (isset($user->enabled) && !$user->enabled) {
            return false;
        }

        return parent::validateCredentials($user, $credentials);
    }
}
