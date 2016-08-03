<?php

namespace App\Http\Middleware;

use App\Response\JsonResponse;
use Closure;
use Illuminate\Contracts\Auth\StatefulGuard;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $guardIns = \Auth::guard($guard);

        if ($guardIns->guest()) {
            return $this->error($request);
        }

        if (isset($guardIns->user()->enabled) && !$guardIns->user()->enabled) {
            if ($guardIns instanceof StatefulGuard) {
                $guardIns->logout();
            }

            return $this->error($request);
        }

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    private function error($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return JsonResponse::error(trans('auth.need_login'));
        }

        return redirect()->route('cas_login_page');
    }
}
