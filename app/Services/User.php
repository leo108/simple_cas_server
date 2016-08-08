<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 14:57
 */

namespace App\Services;

use App\Exceptions\UserException;
use App\User as Model;
use App\Utils\SimpleValidator;
use Illuminate\Support\Str;

class User
{
    /**
     * @param $id
     * @return \App\User|null
     */
    public static function getUserById($id)
    {
        return Model::find($id);
    }

    /**
     * @param $name
     * @return \App\User|null
     */
    public static function getUserByName($name)
    {
        return Model::where('name', $name)->first();
    }

    /**
     * @param $email
     * @return \App\User|null
     */
    public static function getUserByEmail($email)
    {
        return Model::where('email', $email)->first();
    }

    /**
     * @param string $name
     * @param string $realName
     * @param string $password
     * @param string $email
     * @param bool   $isAdmin
     * @param bool   $enabled
     * @param int    $id
     * @return \App\User
     */
    public static function createOrUpdate(
        $name,
        $realName,
        $password,
        $email,
        $isAdmin = false,
        $enabled = true,
        $id = 0
    ) {
        $data = [
            'real_name' => $realName,
            'email'     => $email,
            'enabled'   => boolval($enabled),
            'admin'     => boolval($isAdmin),
        ];

        SimpleValidator::validate(
            $data,
            [
                'real_name' => 'required',
                'email'     => 'required|email',
            ],
            [
                'real_name' => trans('admin.user.real_name'),
                'email'     => trans('admin.user.email'),
            ]
        );

        if ($id <= 0) {
            if (static::getUserByName($name)) {
                throw new UserException(trans('message.user.name_duplicated'));
            }

            if (static::getUserByEmail($email)) {
                throw new UserException(trans('message.user.email_duplicated'));
            }

            $data['name']     = $name;
            $data['password'] = $password;

            SimpleValidator::validate(
                $data,
                [
                    'name'     => 'required',
                    'password' => self::getPasswordRule(true),
                ],
                [
                    'name'     => trans('admin.user.username'),
                    'password' => trans('admin.user.password'),
                ]
            );
            $data['password'] = bcrypt($password);

            return Model::create($data);
        }

        if (!empty($password)) {
            $data['password'] = $password;
            SimpleValidator::validate(
                $data,
                [
                    'password' => self::getPasswordRule(true),
                ],
                [
                    'password' => trans('admin.user.password'),
                ]
            );
            $data['password'] = bcrypt($password);
        }

        Model::find($id)->update($data);

        return Model::find($id);
    }

    /**
     * @param int    $id
     * @param string $pwd
     * @return \App\User
     */
    public static function resetPassword($id, $pwd)
    {
        $user = Model::find($id);
        if (!$user) {
            throw new UserException(trans('messages.user.not_exists'));
        }
        $user->password       = bcrypt($pwd);
        $user->remember_token = Str::random(60);
        $user->save();

        return $user;
    }

    /**
     * @param $search
     * @param $enabled
     * @param $admin
     * @param $page
     * @param $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getList($search, $enabled, $admin, $page, $limit)
    {
        /* @var \Illuminate\Database\Query\Builder $query */
        $query = Model::getQuery();
        if ($search) {
            $like = '%'.$search.'%';
            $query->where(
                function ($query) use ($like) {
                    /* @var \Illuminate\Database\Query\Builder $query */
                    $query->where('name', 'like', $like)
                        ->orWhere('real_name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                }
            );
        }

        if (!is_null($enabled)) {
            $query->where('enabled', boolval($enabled));
        }

        if (!is_null($admin)) {
            $query->where('admin', boolval($admin));
        }

        return $query->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);
    }

    public static function dashboard()
    {
        return [
            'total'  => Model::count(),
            'active' => Model::where('enabled', true)->count(),
            'admin'  => Model::where('admin', true)->count(),
        ];
    }

    public static function getPasswordRule($returnStr = true)
    {
        $rule = [
            'required',
            'min:6',
        ];
        if ($returnStr) {
            return join('|', $rule);
        }

        return $rule;
    }
}
