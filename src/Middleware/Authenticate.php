<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 7:40 PM
 */
namespace Tw\Server\Middleware;
use Illuminate\Support\Facades\Auth;
class Authenticate {
    /**
     * @param $request
     * @param \Closure $next
     */
    public function handle($request, \Closure $next)
    {
        $redirectTo = tw_base_path(config('tw.auth.redirect_to', 'login'));
        if (Auth::guard('tw')->guest() && !$this->shouldPassThrough($request)) {
            return redirect()->guest($redirectTo);
        }
        return $next($request);
    }

    /**
     * @des login logout 应该通过 返回true 否则 false
     * @param $request
     * @return bool
     */
    public function shouldPassThrough($request)
    {
        $excepts = config('tw.auth.excepts', [
            'login',
            'logout',
        ]);
        return collect($excepts)
            ->map('tw_base_path')
            ->contains(function ($except) use ($request) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }
                return $request->is($except);
            });
    }
}