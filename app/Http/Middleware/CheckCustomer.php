<?php

namespace App\Http\Middleware;

use Closure;

class CheckCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::guard('customer')->check()) {
            $model = \Auth::guard('customer')->user();
            // var_dump($model);die();
            if ($model instanceof Facilitator) {
                return $next($request);
            } else {
                abort(403, "Access Denied.");
            }
        }
        abort(401, 'Not authenticated');
    }
}
