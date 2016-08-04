<?php

namespace App\Http\Middleware;

use App\Response\JsonResponse;
use Closure;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::user() && \Auth::user()->admin) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return JsonResponse::error(trans('auth.need_login'));
        }

        return redirect()->route('cas_login_page');
    }
}
