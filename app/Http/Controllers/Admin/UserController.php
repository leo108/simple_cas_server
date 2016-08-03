<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/3
 * Time: 11:32
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Response\JsonResponse;
use App\Services\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function listAction(Request $request)
    {
        $page    = $request->get('page', 1);
        $limit   = 20;
        $search  = $request->get('search', '');
        $enabled = $request->get('enabled', null);
        if ($enabled === '') {
            $enabled = null;
        }
        $users = User::getList($search, $enabled, null, $page, $limit);

        return view(
            'admin.user',
            [
                'users' => $users,
                'query' => [
                    'search'  => $search,
                    'enabled' => is_null($enabled) ? '' : $enabled,
                ],
            ]
        );
    }

    public function saveAction(Request $request)
    {
        $id       = $request->get('id', 0);
        $name     = $request->get('name', '');
        $realName = $request->get('real_name', '');
        $password = $request->get('password', '');
        $email    = $request->get('email', '');
        $enabled  = $request->get('enabled', false);
        $admin    = $request->get('admin', false);
        $user     = User::createOrUpdate($name, $realName, $password, $email, $admin, $enabled, $id);

        return JsonResponse::success($user, trans($id > 0 ? 'admin.user.edit_ok' : 'admin.user.add_ok'));
    }
}
