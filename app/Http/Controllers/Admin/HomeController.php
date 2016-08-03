<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/3
 * Time: 11:28
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\User;

class HomeController extends Controller
{
    public function indexAction()
    {
        return view(
            'admin.dashboard',
            [
                'user' => User::dashboard(),
            ]
        );
    }
}
