<?php


namespace Cly\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Deny
{
    public function handle(Request $request, Closure $next)
    {
        $denyIpStr = env('DENY_IP', '');
        $denyIps = explode(',', $denyIpStr);

        if (in_array($request->getClientIp(), $denyIps)) {
            throw new HttpException(403, 'deny');
        }

        $denyAgentReg = env('DENY_AGENT_REG', '');
        if ($denyAgentReg && preg_match($denyAgentReg, $request->userAgent())) {
            throw new HttpException(403, 'deny');
        }

        return $next($request);
    }
}
