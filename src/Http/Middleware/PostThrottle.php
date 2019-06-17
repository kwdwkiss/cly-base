<?php

namespace Cly\Http\Middleware;

use Closure;
use Modules\Common\Exceptions\MsgException;

class PostThrottle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //防止cdn重复提交数据
        if ($request->isJson() && $request->method() == 'POST') {
            $uses = \Route::current()->action['uses'];
            if (is_string($uses)) {
                $sessionId = session()->getId();
                $key = "$uses:$sessionId";
                $ttl = 1;
                $lock = app('redis')->executeRaw(['set', $key, 1, 'nx', 'ex', $ttl]);
                if (!$lock) {
                    //logger('PostThrottle', [$uses, $sessionId]);
                    throw new MsgException("请求过于频繁,请{$ttl}秒稍后再试");
                }
            }
        }
        return $next($request);
    }
}
