<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 7:40 PM
 */
namespace Tw\Server\Middleware;
use Vinkla\Hashids\Facades\Hashids;
class HashIdsmid {
    /**
     * @param $request
     * @param \Closure $next
     */
    public function handle($request, \Closure $next)
    {
        if ($request->query) {
            foreach ($request->query as $key =>$value) {
                $request->offsetSet($key,Hashids::decodeHex($value)?:$value);
            }
        }
        $parameters = $request->route()->parameters;
        if (!empty($parameters)) {
            foreach ($parameters as $key => $value) {
                $request->route()->parameters[$key] = Hashids::decodeHex($value)?:$value;
            }
        }
        return $next($request);
    }


}