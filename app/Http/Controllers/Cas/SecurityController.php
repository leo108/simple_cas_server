<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 14:50
 */

namespace App\Http\Controllers\Cas;


use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Service;
use App\Http\Controllers\Controller;
use App\Services\Ticket;
use App\User;
use App\Exceptions\CAS\InvalidServiceException;

class SecurityController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    protected $username = 'name';

    public function __construct()
    {
        //$this->middleware($this->guestMiddleware(), ['except' => 'logoutAction']);
    }

    public function loginPageAction(Request $request)
    {
        $gateway = $request->get('gateway', '');
        $service = $request->get('service', '');
        $errors = [];
        if (!empty($service)) {
            //service not found in white list
            if (!Service::isUrlValid($service)) {
                $errors[] = 'invalid service';
            }
        }

        $user = \Auth::user();

        //user already has sso session
        if (empty($errors) && $user) {
            //must not be transparent
            if ($request->get('warn') === 'true' && !empty($service)) {
                $query = $request->query->all();
                unset($query['warn']);
                $url = route('cas_login_action', $query);

                return view('auth.login_warn', ['url' => $url]);
            }


            return $this->authenticated($request, $user);
        }

        //user not login but set gateway, redirect to $service directly
        if (!$user && $gateway === 'true' && !empty($service)) {
            return redirect($service);
        }

        return view('auth.login', ['origin_req' => $request->query->all()])->withErrors(['global' => $errors]);

    }

    protected function authenticated(Request $request, User $user)
    {
        $serviceUrl = $request->get('service', '');
        if (!empty($serviceUrl)) {
            $query    = parse_url($serviceUrl, PHP_URL_QUERY);
            $ticket   = Ticket::applyTicket($user, $serviceUrl);
            $finalUrl = $serviceUrl.($query ? '&' : '?').'ticket='.$ticket->ticket;
            $resp     = redirect($finalUrl);
        } else {
            $resp = redirect()->route('home');
        }

        return $resp;
    }

    public function logoutAction()
    {
        return new Response('123');
    }
}