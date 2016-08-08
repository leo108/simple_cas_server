<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/7
 * Time: 14:16
 */

namespace App\Http\Controllers;


use App\Services\User;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends Controller
{
    protected $subject = '';

    protected $redirectPath = '/';

    public function __construct()
    {
        $this->subject      = trans('passwords.email_subject');
        $this->redirectPath = route('home');
    }

    protected function getResetValidationRules()
    {
        $rule             = $this->originGetResetValidationRules();
        $pwdRule          = User::getPasswordRule(false);
        $pwdRule[]        = 'confirmed';
        $rule['password'] = join('|', array_unique($pwdRule));

        return $rule;
    }

    protected function getResetValidationCustomAttributes()
    {
        return [
            'password' => trans('auth.new_pwd'),
            'email'    => trans('passwords.email'),
        ];
    }

    use ResetsPasswords {
        getResetValidationRules as originGetResetValidationRules;
    }
}
