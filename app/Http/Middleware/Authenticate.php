<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            // cek api token
            $api_token = $request->header('api-token');
            if ($api_token === null) {
                $response = [
                    'status' => 401,
                    'message' => "api token is required!",
                    'api_token' => $api_token
                ];
                return response($response, 401);
            } else if ($api_token != null) {
                $response = [
                    'status' => 401,
                    'message' => "Unauthorized",
                    'api_token' => $api_token
                ];
                return response($response, 401);
            } else {
                $response = [
                    'status' => 401,
                    'message' => "Unauthorized",
                ];
                return response($response, 401);
            }
        }

        return $next($request);
    }
}
