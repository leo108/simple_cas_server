<?php

namespace App\Http\Controllers;

use App\Response\JsonResponse;
use App\Services\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAction()
    {
        return view('home');
    }

    public function changePwdAction(Request $request)
    {
        $rule = User::getPasswordRule(true);
        $this->validate($request, ['new' => $rule], [], ['new' => trans('auth.new_pwd')]);

        $old  = $request->get('old');
        $new  = $request->get('new');
        $user = \Auth::user();
        if (!\Hash::check($old, $user->password)) {
            return JsonResponse::error(trans('message.invalid_old_pwd'));
        }

        User::resetPassword($user->id, $new);

        return JsonResponse::success([], trans('message.change_pwd_ok'));
    }
}
