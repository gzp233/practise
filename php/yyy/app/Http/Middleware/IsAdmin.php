<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Exceptions\IsNotAdminException;

class IsAdmin extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @throws \App\Exceptions\IsNotAdminException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->user()->is_admin) {
            throw new IsNotAdminException('未授权，非管理员账号无法请求');
        }

        return $next($request);
    }
}
