<?php

namespace Cly\Http\Middleware;

use Closure;
use League\OAuth2\Server\ResourceServer;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class CheckClientCredentials
{
    /**
     * The Resource Server instance.
     *
     * @var \League\OAuth2\Server\ResourceServer
     */
    private $server;

    /**
     * Create a new middleware instance.
     *
     * @param  \League\OAuth2\Server\ResourceServer $server
     * @return void
     */
    public function __construct(ResourceServer $server)
    {
        $this->server = $server;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  mixed ...$scopes
     * @return mixed
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        if (env('CLY_CLIENT_KEY')) {
            if ($request->input('cly_client_key') == env('CLY_CLIENT_KEY')) {
                return $next($request);
            }
        }

        $psr = (new DiactorosFactory)->createRequest($request);

        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);
        } catch (OAuthServerException $e) {
            throw new AuthenticationException;
        }

        app()->instance('passport.psr', $psr);

        $this->validateScopes($psr, $scopes);

        return $next($request);
    }

    /**
     * Validate the scopes on the incoming request.
     *
     * @param  \Psr\Http\Message\ResponseInterface $psr
     * @param  array $scopes
     * @return void
     * @throws \Laravel\Passport\Exceptions\MissingScopeException
     */
    protected function validateScopes($psr, $scopes)
    {
        if (in_array('*', $tokenScopes = $psr->getAttribute('oauth_scopes'))) {
            return;
        }

        foreach ($scopes as $scope) {
            if (!in_array($scope, $tokenScopes)) {
                throw new MissingScopeException($scope);
            }
        }
    }
}
