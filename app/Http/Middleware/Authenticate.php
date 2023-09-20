<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Auth\Factory as Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
        try {
            $jwtToken = $request->header('authorization');

            if ($jwtToken == null) {
                $response = [
                    'statusCode' => 401,
                    'status' => 'failed',
                    'message' => ["authorization" => ["authorization token is required"]],
                    'data' => []
                ];
        
                return response(json_encode($response),401);
            }

            JWT::decode($jwtToken, new Key(env('JWT_SECRET'),'HS256'));
        } catch (Exception $th) {
            $response = [
                'statusCode' => 401,
                'status' => 'failed',
                'message' => ["authorization" => ["authorization token is invalid"]],
                'data' => []
            ];
    
            return response(json_encode($response),401);
        }


        // if ($this->auth->guard($guard)->guest()) {
        //     return response('Unauthorized.', 401);
        // }

        return $next($request);
    }
}
